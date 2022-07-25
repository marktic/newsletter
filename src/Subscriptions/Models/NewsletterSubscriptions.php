<?php

namespace Marktic\Newsletter\Subscriptions\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordsTrait;
use Nip\Records\RecordManager;

/**
 * Class Events
 * @package Marktic\Promotion\Models\NewsletterSubscriptions
 */
class NewsletterSubscriptions extends RecordManager
{
    use NewsletterSubscriptionsTrait;
    use CommonRecordsTrait;

    public const TABLE = 'mkt_newsletter_subscriptions';
    public const CONTROLLER = 'mkt_newsletter_subscriptions';

}
