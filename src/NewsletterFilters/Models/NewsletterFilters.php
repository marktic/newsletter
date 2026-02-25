<?php

namespace Marktic\Newsletter\NewsletterFilters\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordsTrait;
use Nip\Records\RecordManager;

/**
 * Class NewsletterFilters
 * @package Marktic\Newsletter\NewsletterFilters\Models
 */
class NewsletterFilters extends RecordManager
{
    use NewsletterFiltersTrait;
    use CommonRecordsTrait;

    public const TABLE = 'mkt_newsletter_filters';
    public const CONTROLLER = 'mkt_newsletter-filters';

    public const RELATION_NEWSLETTER = 'NewsletterNewsletter';
}
