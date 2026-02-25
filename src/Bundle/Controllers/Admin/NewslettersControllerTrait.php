<?php

declare(strict_types=1);

namespace Marktic\Newsletter\Bundle\Controllers\Admin;

use Marktic\Newsletter\Bundle\Controllers\Base\Behaviours\HasNewsletterOwnerTrait;
use Marktic\Newsletter\NewsletterOwners\Dto\NewsletterOwner;
use Marktic\Newsletter\Utility\NewsletterModels;
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

    public function edit()
    {
        $item = $this->getModelFromRequest();
        if (!$item) {
            $this->payload()->notFound();
            return;
        }

        if ($this->getRequest()->isMethod('POST')) {
            $grapesjsData = $this->getRequest()->getPost('grapesjs_data');
            $content = $this->getRequest()->getPost('content');

            if ($grapesjsData !== null) {
                $item->setGrapesjsData($grapesjsData);
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
        $this->payload()->setView('edit');
    }
}

