<?php

namespace Marktic\Newsletter\Tests\Bundle;

use Marktic\Newsletter\Tests\AbstractTest;

class NewsletterPreviewTest extends AbstractTest
{
    public function test_preview_preserves_saved_html_in_a_sandboxed_frame(): void
    {
        $content = '<html><body><a href="https://example.com">Read more</a><script>alert(1)</script></body></html>';
        $preview = $this->renderPreview($content);

        self::assertStringContainsString('sandbox=""', $preview);
        self::assertStringContainsString(
            'srcdoc="' . htmlspecialchars($content, ENT_QUOTES, 'UTF-8') . '"',
            $preview
        );
        self::assertStringContainsString('Weekly &lt;news&gt;', $preview);
        self::assertStringNotContainsString('<script>alert(1)</script>', $preview);
    }

    public function test_preview_explains_when_no_content_has_been_saved(): void
    {
        foreach ([null, '', '   '] as $content) {
            $preview = $this->renderPreview($content);

            self::assertStringContainsString('No saved newsletter content to preview.', $preview);
            self::assertStringNotContainsString('<iframe', $preview);
        }
    }

    private function renderPreview(?string $content): string
    {
        $item = new class($content) {
            public function __construct(private ?string $content)
            {
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

        ob_start();
        require dirname(__DIR__, 4) . '/src/Bundle/Resources/views/admin/mkt_newsletter-newsletters/view.php';
        return ob_get_clean();
    }
}
