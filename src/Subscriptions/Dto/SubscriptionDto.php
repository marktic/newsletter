<?php

namespace Marktic\Newsletter\Subscriptions\Dto;

use Marktic\Newsletter\Contacts\Dto\ContactDto;
use Marktic\Newsletter\Lists\Dto\ListDto;

class SubscriptionDto
{
    public ?ContactDto $contact = null;
    public ?ListDto $list = null;
    public ?string $status = null;

    public const STATUS_SUBSCRIBED = 'subscribed';
    public const STATUS_UNSUBSCRIBED = 'unsubscribed';
    public const STATUS_PENDING = 'pending';

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->status = $data['status'] ?? self::STATUS_SUBSCRIBED;

        if (!empty($data['contact'])) {
            $dto->contact = ContactDto::fromArray($data['contact']);
        }

        if (!empty($data['list'])) {
            $dto->list = ListDto::fromArray($data['list']);
        }

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'contact' => $this->contact?->toArray(),
            'list' => $this->list?->toArray(),
            'status' => $this->status,
        ];
    }
}
