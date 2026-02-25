<?php

namespace Marktic\Newsletter\NewsletterItems\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordTrait;
use Nip\Records\Record;

/**
 * Class NewsletterItem
 * @package Marktic\Newsletter\NewsletterItems\Models
 */
class NewsletterItem extends Record
{
    use NewsletterItemTrait;
    use CommonRecordTrait;

    public function getRegistry()
    {
        // TODO: Implement getRegistry() method.
    }
}
