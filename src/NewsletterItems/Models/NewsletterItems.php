<?php

namespace Marktic\Newsletter\NewsletterItems\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordsTrait;
use Nip\Records\RecordManager;

/**
 * Class NewsletterItems
 * @package Marktic\Newsletter\NewsletterItems\Models
 */
class NewsletterItems extends RecordManager
{
    use NewsletterItemsTrait;
    use CommonRecordsTrait;

    public const TABLE = 'mkt_newsletter_items';
    public const CONTROLLER = 'mkt_newsletter-items';

    public const RELATION_NEWSLETTER = 'NewsletterNewsletter';
}
