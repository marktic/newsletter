<?php

namespace Marktic\Newsletter\ConsentStatements\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordsTrait;
use Nip\Records\RecordManager;

/**
 * Class NewsletterConsentStatements
 * @package Marktic\Newsletter\ConsentStatements\Models
 */
class NewsletterConsentStatements extends RecordManager
{
    use NewsletterConsentStatementsTrait;
    use CommonRecordsTrait;

    public const TABLE = 'mkt_newsletter_consent_statements';
    public const CONTROLLER = 'mkt_newsletter-consent_statements';

}
