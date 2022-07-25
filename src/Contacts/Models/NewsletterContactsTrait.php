<?php

namespace Marktic\Newsletter\Contacts\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasOwner\HasOwnerRepositoryTrait;
use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableManagerTrait;
use Marktic\Newsletter\Utility\NewsletterModels;
use Marktic\Newsletter\Utility\PackageConfig;

trait NewsletterContactsTrait
{
    use HasOwnerRepositoryTrait;
    use TimestampableManagerTrait;

    protected function bootNewsletterContactsTrait()
    {
//        static::updating(function ($event) {
//            /** @var Event $event */
//            UpdatePromotionCodes::for($event->getRecord());
//        });
    }

    protected function initRelationsNewsletter(): void
    {
        $this->initRelationsNewsletterOwner();
    }

    public function generatePrimaryFK()
    {
        return 'list_id';
    }

    protected function generateTable(): string
    {
        return PackageConfig::tableName(NewsletterModels::CONTACTS, NewsletterContacts::TABLE);
    }
}
