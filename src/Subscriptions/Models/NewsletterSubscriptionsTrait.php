<?php

namespace Marktic\Newsletter\Subscriptions\Models;

use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableManagerTrait;
use Marktic\Newsletter\Base\Models\Traits\HasDatabaseConnectionTrait;
use Marktic\Newsletter\Utility\NewsletterModels;
use Marktic\Newsletter\Utility\PackageConfig;

trait NewsletterSubscriptionsTrait
{
    use TimestampableManagerTrait;
    use HasDatabaseConnectionTrait;

    protected function bootNewsletterSubscriptionsTrait()
    {
//        static::updating(function ($event) {
//            /** @var Event $event */
//            UpdatePromotionCodes::for($event->getRecord());
//        });
    }

    protected function initRelationsNewsletter(): void
    {
        $this->initRelationsNewsletterList();
        $this->initRelationsNewsletterContact();
    }

    protected function initRelationsNewsletterList()
    {
        $this->belongsTo(NewsletterSubscriptions::RELATION_LIST, ['class' => get_class(NewsletterModels::lists())]);
    }

    protected function initRelationsNewsletterContact()
    {
        $this->belongsTo(NewsletterSubscriptions::RELATION_CONTACT, ['class' => get_class(NewsletterModels::contacts())]);
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