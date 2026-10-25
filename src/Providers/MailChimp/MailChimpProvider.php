<?php

namespace Marktic\Newsletter\Providers\MailChimp;

use Marktic\Newsletter\Contacts\Dto\ContactDto;
use Marktic\Newsletter\Lists\Dto\ListDto;
use Marktic\Newsletter\Providers\AbstractProvider\AbstractProvider;
use Marktic\Newsletter\Subscriptions\Dto\SubscriptionDto;

/**
 * MailChimp provider implementation.
 *
 * Uses the MailChimp Marketing API v3.
 * @see https://mailchimp.com/developer/marketing/api/
 *
 * Authentication: HTTP Basic Auth with any username and api_key as password.
 * The datacenter (dc) suffix on the API key determines the API base URL:
 *   https://<dc>.api.mailchimp.com/3.0/
 */
class MailChimpProvider extends AbstractProvider
{
    private const API_VERSION = '3.0';

    public function __construct(string $apiKey = '')
    {
        if ($apiKey !== '') {
            $this->setApiKey($apiKey);
        }
    }

    public function getName(): string
    {
        return 'mailchimp';
    }

    public function setApiKey(string $apiKey): static
    {
        parent::setApiKey($apiKey);
        $dc = $this->extractDatacenter($apiKey);
        $this->apiBaseUrl = "https://{$dc}.api.mailchimp.com/" . self::API_VERSION;

        return $this;
    }

    protected function buildHeaders(): array
    {
        return [
            'Authorization: Basic ' . base64_encode('anystring:' . $this->apiKey),
        ];
    }

    public function getLists(): array
    {
        $response = $this->get('/lists', ['count' => 1000]);
        $lists = [];

        foreach ($response['lists'] ?? [] as $item) {
            $lists[] = $this->mapList($item);
        }

        return $lists;
    }

    public function findList(string $listId): ?ListDto
    {
        $response = $this->get("/lists/{$listId}");

        if (empty($response['id'])) {
            return null;
        }

        return $this->mapList($response);
    }

    public function getSubscribers(string $listId): array
    {
        $response = $this->get("/lists/{$listId}/members", ['count' => 1000]);
        $subscribers = [];

        foreach ($response['members'] ?? [] as $member) {
            $subscribers[] = $this->mapMember($member);
        }

        return $subscribers;
    }

    public function findSubscriber(string $listId, string $email): ?ContactDto
    {
        $hash = $this->emailHash($email);
        $response = $this->get("/lists/{$listId}/members/{$hash}");

        if (empty($response['id'])) {
            return null;
        }

        return $this->mapMember($response);
    }

    public function subscribe(string $listId, ContactDto $contact): bool
    {
        $hash = $this->emailHash($contact->email);
        $payload = [
            'email_address' => $contact->email,
            'status' => SubscriptionDto::STATUS_SUBSCRIBED,
            'merge_fields' => [
                'FNAME' => $contact->firstName ?? '',
                'LNAME' => $contact->lastName ?? '',
            ],
        ];

        if (!empty($contact->attributes)) {
            $payload['merge_fields'] = array_merge($payload['merge_fields'], $contact->attributes);
        }

        $response = $this->put("/lists/{$listId}/members/{$hash}", $payload);

        return !empty($response['id']);
    }

    public function unsubscribe(string $listId, string $email): bool
    {
        $hash = $this->emailHash($email);
        $response = $this->patch("/lists/{$listId}/members/{$hash}", [
            'status' => SubscriptionDto::STATUS_UNSUBSCRIBED,
        ]);

        return isset($response['status']) && $response['status'] === SubscriptionDto::STATUS_UNSUBSCRIBED;
    }

    public function updateSubscriber(string $listId, ContactDto $contact): bool
    {
        $hash = $this->emailHash($contact->email);
        $payload = [
            'merge_fields' => [
                'FNAME' => $contact->firstName ?? '',
                'LNAME' => $contact->lastName ?? '',
            ],
        ];

        if (!empty($contact->attributes)) {
            $payload['merge_fields'] = array_merge($payload['merge_fields'], $contact->attributes);
        }

        $response = $this->patch("/lists/{$listId}/members/{$hash}", $payload);

        return !empty($response['id']);
    }

    private function extractDatacenter(string $apiKey): string
    {
        $parts = explode('-', $apiKey);

        return end($parts) ?: 'us1';
    }

    private function emailHash(string $email): string
    {
        return md5(strtolower($email));
    }

    private function mapMember(array $member): ContactDto
    {
        $dto = new ContactDto();
        $dto->email = $member['email_address'] ?? null;
        $dto->firstName = $member['merge_fields']['FNAME'] ?? null;
        $dto->lastName = $member['merge_fields']['LNAME'] ?? null;

        $mergeFields = $member['merge_fields'] ?? [];
        unset($mergeFields['FNAME'], $mergeFields['LNAME']);
        $dto->attributes = $mergeFields;

        return $dto;
    }

    private function mapList(array $list): ListDto
    {
        $dto = new ListDto();
        $dto->remoteId = $list['id'] ?? null;
        $dto->name = $list['name'] ?? null;

        return $dto;
    }
}
