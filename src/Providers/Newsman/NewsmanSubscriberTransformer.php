<?php

namespace Marktic\Newsletter\Providers\Newsman;

use Marktic\Newsletter\Contacts\Dto\ContactDto;
use Marktic\Newsletter\Providers\AbstractProvider\Contracts\SubscriberTransformerInterface;

class NewsmanSubscriberTransformer implements SubscriberTransformerInterface
{
    public function toProviderFormat(ContactDto $contact): array
    {
        $payload = [
            'email' => $contact->email,
        ];

        if ($contact->firstName !== null) {
            $payload['firstname'] = $contact->firstName;
        }

        if ($contact->lastName !== null) {
            $payload['lastname'] = $contact->lastName;
        }

        if (!empty($contact->attributes)) {
            $payload['props'] = $contact->attributes;
        }

        return $payload;
    }

    public function fromProviderFormat(array $data): ContactDto
    {
        $dto = new ContactDto();
        $dto->email = $data['email'] ?? null;
        $dto->firstName = $data['firstname'] ?? null;
        $dto->lastName = $data['lastname'] ?? null;

        $knownKeys = ['email', 'firstname', 'lastname', 'status', 'subscriber_id',
            'subscribe_date', 'unsubscribe_date', 'bounce_date', 'spam_date', 'ip'];
        $dto->attributes = $data['props'] ?? array_diff_key($data, array_flip($knownKeys));

        return $dto;
    }
}
