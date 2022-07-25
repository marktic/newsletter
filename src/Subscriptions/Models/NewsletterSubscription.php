<?php

namespace Marktic\Newsletter\Subscriptions\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordTrait;
use Nip\Records\Record;

/**
 * Class NewsletterSubscription
 * @package Marktic\Promotion\Models\Events
 */
class NewsletterSubscription extends Record 
{
    use NewsletterSubscriptionTrait;
    use CommonRecordTrait;

    public function getRegistry()
    {
        // TODO: Implement getRegistry() method.
    }

}
