<?php

namespace Marktic\Newsletter\Contacts\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordsTrait;
use Nip\Records\RecordManager;

/**
 * Class Events
 * @package Marktic\Newsletter\Contacts\Models
 */
class NewsletterContacts extends RecordManager
{
    use NewsletterContactsTrait;
    use CommonRecordsTrait;

    public const TABLE = 'mkt_newsletter_contacts';
    public const CONTROLLER = 'mkt_newsletter_contacts';

}
