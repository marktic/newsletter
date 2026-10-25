<?php

namespace Marktic\Newsletter\Providers\MailChimp;

use Marktic\Newsletter\Contacts\Dto\ContactDto;
use Marktic\Newsletter\Providers\AbstractProvider\Contracts\SubscriberTransformerInterface;

class MailChimpSubscriberTransformer implements SubscriberTransformerInterface
{
    public function toProviderFormat(ContactDto $contact): array
    {
        $payload = [
            'email_address' => $contact->email,
            'merge_fields' => [
                'FNAME' => $contact->firstName ?? '',
                'LNAME' => $contact->lastName ?? '',
            ],
        ];

        if (!empty($contact->attributes)) {
            $payload['merge_fields'] = array_merge($payload['merge_fields'], $contact->attributes);
        }

        return $payload;
    }

    public function fromProviderFormat(array $data): ContactDto
    {
        $dto = new ContactDto();
        $dto->email = $data['email_address'] ?? null;
        $dto->firstName = $data['merge_fields']['FNAME'] ?? null;
        $dto->lastName = $data['merge_fields']['LNAME'] ?? null;

        $mergeFields = $data['merge_fields'] ?? [];
        unset($mergeFields['FNAME'], $mergeFields['LNAME']);
        $dto->attributes = $mergeFields;

        return $dto;
    }
}
