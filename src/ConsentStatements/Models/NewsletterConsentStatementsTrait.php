<?php

namespace Marktic\Newsletter\ConsentStatements\Models;

use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableManagerTrait;
use Marktic\Newsletter\Base\Models\Traits\HasDatabaseConnectionTrait;
use Marktic\Newsletter\Utility\NewsletterModels;
use Marktic\Newsletter\Utility\PackageConfig;

trait NewsletterConsentStatementsTrait
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

    public function generatePrimaryFK()
    {
        return 'consent_statement_id';
    }

    protected function generateTable(): string
    {
        return PackageConfig::tableName(NewsletterModels::CONSENT_STATEMENTS, NewsletterConsentStatements::TABLE);
    }
}
