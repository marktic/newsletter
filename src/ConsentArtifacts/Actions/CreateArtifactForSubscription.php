<?php

namespace Marktic\Newsletter\ConsentArtifacts\Actions;

use Marktic\Newsletter\Base\Actions\Behaviours\HasData;
use Marktic\Newsletter\Base\Actions\Behaviours\HasOwner;
use Marktic\Newsletter\Base\Actions\Behaviours\HasRepository;
use Marktic\Newsletter\Consents\Actions\FindOrCreateConsent;
use Marktic\Newsletter\Consents\Exceptions\InvalidConsent;
use Marktic\Newsletter\ConsentStatements\Actions\FindOrCreateConsentStatement;
use Marktic\Newsletter\ConsentStatements\Exceptions\InvalidConsentStatement;
use Marktic\Newsletter\Contacts\Actions\FindOrCreateContact;
use Marktic\Newsletter\Subscriptions\Actions\FindOrCreateSubscription;
use Marktic\Newsletter\Utility\NewsletterModels;
use Nip\Records\AbstractModels\RecordManager;

/**
 *
 */
class CreateArtifactForSubscription
{
    use HasRepository;
    use HasOwner;
    use HasData;

    public function withConsent(mixed $consent): self
    {
        $this->data['consent'] = $consent;
        return $this;
    }

    public function withConsentStatement($text = null, $name = null): static
    {
        $this->data['consent'] = $name;
        $this->data['consent_statement'] = $text;
        return $this;
    }

    public function withContact(mixed $data): self
    {
        $this->data['contact'] = $data;
        return $this;
    }

    public function withList(mixed $data): self
    {
        $this->data['list'] = $data;
        return $this;
    }

    /**
     * @throws InvalidConsent
     */
    public function execute()
    {
        $this->hydrateConsent();
        $this->hydrateConsentStatement();
        $this->hydrateSubscription();

        $data = [
            'consent_id' => isset($this->data['consent']) ? $this->data['consent']->id : null,
            'statement_id' => isset($this->data['consent_statement']) ? $this->data['consent_statement']->id : null,
            'contact_id' => isset($this->data['contact']) ? $this->data['contact']->id : null,
            'subscription_id' => isset($this->data['subscription']) ? $this->data['subscription']->id : null,
        ];

        return $this->createRecord($data);
    }

    protected function hydrateConsent()
    {
        $this->data['consent'] = FindOrCreateConsent::for($this->data['consent'])
            ->forOwner($this->owner, $this->owner_id)
            ->execute();
    }

    protected function hydrateConsentStatement()
    {
        if (empty($this->data['consent_statement'])) {
            return;
        }
        if (is_string($this->data['consent_statement'])) {
            $this->data['consent_statement'] = [
                'text' => $this->data['consent_statement']
            ];
        }
        if (!is_array($this->data['consent_statement'])) {
            throw new InvalidConsentStatement();
        }

        $this->data['consent_statement']['consent_id'] = $this->data['consent']->id;
        $this->data['consent_statement'] = FindOrCreateConsentStatement::for($this->data['consent_statement'])
            ->execute();
    }

    protected function hydrateSubscription()
    {
        $this->data['subscription'] = FindOrCreateSubscription::create()
            ->forContact($this->data['contact'] ?? null)
            ->forList($this->data['list'] ?? null)
            ->withConsent($this->data['consent'] ?? null, $this->data['consent_statement'] ?? null)
            ->forOwner($this->owner, $this->owner_id)
            ->execute();

        $this->data['contact'] = $this->data['subscription']->getNewsletterContact();
        $this->data['list'] = $this->data['subscription']->getNewsletterList();
    }

    protected function generateRepository(): RecordManager
    {
        return NewsletterModels::consentArtifacts();
    }


}
