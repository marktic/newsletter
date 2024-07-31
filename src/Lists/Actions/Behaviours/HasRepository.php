<?php

namespace Marktic\Newsletter\Lists\Actions\Behaviours;

use Marktic\Newsletter\Subscriptions\Models\NewsletterSubscriptions;
use Marktic\Newsletter\Utility\NewsletterModels;
use Nip\Records\RecordManager;

trait HasRepository
{
    use \Bytic\Actions\Behaviours\Entities\HasRepository;

    protected function generateRepository(): NewsletterSubscriptions|RecordManager
    {
        return NewsletterModels::lists();
    }
}