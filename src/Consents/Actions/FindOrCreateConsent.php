<?php

namespace Marktic\Newsletter\Consents\Actions;

use Marktic\Newsletter\Base\Actions\Behaviours\HasData;
use Marktic\Newsletter\Base\Actions\Behaviours\HasOwner;
use Marktic\Newsletter\Base\Actions\Behaviours\HasRepository;
use Marktic\Newsletter\Consents\Exceptions\InvalidConsent;
use Marktic\Newsletter\Consents\Models\NewsletterConsent;
use Marktic\Newsletter\Consents\Models\NewsletterConsents;
use Marktic\Newsletter\Utility\NewsletterModels;
use Nip\Records\AbstractModels\Record;
use Nip\Records\AbstractModels\RecordManager;

class FindOrCreateConsent
{
    use HasOwner;
    use HasRepository;
    use HasData;

    /**
     * @throws InvalidConsent
     */
    public function execute()
    {
        if ($this->data instanceof NewsletterConsent) {
            return $this->data;
        }

        if (is_array($this->data)) {
            return $this->executeForArray($this->data);
        }
        if (is_string($this->data)) {
            return $this->executeForString($this->data);
        }

        throw new InvalidConsent();
    }

    protected function executeForArray($data): Record
    {
        $data['name'] = $data['name'] ?? NewsletterConsents::DEFAULT_NAME;

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->checkOwnerWithArray($data);

        $recordFound = $this->findByName($data['name'], $data['owner_id'], $data['owner']);
        if ($recordFound) {
            return $recordFound;
        }

        return $this->createRecord($this->data);
    }

    protected function executeForString($data)
    {
        return $this->executeForArray(['name' => $data]);
    }

    protected function findByName($email, $owner_id, $owner): ?Record
    {
        return $this->repository->findOneByParams([
            'where' => [
                ['name = ?', $email],
                ['owner_id = ?', $owner_id],
                ['owner = ?', $owner],
            ]]);
    }

    protected function generateRepository(): RecordManager
    {
        return NewsletterModels::consents();
    }
}