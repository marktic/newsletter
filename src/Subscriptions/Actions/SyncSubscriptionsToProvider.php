<?php

namespace Marktic\Newsletter\Subscriptions\Actions;

use Marktic\Newsletter\Contacts\Dto\ContactDto;
use Marktic\Newsletter\Lists\Models\NewsletterList;
use Marktic\Newsletter\Providers\AbstractProvider\AbstractProvider;
use Marktic\Newsletter\Subscriptions\Models\NewsletterSubscription;
use Marktic\Newsletter\Utility\NewsletterModels;

class SyncSubscriptionsToProvider
{
    protected AbstractProvider $provider;
    protected string $listId;
    protected NewsletterList $list;

    public static function create(AbstractProvider $provider, string $listId, NewsletterList $list): static
    {
        $instance = new static();
        $instance->provider = $provider;
        $instance->listId = $listId;
        $instance->list = $list;

        return $instance;
    }

    public function execute(): void
    {
        $subscriptions = NewsletterModels::subscriptions()->findByParams([
            'where' => [
                ['list_id = ?', $this->list->id],
            ],
        ]);

        foreach ($subscriptions as $subscription) {
            /** @var NewsletterSubscription $subscription */
            $contact = $subscription->getNewsletterContact();
            if ($contact === null) {
                continue;
            }

            $dto = new ContactDto();
            $dto->email = $contact->getEmail();
            $dto->firstName = $contact->getFirstName();
            $dto->lastName = $contact->getLastName();

            $existing = $this->provider->findSubscriber($this->listId, $dto->email);

            if ($existing !== null) {
                $this->provider->updateSubscriber($this->listId, $dto);
            } else {
                $this->provider->subscribe($this->listId, $dto);
            }
        }
    }
}
