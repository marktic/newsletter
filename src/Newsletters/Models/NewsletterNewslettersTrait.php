<?php

namespace Marktic\Newsletter\Newsletters\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasOwner\HasOwnerRepositoryTrait;
use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableManagerTrait;
use Marktic\Newsletter\Base\Models\Traits\HasDatabaseConnectionTrait;
use Marktic\Newsletter\Utility\NewsletterModels;
use Marktic\Newsletter\Utility\PackageConfig;

trait NewsletterNewslettersTrait
{
    use HasOwnerRepositoryTrait;
    use TimestampableManagerTrait;
    use HasDatabaseConnectionTrait;

    protected function initRelationsNewsletter(): void
    {
        $this->initRelationsNewsletterOwner();
        $this->initRelationsNewsletterList();
    }

    protected function initRelationsNewsletterList(): void
    {
        $this->belongsTo(
            NewsletterNewsletters::RELATION_LIST,
            ['class' => get_class(NewsletterModels::lists())]
        );
    }

    public function generatePrimaryFK(): string
    {
        return 'newsletter_id';
    }

    protected function generateTable(): string
    {
        return PackageConfig::tableName(NewsletterModels::NEWSLETTERS, NewsletterNewsletters::TABLE);
    }
}
