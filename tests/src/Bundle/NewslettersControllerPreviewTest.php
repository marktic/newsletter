<?php

namespace Marktic\Newsletter\Tests\Bundle;

use LogicException;
use Marktic\Newsletter\Bundle\Controllers\Admin\NewslettersControllerTrait;
use Marktic\Newsletter\Tests\AbstractTest;

class NewslettersControllerPreviewTest extends AbstractTest
{
    public function test_view_overrides_crud_action_without_creating_a_details_form(): void
    {
        $controller = $this->createController();
        $controller->item = new \stdClass();

        $controller->view();

        self::assertSame($controller->item, $controller->response->data['item']);
        self::assertSame(['filter'], $controller->response->data['filters']);
        self::assertArrayHasKey('list', $controller->response->data['filterTypes']);
        self::assertSame('/preview', $controller->response->data['urls']['preview']);
        self::assertFalse($controller->response->missing);
    }

    public function test_preview_renders_standalone_page_without_layout(): void
    {
        $controller = $this->createController();
        $controller->item = new class {
            public function getName(): string
            {
                return 'Weekly';
            }

            public function getSubject(): ?string
            {
                return null;
            }

            public function getContent(): string
            {
                return '<p>Hi</p>';
            }
        };

        $controller->preview();
        $factory = new class {
            public function make($content = '', $status = 200, array $headers = []): array
            {
                return [$content, $status, $headers];
            }
        };
        [$content, $status, $headers] = $controller->previewViewResponse($factory, $controller->response->data);

        self::assertSame(200, $status);
        self::assertSame('text/html; charset=UTF-8', $headers['Content-Type']);
        self::assertStringStartsWith('<!DOCTYPE html>', $content);
        self::assertStringContainsString('srcdoc="&lt;p&gt;Hi&lt;/p&gt;"', $content);
    }

    public function test_view_returns_not_found_for_a_missing_newsletter(): void
    {
        $controller = $this->createController();

        $controller->view();

        self::assertTrue($controller->response->missing);
        self::assertSame([], $controller->response->data);
        self::assertNull($controller->response->view);
    }

    private function createController(): NewsletterPreviewCrudController
    {
        return new class extends NewsletterPreviewCrudController {
            use NewslettersControllerTrait;

            public $item = null;
            public $response;

            public function __construct()
            {
                $this->response = new class {
                    public array $data = [];
                    public ?string $view = null;
                    public bool $missing = false;

                    public function set($key, $value): void
                    {
                        $this->data[$key] = $value;
                    }

                    public function setView(string $view): void
                    {
                        $this->view = $view;
                    }

                    public function notFound(): void
                    {
                        $this->missing = true;
                    }
                };
            }

            protected function getModelFromRequest()
            {
                return $this->item;
            }

            public function payload()
            {
                return $this->response;
            }

            protected function findNewsletterFilters($item): array
            {
                return ['filter'];
            }

            protected function newsletterViewUrls($item): array
            {
                return ['view' => '/view', 'preview' => '/preview'];
            }

            protected function generateBillingOwnerDefault()
            {
                return null;
            }
        };
    }
}

class NewsletterPreviewCrudController
{
    public function view()
    {
        $this->getModelForm();
    }

    protected function getModelForm()
    {
        throw new LogicException('The newsletter preview must not request a details form.');
    }
}
