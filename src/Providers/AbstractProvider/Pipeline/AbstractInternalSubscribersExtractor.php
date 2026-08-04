<?php

namespace Marktic\Newsletter\Providers\AbstractProvider\Pipeline;

use Marktic\Newsletter\Contacts\Dto\ContactDto;
use Marktic\Newsletter\Lists\Models\NewsletterList;
use Marktic\Newsletter\Subscriptions\Models\NewsletterSubscription;
use Marktic\Newsletter\Utility\NewsletterModels;
use Pipetic\Bundle\Pipeline\AbstractExtractor;

abstract class AbstractInternalSubscribersExtractor extends AbstractExtractor
{
    protected NewsletterList $list;

    public function __construct(NewsletterList $list)
    {
        $this->list = $list;
    }

    protected function doExtract(): iterable
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

            yield $dto;
        }
    }
}
