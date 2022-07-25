<?php

namespace Marktic\Newsletter\Subscriptions\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasId\RecordHasId;
use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableTrait;
use Marktic\Newsletter\Contacts\Models\NewsletterContact;
use Marktic\Newsletter\Lists\Models\NewsletterList;

/**
 * Trait NewsletterSubscriptionTrait
 *
 * @method NewsletterContact getNewsletterContact()
 * @method NewsletterList getNewsletterList()
 */
trait NewsletterSubscriptionTrait
{
    use RecordHasId;
    use TimestampableTrait;
}
