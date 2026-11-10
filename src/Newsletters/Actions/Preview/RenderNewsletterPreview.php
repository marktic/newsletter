<?php

namespace Marktic\Newsletter\Newsletters\Actions\Preview;

/**
 * Renders the standalone (full page) newsletter preview.
 */
class RenderNewsletterPreview
{
    public const TEMPLATE = __DIR__ . '/../../../Bundle/Resources/views/admin/mkt_newsletter-newsletters/preview.php';

    protected $item;

    public static function for($item): self
    {
        $action = new static();
        $action->item = $item;
        return $action;
    }

    public function render(): string
    {
        $item = $this->item;
        ob_start();
        try {
            require static::TEMPLATE;
        } finally {
            $output = (string)ob_get_clean();
        }
        return $output;
    }
}
