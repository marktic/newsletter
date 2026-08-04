<?php

namespace Marktic\Newsletter\Providers\AbstractProvider\Pipeline;

use Marktic\Newsletter\Contacts\Dto\ContactDto;
use Marktic\Newsletter\Providers\AbstractProvider\AbstractProvider;
use Pipetic\Bundle\Pipeline\AbstractExtractor;

abstract class AbstractSubscribersExtractor extends AbstractExtractor
{
    protected AbstractProvider $provider;
    protected string $listId;

    public function __construct(AbstractProvider $provider, string $listId)
    {
        $this->provider = $provider;
        $this->listId = $listId;
    }

    protected function doExtract(): iterable
    {
        $subscribers = $this->provider->getSubscribers($this->listId);
        foreach ($subscribers as $subscriber) {
            yield $subscriber;
        }
    }
}
