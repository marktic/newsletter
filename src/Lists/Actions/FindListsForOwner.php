<?php

namespace Marktic\Newsletter\Lists\Actions;

use Bytic\Actions\Action;
use Bytic\Actions\Behaviours\Entities\FindRecords;
use Marktic\Newsletter\NewsletterOwners\Actions\Behaviours\HasOwnerRecordTrait;
use Marktic\Newsletter\Lists\Actions\Behaviours\HasRepository;

class FindListsForOwner extends Action
{
    use HasOwnerRecordTrait;
    use HasRepository;
    use FindRecords;

    protected function findParams(): array
    {
        $params  = [];
        $params = $this->findParamsPopulateWithOwner($params);
        return $params;
    }

}
