<?php

namespace Marktic\Newsletter\ConsentArtifacts\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasId\RecordHasId;
use Marktic\Newsletter\Base\Models\Behaviours\HasOwner\HasOwnerRecordTrait;
use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableTrait;
use Marktic\Newsletter\ConsentStatements\Models\NewsletterConsentStatement;

/**
 * Trait NewsletterConsentArtifactTrait
 * @method NewsletterConsentStatement getNewsletterConsentStatement()
 */
trait NewsletterConsentArtifactTrait
{
    use RecordHasId;
    use TimestampableTrait;

}
