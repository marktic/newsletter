<?php

namespace Marktic\Newsletter\Base\Actions\Behaviours;

use Nip\Records\AbstractModels\Record;
use Nip\Records\AbstractModels\RecordManager;

trait HasRepository
{
    protected RecordManager $repository;

    /**
     * @param $repository
     */
    public function __construct($repository = null)
    {
        $this->initRepository();
    }

    protected function initRepository($repository = null): void
    {
        $this->repository = $repository ?? $this->generateRepository();
    }

    protected function createRecord($data): Record
    {
        $contact = $this->repository->getNewRecord($data);
        $contact->save();
        return $contact;
    }

    abstract protected function generateRepository(): RecordManager;

}