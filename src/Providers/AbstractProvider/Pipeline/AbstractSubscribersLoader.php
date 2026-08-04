<?php

namespace Marktic\Newsletter\Providers\AbstractProvider\Pipeline;

use Marktic\Newsletter\Contacts\Dto\ContactDto;
use Marktic\Newsletter\Providers\AbstractProvider\AbstractProvider;
use Pipetic\Bundle\Pipeline\AbstractLoader;

abstract class AbstractSubscribersLoader extends AbstractLoader
{
    protected AbstractProvider $provider;
    protected string $listId;

    public function __construct(AbstractProvider $provider, string $listId)
    {
        $this->provider = $provider;
        $this->listId = $listId;
    }

    protected function doLoad(mixed $record): void
    {
        /** @var ContactDto $contact */
        $contact = $record;
        $existing = $this->provider->findSubscriber($this->listId, $contact->email);

        if ($existing !== null) {
            $this->provider->updateSubscriber($this->listId, $contact);
        } else {
            $this->provider->subscribe($this->listId, $contact);
        }
    }
}
