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
    use NewsletterSubscriptionsTrait, CommonRecordsTrait {
        NewsletterSubscriptionsTrait::getFilterManagerClass insteadof CommonRecordsTrait;
    }

    public const TABLE = 'mkt_newsletter_subscriptions';
    public const CONTROLLER = 'mkt_newsletter-subscriptions';

    public const RELATION_LIST = 'NewsletterList';
    public const RELATION_CONTACT = 'NewsletterContact';

    public const RELATION_CONSENT = 'NewsletterConsent';

    public const RELATION_CONSENT_ARTIFACTS = 'NewsletterConsentArtifacts';
}
