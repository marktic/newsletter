<?php

namespace Marktic\Newsletter\ConsentArtifacts\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordsTrait;
use Nip\Records\RecordManager;

/**
 * Class NewsletterConsentArtifacts
 * @package Marktic\Newsletter\ConsentArtifacts\Models
 */
class NewsletterConsentArtifacts extends RecordManager
{
    use NewsletterConsentArtifactsTrait;
    use CommonRecordsTrait;

    public const TABLE = 'mkt_newsletter_consent_artifacts';
    public const CONTROLLER = 'mkt_newsletter-consent_artifacts';

    public const RELATION_CONSENT_STATEMENT = 'NewsletterConsentStatement';
}
