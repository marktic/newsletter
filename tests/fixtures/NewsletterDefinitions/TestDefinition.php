<?php

namespace Marktic\Newsletter\Tests\Fixtures\NewsletterDefinitions;

use Marktic\Newsletter\NewsletterDefinitions\BaseDefinition;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypes;
use Marktic\Newsletter\Newsletters\Actions\Preview\SendNewsletterPreviewEmail;

class TestDefinition extends BaseDefinition
{
    public array $previewRecipients = [];
    public bool $sent = false;

    public function getFilterTypes(): array
    {
        return FilterTypes::only(['list']);
    }

    public function getPlaceholders(): array
    {
        return ['{{ organization }}' => 'Organization'];
    }

    public function sendPreview(mixed $recipients): array
    {
        return $this->previewRecipients = SendNewsletterPreviewEmail::parseRecipients($recipients);
    }

    public function send(): void
    {
        $this->sent = true;
    }
}
