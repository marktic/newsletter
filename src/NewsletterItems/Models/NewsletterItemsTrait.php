<?php

namespace Marktic\Newsletter\NewsletterItems\Models;

use Marktic\Newsletter\Base\Models\Traits\HasDatabaseConnectionTrait;
use Marktic\Newsletter\Utility\NewsletterModels;
use Marktic\Newsletter\Utility\PackageConfig;

trait NewsletterItemsTrait
{
    use HasDatabaseConnectionTrait;

    protected function initRelationsNewsletter(): void
    {
        $this->initRelationsNewsletterNewsletter();
    }

    protected function initRelationsNewsletterNewsletter(): void
    {
        $this->belongsTo(
            NewsletterItems::RELATION_NEWSLETTER,
            ['class' => get_class(NewsletterModels::newsletters()), 'fk' => 'id_newsletter']
        );
    }

    public function generatePrimaryFK(): string
    {
        return 'id';
    }

    protected function generateTable(): string
    {
        return PackageConfig::tableName(NewsletterModels::NEWSLETTER_ITEMS, NewsletterItems::TABLE);
    }
}
