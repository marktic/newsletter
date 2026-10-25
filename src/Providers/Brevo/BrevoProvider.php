<?php

namespace Marktic\Newsletter\Providers\Brevo;

use Marktic\Newsletter\Contacts\Dto\ContactDto;
use Marktic\Newsletter\Lists\Dto\ListDto;
use Marktic\Newsletter\Providers\AbstractProvider\AbstractProvider;

/**
 * Brevo (formerly Sendinblue) provider implementation.
 *
 * Uses the Brevo API v3.
 * @see https://developers.brevo.com/reference/
 *
 * Authentication: api-key header.
 * Base URL: https://api.brevo.com/v3
 */
class BrevoProvider extends AbstractProvider
{
    protected string $apiBaseUrl = 'https://api.brevo.com/v3';

    public function __construct(string $apiKey = '')
    {
        if ($apiKey !== '') {
            $this->setApiKey($apiKey);
        }
    }

    public function getName(): string
    {
        return 'brevo';
    }

    protected function buildHeaders(): array
    {
        return [
            'api-key: ' . $this->apiKey,
            'accept: application/json',
        ];
    }

    public function getLists(): array
    {
        $response = $this->get('/contacts/lists', ['limit' => 50, 'offset' => 0]);
        $lists = [];

        foreach ($response['lists'] ?? [] as $item) {
            $lists[] = $this->mapList($item);
        }

        return $lists;
    }

    public function findList(string $listId): ?ListDto
    {
        $response = $this->get("/contacts/lists/{$listId}");

        if (empty($response['id'])) {
            return null;
        }

        return $this->mapList($response);
    }

    public function getSubscribers(string $listId): array
    {
        $response = $this->get("/contacts/lists/{$listId}/contacts", ['limit' => 500, 'offset' => 0]);
        $subscribers = [];

        foreach ($response['contacts'] ?? [] as $contact) {
            $subscribers[] = $this->mapContact($contact);
        }

        return $subscribers;
    }

    public function findSubscriber(string $listId, string $email): ?ContactDto
    {
        $response = $this->get('/contacts/' . urlencode($email));

        if (empty($response['email'])) {
            return null;
        }

        $contact = $this->mapContact($response);
        $listIds = array_column($response['listIds'] ?? [], null);

        if (!in_array((int)$listId, $listIds, true)) {
            return null;
        }

        return $contact;
    }

    public function subscribe(string $listId, ContactDto $contact): bool
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
            'listIds' => [(int)$listId],
            'updateEnabled' => true,
        ];

        if (!empty($attributes)) {
            $payload['attributes'] = $attributes;
        }

        $response = $this->post('/contacts', $payload);

        return isset($response['id']) || isset($response['email']) || empty($response);
    }

    public function unsubscribe(string $listId, string $email): bool
    {
        $response = $this->post("/contacts/lists/{$listId}/contacts/remove", [
            'emails' => [$email],
        ]);

        return empty($response) || !isset($response['code']);
    }

    public function updateSubscriber(string $listId, ContactDto $contact): bool
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

        $payload = [];
        if (!empty($attributes)) {
            $payload['attributes'] = $attributes;
        }

        $response = $this->put('/contacts/' . urlencode($contact->email), $payload);

        return empty($response) || !isset($response['code']);
    }

    private function mapContact(array $data): ContactDto
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

    private function mapList(array $data): ListDto
    {
        $dto = new ListDto();
        $dto->remoteId = (string)($data['id'] ?? '');
        $dto->name = $data['name'] ?? null;

        return $dto;
    }
}
