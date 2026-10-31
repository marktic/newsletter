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
        self::assertFalse($controller->response->missing);
    }

    public function test_view_returns_not_found_for_a_missing_newsletter(): void
    {
        $controller = $this->createController();

        $controller->view();

        self::assertTrue($controller->response->missing);
        self::assertSame([], $controller->response->data);
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
                    public bool $missing = false;

                    public function set($key, $value): void
                    {
                        $this->data[$key] = $value;
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
