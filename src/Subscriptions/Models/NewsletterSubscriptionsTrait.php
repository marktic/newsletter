<?php

namespace Marktic\Newsletter\Subscriptions\Models;

use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableManagerTrait;
use Marktic\Newsletter\Utility\NewsletterModels;
use Marktic\Newsletter\Utility\PackageConfig;

trait NewsletterSubscriptionsTrait
{
    use TimestampableManagerTrait;

    protected function bootNewsletterSubscriptionsTrait()
    {
//        static::updating(function ($event) {
//            /** @var Event $event */
//            UpdatePromotionCodes::for($event->getRecord());
//        });
    }

    public function generatePrimaryFK(): string
    {
        return 'subscription_id';
    }

    protected function generateTable(): string
    {
        return PackageConfig::tableName(NewsletterModels::SUBSCRIPTIONS, NewsletterSubscriptions::TABLE);
    }
}