<?php

namespace Marktic\Newsletter\Subscriptions\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasId\RecordHasId;
use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableTrait;
use Marktic\Newsletter\Consents\Models\NewsletterConsent;
use Marktic\Newsletter\Contacts\Models\NewsletterContact;
use Marktic\Newsletter\Lists\Models\NewsletterList;
use Nip\Records\Collections\Associated;

/**
 * Trait NewsletterSubscriptionTrait
 *
 * @method NewsletterContact getNewsletterContact()
 * @method NewsletterList getNewsletterList()
 * @method NewsletterConsent getNewsletterConsent()
 * @method Associated|NewsletterConsent[] getNewsletterConsentArtifacts()
 */
trait NewsletterSubscriptionTrait
{
    use RecordHasId;
    use TimestampableTrait;
}
