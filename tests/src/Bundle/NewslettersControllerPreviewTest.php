<?php

namespace Marktic\Newsletter\Tests\Bundle;

use LogicException;
use Marktic\Newsletter\Bundle\Controllers\Admin\NewslettersControllerTrait;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypes;
use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;
use Marktic\Newsletter\Tests\AbstractTest;
use Marktic\Newsletter\Utility\PackageConfig;
use Mockery;
use Nip\Config\Config;
use Nip\Container\Utility\Container;

class NewslettersControllerPreviewTest extends AbstractTest
{
    private $originalConfig;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalConfig = config();
        Container::container()->set('config', new Config(['mkt_newsletter' => []], true));
        PackageConfig::resetInstance();
        FilterTypes::reset();
        $this->loadFakeTranslator();
    }

    protected function tearDown(): void
    {
        Container::container()->set('config', $this->originalConfig);
        PackageConfig::resetInstance();
        FilterTypes::reset();
        parent::tearDown();
    }

    public function test_view_overrides_crud_action_without_creating_a_details_form(): void
    {
        $controller = $this->createController();
        $controller->item = new NewsletterNewsletter();

        $controller->view();

        self::assertSame($controller->item, $controller->response->data['item']);
        self::assertSame(['filter'], $controller->response->data['filters']);
        self::assertSame([], $controller->response->data['filterTypes']);
        self::assertSame('/preview', $controller->response->data['urls']['preview']);
        self::assertFalse($controller->response->missing);
    }

    public function test_controller_filter_definition_overrides_config_and_rejects_other_types(): void
    {
        $config = config();
        Container::container()->set('config', new Config([
            'mkt_newsletter' => ['filters' => ['default' => ['channel']]],
        ], true));
        PackageConfig::resetInstance();
        try {
            $controller = $this->createController();
            $controller->item = new NewsletterNewsletter();
            $controller->item->setId(7);
            $controller->availableTypes = FilterTypes::only(['list']);
            $controller->view();
            self::assertSame(['list'], array_keys($controller->response->data['filterTypes']));

            $controller->request = new class {
                public $request;
                public function __construct()
                {
                    $this->request = new \Symfony\Component\HttpFoundation\ParameterBag([
                        'type' => 'channel', 'values' => 'email',
                    ]);
                }
                public function isMethod($method): bool
                {
                    return $method === 'POST';
                }
            };
            $controller->addFilter();
            self::assertSame('error', $controller->redirect[2]);
            self::assertStringContainsString('not available', $controller->redirect[0]);
        } finally {
            Container::container()->set('config', $config);
            PackageConfig::resetInstance();
        }
    }

    public function test_edit_saves_name_content_and_editor_data(): void
    {
        $controller = $this->createController();
        $controller->item = Mockery::mock(NewsletterNewsletter::class)->makePartial();
        $controller->item->setName('Old name');
        $controller->item->shouldReceive('save')->once();
        $controller->request = $this->editRequest([
            'name' => 'Renamed newsletter',
            'content' => '<p>Updated</p>',
            'editor_data' => '{"pages":[]}',
        ]);

        $controller->edit();

        self::assertSame('Renamed newsletter', $controller->item->getName());
        self::assertSame('<p>Updated</p>', $controller->item->getContent());
        self::assertSame('{"pages":[]}', $controller->item->getEditorData());
        self::assertSame(['status' => 'ok'], $controller->response->data['json']);
    }

    public function test_edit_preserves_name_when_not_submitted_and_accepts_legacy_editor_data(): void
    {
        $controller = $this->createController();
        $controller->item = Mockery::mock(NewsletterNewsletter::class)->makePartial();
        $controller->item->setName('Original');
        $controller->item->shouldReceive('save')->once();
        $controller->request = $this->editRequest(['grapesjs_data' => '{"legacy":true}']);

        $controller->edit();

        self::assertSame('Original', $controller->item->getName());
        self::assertSame('{"legacy":true}', $controller->item->getEditorData());
    }

    private function editRequest(array $post): object
    {
        return new class($post) {
            public function __construct(private array $post)
            {
            }
            public function isMethod($method): bool
            {
                return $method === 'POST';
            }
            public function getPost($key)
            {
                return $this->post[$key] ?? null;
            }
            public function isAjax(): bool
            {
                return true;
            }
        };
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
            use NewslettersControllerTrait {
                newsletterFilterTypes as protected configuredNewsletterFilterTypes;
            }

            public $item = null;
            public $response;
            public $request;
            public ?array $availableTypes = null;
            public array $redirect = [];

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

                    public function json($data): void
                    {
                        $this->data['json'] = $data;
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

            protected function getRequest()
            {
                return $this->request;
            }

            protected function newsletterFilterTypes($item): array
            {
                return $this->availableTypes ?? $this->configuredNewsletterFilterTypes($item);
            }

            protected function flashRedirect($message, $url, $type = null): void
            {
                $this->redirect = [$message, $url, $type];
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
