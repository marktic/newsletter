<?php

namespace Marktic\Newsletter\Subscriptions\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasId\RecordHasId;
use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableTrait;

/**
 * Trait NewsletterSubscriptionTrait
 */
trait NewsletterSubscriptionTrait
{
    use RecordHasId;
    use TimestampableTrait;
}
