<?php

namespace Marktic\Newsletter\Providers\Newsman;

use Marktic\Newsletter\Contacts\Dto\ContactDto;
use Marktic\Newsletter\Lists\Dto\ListDto;
use Marktic\Newsletter\Providers\AbstractProvider\AbstractProvider;

/**
 * Newsman.ro provider implementation.
 *
 * Uses the Newsman REST API v1.2.
 * @see https://kb.newsman.ro/api/1.2/
 *
 * Authentication: api_key as a request parameter (query string for GET, body for POST).
 * Base URL: https://api.newsmanapp.com
 */
class NewsmanProvider extends AbstractProvider
{
    private const API_BASE = 'https://api.newsmanapp.com';

    protected string $userId = '';

    public function __construct(string $userId = '', string $apiKey = '')
    {
        $this->apiBaseUrl = self::API_BASE;

        if ($userId !== '') {
            $this->userId = $userId;
        }

        if ($apiKey !== '') {
            $this->setApiKey($apiKey);
        }
    }

    public function getName(): string
    {
        return 'newsman';
    }

    public function setUserId(string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    protected function buildHeaders(): array
    {
        return [
            'Content-Type: application/json',
            'Accept: application/json',
        ];
    }

    private function withApiKey(array $params = []): array
    {
        return array_merge(['api_key' => $this->apiKey], $params);
    }

    public function getLists(): array
    {
        $response = $this->get('/list.all.json', $this->withApiKey());
        $lists = [];

        if (!is_array($response)) {
            return $lists;
        }

        foreach ($response as $item) {
            $lists[] = $this->mapList($item);
        }

        return $lists;
    }

    public function findList(string $listId): ?ListDto
    {
        $lists = $this->getLists();

        foreach ($lists as $list) {
            if ($list->remoteId === $listId) {
                return $list;
            }
        }

        return null;
    }

    public function getSubscribers(string $listId): array
    {
        $response = $this->get('/list.getSubscribers.json', $this->withApiKey(['list_id' => $listId]));

        $subscribers = [];

        if (!is_array($response)) {
            return $subscribers;
        }

        foreach ($response as $item) {
            $subscribers[] = $this->mapSubscriber($item);
        }

        return $subscribers;
    }

    public function findSubscriber(string $listId, string $email): ?ContactDto
    {
        $response = $this->get('/subscriber.getByEmail.json', $this->withApiKey([
            'list_id' => $listId,
            'email' => $email,
        ]));

        if (empty($response) || !is_array($response) || empty($response['email'])) {
            return null;
        }

        return $this->mapSubscriber($response);
    }

    public function subscribe(string $listId, ContactDto $contact): bool
    {
        $subscriber = [
            'api_key' => $this->apiKey,
            'list_id' => $listId,
            'email' => $contact->email,
        ];

        if ($contact->firstName !== null) {
            $subscriber['firstname'] = $contact->firstName;
        }

        if ($contact->lastName !== null) {
            $subscriber['lastname'] = $contact->lastName;
        }

        if (!empty($contact->attributes)) {
            $subscriber['props'] = $contact->attributes;
        }

        $response = $this->post('/subscriber.saveSubscribe.json', $subscriber);

        return !empty($response);
    }

    public function unsubscribe(string $listId, string $email): bool
    {
        $response = $this->post('/subscriber.unsubscribe.json', [
            'api_key' => $this->apiKey,
            'list_id' => $listId,
            'email' => $email,
        ]);

        return !empty($response);
    }

    public function updateSubscriber(string $listId, ContactDto $contact): bool
    {
        return $this->subscribe($listId, $contact);
    }

    private function mapSubscriber(array $data): ContactDto
    {
        $dto = new ContactDto();
        $dto->email = $data['email'] ?? null;
        $dto->firstName = $data['firstname'] ?? null;
        $dto->lastName = $data['lastname'] ?? null;

        $knownKeys = ['email', 'firstname', 'lastname', 'status', 'subscriber_id', 'subscribe_date',
            'unsubscribe_date', 'bounce_date', 'spam_date', 'ip'];
        $dto->attributes = array_diff_key($data['props'] ?? [], []) + array_diff_key($data, array_flip($knownKeys));

        return $dto;
    }

    private function mapList(array $data): ListDto
    {
        $dto = new ListDto();
        $dto->remoteId = (string)($data['list_id'] ?? $data['id'] ?? '');
        $dto->name = $data['list_name'] ?? $data['name'] ?? null;

        return $dto;
    }
}
