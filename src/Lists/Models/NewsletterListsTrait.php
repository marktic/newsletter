<?php

namespace Marktic\Newsletter\Lists\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasOwner\HasOwnerRepositoryTrait;
use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableManagerTrait;
use Marktic\Newsletter\Base\Models\Traits\HasDatabaseConnectionTrait;
use Marktic\Newsletter\Utility\NewsletterModels;
use Marktic\Newsletter\Utility\PackageConfig;

trait NewsletterListsTrait
{
    use HasOwnerRepositoryTrait;
    use TimestampableManagerTrait;
    use HasDatabaseConnectionTrait;

    protected function bootNewsletterListsTrait()
    {
//        static::updating(function ($event) {
//            /** @var Event $event */
//            UpdatePromotionCodes::for($event->getRecord());
//        });
    }

    public function generatePrimaryFK()
    {
        return 'list_id';
    }

    protected function generateTable(): string
    {
        return PackageConfig::tableName(NewsletterModels::LISTS, NewsletterLists::TABLE);
    }
}
