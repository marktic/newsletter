<?php

namespace Marktic\Newsletter\Lists\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordTrait;
use Nip\Records\Record;

/**
 * Class NewsletterList
 * @package Marktic\Newsletter\Lists\Models
 */
class NewsletterList extends Record
{
    use NewsletterListTrait;
    use CommonRecordTrait;

    public function getRegistry()
    {
        // TODO: Implement getRegistry() method.
    }

}
