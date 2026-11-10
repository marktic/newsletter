<?php

declare(strict_types=1);

namespace Marktic\Newsletter\NewsletterDefinitions;

use Marktic\Newsletter\Base\Exceptions\NewsletterException;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypeInterface;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypes;
use Marktic\Newsletter\Newsletters\Actions\Preview\SendNewsletterPreviewEmail;
use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;
use Marktic\Newsletter\Utility\PackageConfig;

class BaseDefinition
{
    public function __construct(protected NewsletterNewsletter $newsletter)
    {
    }

    public function getNewsletter(): NewsletterNewsletter
    {
        return $this->newsletter;
    }

    /** @return array<string, FilterTypeInterface> */
    public function getFilterTypes(): array
    {
        return FilterTypes::only(PackageConfig::availableFilterTypes(
            $this->newsletter->getOwner(),
            $this->newsletter->getType()
        ));
    }

    /** @return array<string, string> Placeholder token => display label. */
    public function getPlaceholders(): array
    {
        return [
            '{{ first_name }}' => 'First name',
            '{{ last_name }}' => 'Last name',
            '{{ email }}' => 'Email',
        ];
    }

    /** @return string[] */
    public function sendPreview(mixed $recipients): array
    {
        return SendNewsletterPreviewEmail::for($this->newsletter)->to($recipients)->execute();
    }

    public function send(): void
    {
        throw new NewsletterException('Full newsletter sending is not configured for this definition.');
    }
}
