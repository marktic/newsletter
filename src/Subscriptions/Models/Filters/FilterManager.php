<?php

namespace Marktic\Newsletter\Subscriptions\Models\Filters;

/**
 * Class FilterManager
 * @package Galantom\Common\Models\Donations
 */
class FilterManager extends \Nip\Records\Filters\FilterManager
{

    public function init()
    {
        parent::init();

        $this->addFilter(new NewsletterOwnerFilter());
    }
}
