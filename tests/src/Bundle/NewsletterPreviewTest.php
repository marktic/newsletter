<?php

namespace Marktic\Newsletter\Tests\Bundle;

use Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypes;
use Marktic\Newsletter\NewsletterFilters\Models\NewsletterFilter;
use Marktic\Newsletter\Newsletters\Actions\Preview\RenderNewsletterPreview;
use Marktic\Newsletter\Tests\AbstractTest;

class NewsletterPreviewTest extends AbstractTest
{
    private const VIEW = __DIR__ . '/../../../src/Bundle/Resources/views/admin/mkt_newsletter-newsletters/view.php';

    protected function tearDown(): void
    {
        FilterTypes::reset();
        parent::tearDown();
    }

    public function test_preview_preserves_saved_html_in_a_sandboxed_frame(): void
    {
        $content = '<html><body><a href="https://example.com">Read more</a><script>alert(1)</script></body></html>';

        foreach ([$this->renderPreview($content), $this->renderView($content)] as $preview) {
            self::assertStringContainsString('sandbox=""', $preview);
            self::assertStringContainsString(
                'srcdoc="' . htmlspecialchars($content, ENT_QUOTES, 'UTF-8') . '"',
                $preview
            );
            self::assertStringContainsString('Weekly &lt;news&gt;', $preview);
            self::assertStringNotContainsString('<script>alert(1)</script>', $preview);
        }
    }

    public function test_preview_explains_when_no_content_has_been_saved(): void
    {
        foreach ([null, '', '   '] as $content) {
            foreach ([$this->renderPreview($content), $this->renderView($content)] as $preview) {
                self::assertStringContainsString('No saved newsletter content to preview.', $preview);
                self::assertStringNotContainsString('<iframe', $preview);
            }
        }
    }

    public function test_view_links_preview_in_a_new_tab(): void
    {
        $view = $this->renderView('<p>Hi</p>');

        self::assertMatchesRegularExpression(
            '#<a href="/newsletters/preview\?id=7" target="_blank" rel="noopener noreferrer"#',
            $view
        );
    }

    public function test_view_renders_filter_cards_and_send_preview_card_in_right_column(): void
    {
        $filter = new NewsletterFilter();
        $filter->setType('list');
        $filter->setValues([3, 4]);

        $view = $this->renderView('<p>Hi</p>', [$filter]);

        self::assertStringContainsString('class="col-lg-4"', $view);
        self::assertStringContainsString('Lists: 3, 4', $view);
        self::assertStringContainsString('action="/newsletters/deleteFilter?id=7"', $view);
        self::assertStringContainsString('action="/newsletters/addFilter?id=7"', $view);
        self::assertStringContainsString('<option value="subscribed_after">', $view);
        self::assertStringContainsString('action="/newsletters/sendPreview?id=7"', $view);
        self::assertStringContainsString('name="emails"', $view);
        self::assertStringNotContainsString('mkt_newsletter-newsletters.filters.empty', $view);
    }

    public function test_view_shows_empty_filters_state_and_disables_sending_without_content(): void
    {
        $view = $this->renderView(null);

        self::assertStringContainsString('mkt_newsletter-newsletters.filters.empty', $view);
        self::assertMatchesRegularExpression('#<button type="submit" class="btn btn-success btn-sm" disabled>#', $view);
    }

    private function renderPreview(?string $content): string
    {
        return RenderNewsletterPreview::for($this->item($content))->render();
    }

    private function renderView(?string $content, array $filters = []): string
    {
        $this->loadFakeTranslator();
        $item = $this->item($content);
        $filterTypes = FilterTypes::all();
        $urls = [];
        foreach (['view', 'preview', 'addFilter', 'deleteFilter', 'sendPreview'] as $action) {
            $urls[$action] = '/newsletters/' . $action . '?id=7';
        }

        ob_start();
        try {
            require self::VIEW;
        } finally {
            $output = ob_get_clean();
        }
        return $output;
    }

    private function item(?string $content): object
    {
        return new class($content) {
            public function __construct(private ?string $content)
            {
            }

            public function getId(): int
            {
                return 7;
            }

            public function getContent(): ?string
            {
                return $this->content;
            }

            public function getName(): string
            {
                return 'Weekly <news>';
            }

            public function getSubject(): string
            {
                return 'Latest & greatest';
            }
        };
    }
}
