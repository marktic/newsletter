<?php

declare(strict_types=1);

namespace Marktic\Newsletter\Bundle\Controllers\Admin;

use Marktic\Newsletter\Bundle\Controllers\Base\Behaviours\HasNewsletterOwnerTrait;
use Marktic\Newsletter\NewsletterOwners\Dto\NewsletterOwner;
use Marktic\Newsletter\Utility\NewsletterModels;
use Nip\Controllers\Response\ResponsePayload;

/**
 * @method ResponsePayload payload()
 */
trait NewslettersControllerTrait
{
    use HasNewsletterOwnerTrait;

    protected function indexPrepareItems($items)
    {
        parent::indexPrepareItems($items);
        $items->loadRelation(NewsletterModels::newsletters()::RELATION_LIST);
    }

    protected function parseRequest()
    {
        parent::parseRequest();

        $this->getRequest()->setAttribute(NewsletterOwner::CONTROLLER_ATTRIBUTE, $this->getNewsletterOwner());
    }
}
