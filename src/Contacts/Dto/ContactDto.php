<?php

namespace Marktic\Newsletter\Contacts\Dto;

class ContactDto
{
    public ?string $email = null;
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?string $phone = null;
    public array $attributes = [];

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->email = $data['email'] ?? null;
        $dto->firstName = $data['first_name'] ?? $data['firstName'] ?? null;
        $dto->lastName = $data['last_name'] ?? $data['lastName'] ?? null;
        $dto->phone = $data['phone'] ?? null;
        $dto->attributes = $data['attributes'] ?? [];

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => $this->phone,
            'attributes' => $this->attributes,
        ];
    }
}
