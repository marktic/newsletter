<?php

namespace Marktic\Newsletter\Subscriptions\Actions\Queries;

use Bytic\Actions\Action;
use Bytic\Actions\Behaviours\Entities\HasSelectQuery;
use Marktic\Newsletter\Lists\Actions\FindListsForOwner;
use Marktic\Newsletter\NewsletterOwners\Actions\Behaviours\HasOwnerRecordTrait;
use Marktic\Newsletter\Subscriptions\Actions\Behaviours\HasRepository;

class FindQueryForOwner extends Action
{
    use HasOwnerRecordTrait;
    use HasRepository;
    use HasSelectQuery;

    protected function findParams(): array
    {
        return [
            'where' => [
                'list_id IN ?' => $this->findListsIds(),
            ],
        ];
    }

    protected function findListsIds()
    {
        $lists = FindListsForOwner::make()
            ->withOwner($this->getOwner())
            ->fetch();

        return $lists->pluck('id');
    }
}
