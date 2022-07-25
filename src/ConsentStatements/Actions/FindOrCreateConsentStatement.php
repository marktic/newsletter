<?php

namespace Marktic\Newsletter\ConsentStatements\Actions;

use Marktic\Newsletter\Base\Actions\Behaviours\HasData;
use Marktic\Newsletter\Base\Actions\Behaviours\HasOwner;
use Marktic\Newsletter\Base\Actions\Behaviours\HasRepository;
use Marktic\Newsletter\Consents\Exceptions\InvalidConsent;
use Marktic\Newsletter\Consents\Models\NewsletterConsent;
use Marktic\Newsletter\Consents\Models\NewsletterConsents;
use Marktic\Newsletter\ConsentStatements\Exceptions\InvalidConsentStatement;
use Marktic\Newsletter\ConsentStatements\Models\NewsletterConsentStatement;
use Marktic\Newsletter\Contacts\Models\NewsletterContact;
use Marktic\Newsletter\Utility\NewsletterModels;
use Nip\Records\AbstractModels\Record;
use Nip\Records\AbstractModels\RecordManager;

class FindOrCreateConsentStatement
{
    use HasRepository;
    use HasData;

    /**
     * @throws InvalidConsent
     */
    public function execute()
    {
        if ($this->data instanceof NewsletterConsentStatement) {
            return $this->data;
        }

        if (is_array($this->data)) {
            return $this->executeForArray($this->data);
        }

        throw new InvalidConsentStatement();
    }

    protected function executeForArray($data): Record
    {
        $data['hash'] = $data['name'] ?? NewsletterConsents::DEFAULT_NAME;

        $recordFound = $this->findByHash($data['hash'], $data['consent_id']);
        if ($recordFound) {
            return $recordFound;
        }

        return $this->createRecord($this->data);
    }

    protected function executeForString($data)
    {
        return $this->executeForArray(['name' => $data]);
    }

    protected function findByHash($hash, $consent_id): ?Record
    {
        return $this->repository->findOneByParams([
            'where' => [
                ['hash = ?', $hash],
                ['consent_id = ?', $consent_id],
            ]]);
    }

    protected function generateRepository(): RecordManager
    {
        return NewsletterModels::consentStatements();
    }
}