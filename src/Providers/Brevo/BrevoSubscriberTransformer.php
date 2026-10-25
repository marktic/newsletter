<?php

namespace Marktic\Newsletter\Providers\Brevo;

use Marktic\Newsletter\Contacts\Dto\ContactDto;
use Marktic\Newsletter\Providers\AbstractProvider\Contracts\SubscriberTransformerInterface;

class BrevoSubscriberTransformer implements SubscriberTransformerInterface
{
    public function toProviderFormat(ContactDto $contact): array
    {
        $attributes = [];

        if ($contact->firstName !== null) {
            $attributes['FIRSTNAME'] = $contact->firstName;
        }

        if ($contact->lastName !== null) {
            $attributes['LASTNAME'] = $contact->lastName;
        }

        if (!empty($contact->attributes)) {
            $attributes = array_merge($attributes, $contact->attributes);
        }

        $payload = [
            'email' => $contact->email,
            'updateEnabled' => true,
        ];

        if (!empty($attributes)) {
            $payload['attributes'] = $attributes;
        }

        return $payload;
    }

    public function fromProviderFormat(array $data): ContactDto
    {
        $dto = new ContactDto();
        $dto->email = $data['email'] ?? null;
        $dto->firstName = $data['attributes']['FIRSTNAME'] ?? null;
        $dto->lastName = $data['attributes']['LASTNAME'] ?? null;

        $attributes = $data['attributes'] ?? [];
        unset($attributes['FIRSTNAME'], $attributes['LASTNAME']);
        $dto->attributes = $attributes;

        return $dto;
    }
}
