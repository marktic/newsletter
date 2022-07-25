<?php

namespace Marktic\Newsletter\Consents\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordsTrait;
use Nip\Records\RecordManager;

/**
 * Class Events
 * @package Marktic\Newsletter\Consents\Models
 */
class NewsletterConsents extends RecordManager
{
    use NewsletterConsentsTrait;
    use CommonRecordsTrait;

    public const TABLE = 'mkt_newsletter_consents';
    public const CONTROLLER = 'mkt_newsletter_consents';

}
