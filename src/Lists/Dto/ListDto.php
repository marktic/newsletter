<?php

namespace Marktic\Newsletter\Lists\Dto;

class ListDto
{
    public ?string $remoteId = null;
    public ?string $name = null;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->remoteId = $data['remote_id'] ?? $data['remoteId'] ?? null;
        $dto->name = $data['name'] ?? null;

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'remote_id' => $this->remoteId,
            'name' => $this->name,
        ];
    }
}
