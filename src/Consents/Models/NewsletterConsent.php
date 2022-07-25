<?php

namespace Marktic\Newsletter\Consents\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordTrait;
use Nip\Records\Record;

/**
 * Class NewsletterConsent
 * @package Marktic\Newsletter\Consents\Models
 */
class NewsletterConsent extends Record
{
    use NewsletterConsentTrait;
    use CommonRecordTrait;

    public function getRegistry()
    {
        // TODO: Implement getRegistry() method.
    }

}
