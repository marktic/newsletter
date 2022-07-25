<?php

namespace Marktic\Newsletter\Base\Models\Behaviours\HasId;

trait RecordHasId
{
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = intval($id);
    }
}