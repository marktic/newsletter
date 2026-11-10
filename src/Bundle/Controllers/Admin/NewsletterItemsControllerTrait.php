<?php

declare(strict_types=1);

namespace Marktic\Newsletter\Bundle\Controllers\Admin;

use Marktic\Newsletter\Utility\NewsletterModels;
use Nip\Controllers\Response\ResponsePayload;

/**
 * @method ResponsePayload payload()
 */
trait NewsletterItemsControllerTrait
{

    protected function generateModelName(): string
    {
        return get_class(NewsletterModels::newsletterItems());
    }
    protected function indexPrepareItems($items)
    {
        parent::indexPrepareItems($items);
    }
}
