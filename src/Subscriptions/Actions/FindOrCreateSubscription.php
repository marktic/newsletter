<?php

namespace Marktic\Newsletter\Subscriptions\Actions;

use Marktic\Newsletter\Base\Actions\Behaviours\HasOwner;
use Marktic\Newsletter\Base\Actions\Behaviours\HasRepository;
use Marktic\Newsletter\Contacts\Actions\FindOrCreateContact;
use Marktic\Newsletter\Subscriptions\Models\NewsletterSubscription;
use Marktic\Newsletter\Subscriptions\Models\NewsletterSubscriptions;
use Marktic\Newsletter\Utility\NewsletterModels;
use Nip\Records\AbstractModels\RecordManager;

class FindOrCreateSubscription
{
    use HasOwner;
    use HasRepository;

    protected $contact;
    protected $list;

    /**
     * @var mixed|null
     */
    protected mixed $consent;

    /**
     * @var mixed|null
     */
    protected mixed $consent_statement;

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

    public function withConsent($name = null, $text = null): static
    {
        $this->consent = $name;
        $this->consent_statement = $text;
        return $this;
    }

    /**
     * @return NewsletterSubscription|\Nip\Records\AbstractModels\Record
     * @throws \Marktic\Newsletter\Consents\Exceptions\InvalidConsent
     * @throws \Marktic\Newsletter\Contacts\Exceptions\InvalidContact
     */
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

        $record =  $this->createRecord($data);
        $record->getRelation(NewsletterSubscriptions::RELATION_CONTACT)->setResults($contact);
        $record->getRelation(NewsletterSubscriptions::RELATION_LIST)->setResults($list);

        return $record;
    }

    protected function generateRepository(): RecordManager
    {
        return NewsletterModels::subscriptions();
    }
}
