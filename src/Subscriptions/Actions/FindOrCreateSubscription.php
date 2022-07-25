<?php

namespace Marktic\Newsletter\Subscriptions\Actions;

use Marktic\Newsletter\Base\Actions\Behaviours\HasOwner;
use Marktic\Newsletter\Base\Actions\Behaviours\HasRepository;
use Marktic\Newsletter\Contacts\Actions\FindOrCreateContact;
use Marktic\Newsletter\Utility\NewsletterModels;
use Nip\Records\AbstractModels\RecordManager;

class FindOrCreateSubscription
{
    use HasOwner;
    use HasRepository;

    protected $contact;
    protected $list;

    public static function create(): static
    {
        return new static();
    }

    public function forContact($contact): static
    {
        $this->contact = $contact;
        return $this;
    }

    public function forList($list): static
    {
        $this->list = $list;
        return $this;
    }

    public function execute()
    {
        $contact = FindOrCreateContact::for($this->contact)
            ->forOwner($this->owner, $this->owner_id)
            ->execute();

        $list = FindOrCreateContact::for($this->list)
            ->forOwner($this->owner, $this->owner_id)
            ->execute();

        $data = [
            'contact_id' => $contact->id,
            'list_id' => $list->id,
        ];
        return $this->createRecord($data);
    }

    protected function generateRepository(): RecordManager
    {
        return NewsletterModels::subscriptions();
    }
}
