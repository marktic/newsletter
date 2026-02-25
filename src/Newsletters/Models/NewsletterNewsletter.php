<?php

namespace Marktic\Newsletter\Newsletters\Models;

use Marktic\Newsletter\Base\Models\Traits\CommonRecordTrait;
use Nip\Records\Record;

/**
 * Class NewsletterNewsletter
 * @package Marktic\Newsletter\Newsletters\Models
 */
class NewsletterNewsletter extends Record
{
    use NewsletterNewsletterTrait;
    use CommonRecordTrait;

    public function getRegistry()
    {
        // TODO: Implement getRegistry() method.
    }
}
