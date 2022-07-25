<?php

namespace Marktic\Newsletter\Contacts\Actions;

use Marktic\Newsletter\Base\Actions\Behaviours\HasData;
use Marktic\Newsletter\Base\Actions\Behaviours\HasOwner;
use Marktic\Newsletter\Base\Actions\Behaviours\HasRepository;
use Marktic\Newsletter\Contacts\Exceptions\InvalidContact;
use Marktic\Newsletter\Contacts\Models\NewsletterContact;
use Marktic\Newsletter\Utility\NewsletterModels;
use Nip\Records\AbstractModels\Record;
use Nip\Records\AbstractModels\RecordManager;

class FindOrCreateContact
{
    use HasOwner;
    use HasRepository;
    use HasData;

    /**
     * @throws InvalidContact
     */
    public function execute()
    {
        if ($this->data instanceof NewsletterContact) {
            return $this->data;
        }

        if (is_array($this->data)) {
            return $this->executeForArray($this->data);
        }
        if (is_string($this->data)) {
            return $this->executeForString($this->data);
        }

        throw new InvalidContact();
    }

    protected function executeForArray($data): Record
    {
        if (empty($data['email'])) {
            throw new InvalidContact("Missing email in newsletter contact data");
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->checkOwnerWithArray($data);

        $contactFound = $this->findByEmail($data['email'], $data['owner_id'], $data['owner']);
        if ($contactFound) {
            return $contactFound;
        }
        return $this->createRecord($data);
    }

    protected function executeForString($data)
    {
        return $this->executeForArray(['email' => $data]);
    }

    protected function findByEmail($email, $owner_id, $owner): ?Record
    {
        return $this->repository->findOneByParams([
            'where' => [
                ['email = ?', $email],
                ['owner_id = ?', $owner_id],
                ['owner = ?', $owner],
            ]]);
    }

    protected function generateRepository(): RecordManager
    {
        return NewsletterModels::contacts();
    }
}