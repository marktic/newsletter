<?php

declare(strict_types=1);

namespace Marktic\Newsletter\Bundle\Controllers\Admin;

use Marktic\Newsletter\Utility\NewsletterModels;
use Nip\Controllers\Response\ResponsePayload;

/**
 * @method ResponsePayload payload()
 */
trait NewsletterFiltersControllerTrait
{

    protected function generateModelName(): string
    {
        return get_class(NewsletterModels::newsletterFilters());
    }
    protected function indexPrepareItems($items)
    {
        parent::indexPrepareItems($items);
    }
}
