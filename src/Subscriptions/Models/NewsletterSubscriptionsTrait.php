<?php

namespace Marktic\Newsletter\Subscriptions\Models;

use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableManagerTrait;
use Marktic\Newsletter\Base\Models\Traits\HasDatabaseConnectionTrait;
use Marktic\Newsletter\Subscriptions\Models\Filters\FilterManager;
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
        $this->initRelationsNewsletterConsent();
        $this->initRelationsNewsletterConsentArtifacts();
    }

    protected function initRelationsNewsletterList(): void
    {
        $this->belongsTo(
            NewsletterSubscriptions::RELATION_LIST, ['class' => get_class(NewsletterModels::lists())]);
    }

    protected function initRelationsNewsletterContact(): void
    {
        $repository = NewsletterModels::contacts();
        $this->belongsTo(NewsletterSubscriptions::RELATION_CONTACT,
            ['class' => get_class($repository), 'fk' => $repository->getPrimaryFK()]);
    }

    protected function initRelationsNewsletterConsent(): void
    {
        $this->belongsTo(
            NewsletterSubscriptions::RELATION_CONSENT,
            ['class' => get_class(NewsletterModels::consents())]);
    }

    protected function initRelationsNewsletterConsentArtifacts(): void
    {
        $this->hasMany(
            NewsletterSubscriptions::RELATION_CONSENT_ARTIFACTS,
            ['class' => get_class(NewsletterModels::consentArtifacts())]);
    }

    public function generatePrimaryFK(): string
    {
        return 'subscription_id';
    }

    public function getFilterManagerClass()
    {
        return FilterManager::class;
    }

    protected function generateTable(): string
    {
        return PackageConfig::tableName(NewsletterModels::SUBSCRIPTIONS, NewsletterSubscriptions::TABLE);
    }
}