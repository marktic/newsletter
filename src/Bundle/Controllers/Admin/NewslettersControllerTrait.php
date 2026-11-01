<?php

declare(strict_types=1);

namespace Marktic\Newsletter\Bundle\Controllers\Admin;

use Marktic\Newsletter\Base\Exceptions\NewsletterException;
use Marktic\Newsletter\Bundle\Controllers\Base\Behaviours\HasNewsletterOwnerTrait;
use Marktic\Newsletter\NewsletterFilters\Actions\CreateNewsletterFilter;
use Marktic\Newsletter\NewsletterFilters\Actions\DeleteNewsletterFilter;
use Marktic\Newsletter\NewsletterFilters\Actions\FindFiltersForNewsletter;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypes;
use Marktic\Newsletter\NewsletterOwners\Dto\NewsletterOwner;
use Marktic\Newsletter\Newsletters\Actions\Preview\RenderNewsletterPreview;
use Marktic\Newsletter\Newsletters\Actions\Preview\SendNewsletterPreviewEmail;
use Marktic\Newsletter\Utility\NewsletterModels;
use Marktic\Newsletter\Utility\PackageConfig;
use Nip\Controllers\Response\ResponsePayload;

/**
 * @method ResponsePayload payload()
 */
trait NewslettersControllerTrait
{
    use HasNewsletterOwnerTrait;

    protected function indexPrepareItems($items)
    {
        parent::indexPrepareItems($items);
        $items->loadRelation(NewsletterModels::newsletters()::RELATION_LIST);
    }

    protected function parseRequest()
    {
        parent::parseRequest();

        $this->getRequest()->setAttribute(NewsletterOwner::CONTROLLER_ATTRIBUTE, $this->getNewsletterOwner());
    }

    public function view()
    {
        $item = $this->getModelFromRequest();
        if (!$item) {
            $this->payload()->notFound();
            return;
        }
        $this->payload()->set('item', $item);
        $this->payload()->set('filters', $this->findNewsletterFilters($item));
        $this->payload()->set('filterTypes', FilterTypes::all());
        $this->payload()->set('urls', $this->newsletterViewUrls($item));
    }

    /**
     * Standalone preview page (opened in a new tab from the view page).
     */
    public function preview()
    {
        $item = $this->getModelFromRequest();
        if (!$item) {
            $this->payload()->notFound();
            return;
        }
        $this->payload()->set('item', $item);
    }

    public function previewViewResponse($factory, $payload)
    {
        return $this->previewHtmlResponse($factory, $payload);
    }

    public function previewHtmlResponse($factory, $payload)
    {
        $item = $payload['item'] ?? null;
        if (!$item) {
            return $factory->make('Newsletter not found', 404, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }

        return $factory->make(
            RenderNewsletterPreview::for($item)->render(),
            200,
            ['Content-Type' => 'text/html; charset=UTF-8', 'X-Frame-Options' => 'SAMEORIGIN']
        );
    }

    public function addFilter()
    {
        $item = $this->getModelFromRequest();
        if (!$item || !$this->getRequest()->isMethod('POST')) {
            $this->payload()->notFound();
            return;
        }

        $post = $this->getRequest()->request;
        $this->runNewsletterViewAction(
            $item,
            fn() => CreateNewsletterFilter::for($item)
                ->withType($post->get('type'))
                ->withValues($post->all()['values'] ?? null)
                ->execute(),
            'filters.messages.added'
        );
    }

    public function deleteFilter()
    {
        $item = $this->getModelFromRequest();
        if (!$item || !$this->getRequest()->isMethod('POST')) {
            $this->payload()->notFound();
            return;
        }

        $filterId = $this->getRequest()->request->get('filter_id');
        $this->runNewsletterViewAction(
            $item,
            fn() => DeleteNewsletterFilter::for($item, $filterId)->execute(),
            'filters.messages.deleted'
        );
    }

    public function sendPreview()
    {
        $item = $this->getModelFromRequest();
        if (!$item || !$this->getRequest()->isMethod('POST')) {
            $this->payload()->notFound();
            return;
        }

        $emails = $this->getRequest()->request->get('emails');
        $this->runNewsletterViewAction(
            $item,
            fn() => SendNewsletterPreviewEmail::for($item)->to($emails)->execute(),
            'preview_email.messages.sent'
        );
    }

    protected function runNewsletterViewAction($item, callable $action, string $successMessage): void
    {
        $url = $this->newsletterViewUrls($item)['view'];
        try {
            $action();
        } catch (NewsletterException $exception) {
            $this->flashRedirect($exception->getMessage(), $url, 'error');
            return;
        }
        $this->flashRedirect(
            translator()->trans('mkt_newsletter-newsletters.' . $successMessage),
            $url
        );
    }

    protected function findNewsletterFilters($item): array
    {
        return FindFiltersForNewsletter::for($item)->fetch();
    }

    protected function newsletterViewUrls($item): array
    {
        $repository = NewsletterModels::newsletters();
        $params = ['id' => $item->getId()];
        return [
            'view' => $repository->compileURL('view', $params),
            'preview' => $repository->compileURL('preview', $params),
            'addFilter' => $repository->compileURL('addFilter', $params),
            'deleteFilter' => $repository->compileURL('deleteFilter', $params),
            'sendPreview' => $repository->compileURL('sendPreview', $params),
        ];
    }

    public function edit()
    {
        $item = $this->getModelFromRequest();
        if (!$item) {
            $this->payload()->notFound();
            return;
        }

        if ($this->getRequest()->isMethod('POST')) {
            /* Accept either the generic field name ('editor_data') or the
               legacy GrapesJS-specific field name for backward compatibility. */
            $editorData = $this->getRequest()->getPost('editor_data')
                ?? $this->getRequest()->getPost('grapesjs_data');
            $content = $this->getRequest()->getPost('content');

            if ($editorData !== null) {
                $item->setEditorData($editorData);
            }
            if ($content !== null) {
                $item->setContent($content);
            }
            $item->save();

            if ($this->getRequest()->isAjax()) {
                $this->payload()->json(['status' => 'ok']);
                return;
            }
        }

        $this->payload()->set('item', $item);
        $this->payload()->set('editorDriver', PackageConfig::editorDriver());
        $this->payload()->set('editorOptions', PackageConfig::editorOptions());
    }
}
