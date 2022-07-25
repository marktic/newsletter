<?php

namespace Marktic\Newsletter\Base\Models\Behaviours\HasOwner;

trait HasOwnerRepositoryTrait
{
    protected function initRelationsNewsletterOwner(): void
    {
        $this->morphTo(
            HasOwnerRepositoryInterface::RELATION_OWNER,
            ['morphPrefix' => 'owner', 'morphTypeField' => 'owner']
        );
    }
}
