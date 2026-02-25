<?php

declare(strict_types=1);

namespace Marktic\Newsletter\Bundle\Controllers\Admin;

use Nip\Controllers\Response\ResponsePayload;

/**
 * @method ResponsePayload payload()
 */
trait NewsletterItemsControllerTrait
{
    protected function indexPrepareItems($items)
    {
        parent::indexPrepareItems($items);
    }
}
