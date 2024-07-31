<?php

declare(strict_types=1);

namespace Marktic\Newsletter\Bundle\Controllers\Admin;

use Marktic\Newsletter\Bundle\Controllers\Base\Behaviours\HasNewsletterOwnerTrait;
use Marktic\Newsletter\Subscriptions\Actions\Queries\FindQueryForOwner;
use Nip\Controllers\Response\ResponsePayload;
use Marktic\Newsletter\Bundle\Library\View\ViewUtility;
use Nip\View\View;

/**
 * @method ResponsePayload payload()
 */
trait SubscriptionsControllerTrait
{
    use HasNewsletterOwnerTrait;

    protected function newIndexQuery(): \Nip\Database\Query\Select
    {
        $query = FindQueryForOwner::make()->withOwner($this->getNewsletterOwner())->findQuery();
        return $query;
    }
}
