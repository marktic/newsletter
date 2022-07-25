<?php

namespace Marktic\Newsletter\ConsentStatements\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordTrait;
use Nip\Records\Record;

/**
 * Class NewsletterConsentStatement
 * @package Marktic\Newsletter\ConsentStatements\Models
 */
class NewsletterConsentStatement extends Record
{
    use NewsletterConsentStatementTrait;
    use CommonRecordTrait;

    public function getRegistry()
    {
        // TODO: Implement getRegistry() method.
    }

}
