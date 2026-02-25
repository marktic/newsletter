<?php

namespace Marktic\Newsletter\Newsletters\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordsTrait;
use Nip\Records\RecordManager;

/**
 * Class NewsletterNewsletters
 * @package Marktic\Newsletter\Newsletters\Models
 */
class NewsletterNewsletters extends RecordManager
{
    use NewsletterNewslettersTrait;
    use CommonRecordsTrait;

    public const TABLE = 'mkt_newsletter_newsletters';
    public const CONTROLLER = 'mkt_newsletter-newsletters';

    public const RELATION_LIST = 'NewsletterList';
}
