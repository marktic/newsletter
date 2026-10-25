<?php

namespace Marktic\Newsletter\Providers\AbstractProvider\Contracts;

use Marktic\Newsletter\Contacts\Dto\ContactDto;
use Marktic\Newsletter\Lists\Dto\ListDto;
use Marktic\Newsletter\Subscriptions\Dto\SubscriptionDto;

interface ProviderInterface
{
    public function getLists(): array;

    public function findList(string $listId): ?ListDto;

    public function getSubscribers(string $listId): array;

    public function findSubscriber(string $listId, string $email): ?ContactDto;

    public function subscribe(string $listId, ContactDto $contact): bool;

    public function unsubscribe(string $listId, string $email): bool;

    public function updateSubscriber(string $listId, ContactDto $contact): bool;
}
