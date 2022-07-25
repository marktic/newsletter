<?php

namespace Marktic\Newsletter\Lists\Actions;

use Marktic\Newsletter\Base\Actions\Behaviours\HasData;
use Marktic\Newsletter\Base\Actions\Behaviours\HasOwner;
use Marktic\Newsletter\Base\Actions\Behaviours\HasRepository;
use Marktic\Newsletter\Contacts\Models\NewsletterContact;
use Marktic\Newsletter\Lists\Exceptions\InvalidList;
use Marktic\Newsletter\Utility\NewsletterModels;
use Nip\Records\AbstractModels\Record;
use Nip\Records\AbstractModels\RecordManager;

class FindOrCreateList
{
    use HasOwner;
    use HasRepository;
    use HasData;

    /**
     * @throws InvalidList
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

        throw new InvalidList();
    }

    protected function executeForArray($data): Record
    {
        if (empty($data['name'])) {
            throw new InvalidList("Missing name in newsletter list data");
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->checkOwnerWithArray($data);

        $recordFound = $this->findByName($data['name'], $data['owner_id'], $data['owner']);
        if ($recordFound) {
            return $recordFound;
        }
        return $this->createRecord($data);
    }

    protected function executeForString($data): Record
    {
        return $this->executeForArray(['name' => $data]);
    }

    protected function findByName($name, $owner_id, $owner): ?Record
    {
        return $this->repository->findOneByParams([
            'where' => [
                ['name = ?', $name],
                ['owner_id = ?', $owner_id],
                ['owner = ?', $owner],
            ]]);
    }

    protected function generateRepository(): RecordManager
    {
        return NewsletterModels::lists();
    }
}