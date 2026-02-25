<?php

namespace Marktic\Newsletter\NewsletterFilters\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordTrait;
use Nip\Records\Record;

/**
 * Class NewsletterFilter
 * @package Marktic\Newsletter\NewsletterFilters\Models
 */
class NewsletterFilter extends Record
{
    use NewsletterFilterTrait;
    use CommonRecordTrait;

    public function getRegistry()
    {
        // TODO: Implement getRegistry() method.
    }
}
