<?php

namespace Marktic\Newsletter\ConsentArtifacts\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasOwner\HasOwnerRepositoryTrait;
use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableManagerTrait;
use Marktic\Newsletter\Base\Models\Traits\HasDatabaseConnectionTrait;
use Marktic\Newsletter\Subscriptions\Models\NewsletterSubscriptions;
use Marktic\Newsletter\Utility\NewsletterModels;
use Marktic\Newsletter\Utility\PackageConfig;

trait NewsletterConsentArtifactsTrait
{
    use TimestampableManagerTrait;
    use HasDatabaseConnectionTrait;

    protected function bootNewsletterConsentsTrait()
    {
//        static::updating(function ($event) {
//            /** @var Event $event */
//            UpdatePromotionCodes::for($event->getRecord());
//        });
    }

    protected function initRelationsNewsletter(): void
    {
        $this->initRelationsNewsletterStatement();
    }

    protected function initRelationsNewsletterStatement(): void
    {
        $this->belongsTo(
            NewsletterConsentArtifacts::RELATION_CONSENT_STATEMENT,
            ['class' => get_class(NewsletterModels::consentStatements()), 'fk' => 'statement_id']);
    }

    public function generatePrimaryFK()
    {
        return 'consent_artifact_id';
    }

    protected function generateTable(): string
    {
        return PackageConfig::tableName(NewsletterModels::CONSENT_ARTIFACTS, NewsletterConsentArtifacts::TABLE);
    }
}
