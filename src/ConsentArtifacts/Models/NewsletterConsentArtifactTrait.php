<?php

namespace Marktic\Newsletter\ConsentArtifacts\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasId\RecordHasId;
use Marktic\Newsletter\Base\Models\Behaviours\HasOwner\HasOwnerRecordTrait;
use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableTrait;

/**
 * Trait NewsletterConsentArtifactTrait
 */
trait NewsletterConsentArtifactTrait
{
    use RecordHasId;
    use TimestampableTrait;

}
