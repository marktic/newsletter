<?php

namespace Marktic\Newsletter\Contacts\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordTrait;
use Nip\Records\Record;

/**
 * Class NewsletterContact
 * @package Marktic\Newsletter\Contacts\Models
 */
class NewsletterContact extends Record
{
    use NewsletterContactTrait;
    use CommonRecordTrait;

    public function getRegistry()
    {
        // TODO: Implement getRegistry() method.
    }

}
