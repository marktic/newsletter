<?php

namespace Marktic\Newsletter\Tests\Bundle;

use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;
use Marktic\Newsletter\Tests\AbstractTest;

class NewsletterEditorTest extends AbstractTest
{
    public function test_all_editors_render_as_fragments_with_escaped_editable_names(): void
    {
        foreach (['grapesjs', 'unlayer', 'beefree'] as $driver) {
            $html = $this->renderEditor($driver);
            $markup = preg_replace('#<script\b[^>]*>.*?</script>#si', '', $html);
            self::assertDoesNotMatchRegularExpression('#<!doctype|</?(?:html|head|body)\b#i', $markup);
            self::assertStringContainsString('name="name"', $markup);
            self::assertStringContainsString('value="Weekly &quot;news&quot; &lt;update&gt;"', $markup);
            self::assertStringContainsString("formData.append('name',", $html);
            self::assertStringContainsString("getElementById('newsletter-name').addEventListener('input'", $html);
            self::assertStringContainsString('id="html-source-open"', $html);
            self::assertStringContainsString('name="content"', $html);
            self::assertStringContainsString('Saved newsletter', $html);
            foreach (['{{ first_name }}', '{{ last_name }}', '{{ email }}'] as $placeholder) {
                self::assertStringContainsString('data-placeholder="' . $placeholder . '"', $html);
            }
            self::assertStringNotContainsString('html, body {', $html);
        }
    }

    public function test_grapesjs_has_new_blocks_and_svg_icons(): void
    {
        $html = $this->renderEditor('grapesjs');
        foreach (['three-columns', 'image', 'image-text', 'header', 'quote', 'social', 'footer'] as $block) {
            self::assertStringContainsString("id: 'email-" . $block . "'", $html);
        }
        self::assertStringContainsString('media: \'<svg', $html);
        self::assertStringContainsString('role="presentation"', $html);
        self::assertStringContainsString('<!DOCTYPE html PUBLIC', $html);
    }

    private function renderEditor(string $editorDriver): string
    {
        $item = new NewsletterNewsletter();
        $item->setId(7);
        $item->setName('Weekly "news" <update>');
        $item->setContent('<html><body>Saved newsletter</body></html>');
        $editorOptions = [];
        ob_start();
        try {
            require __DIR__ . '/../../../src/Bundle/Resources/views/admin/mkt_newsletter-newsletters/edit.php';
        } finally {
            $html = ob_get_clean();
        }
        return $html;
    }
}
