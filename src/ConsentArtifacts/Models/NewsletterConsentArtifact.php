<?php

namespace Marktic\Newsletter\ConsentArtifacts\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordTrait;
use Nip\Records\Record;

/**
 * Class NewsletterConsentArtifact
 * @package Marktic\Newsletter\ConsentArtifacts\Models
 */
class NewsletterConsentArtifact extends Record
{
    use NewsletterConsentArtifactTrait;
    use CommonRecordTrait;

    public function getRegistry()
    {
        // TODO: Implement getRegistry() method.
    }

}
