<?php

namespace Marktic\Newsletter\Lists\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordsTrait;
use Nip\Records\RecordManager;

/**
 * Class Events
 * @package Marktic\Newsletter\Lists\Models
 */
class NewsletterLists extends RecordManager
{
    use NewsletterListsTrait;
    use CommonRecordsTrait;

    public const TABLE = 'mkt_newsletter_lists';
    public const CONTROLLER = 'mkt_newsletter_lists';

    protected function initRelationsNewsletter(): void
    {
        $this->initRelationsNewsletterOwner();
    }
}
