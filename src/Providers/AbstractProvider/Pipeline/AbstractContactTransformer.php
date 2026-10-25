<?php

namespace Marktic\Newsletter\Providers\AbstractProvider\Pipeline;

use Marktic\Newsletter\Contacts\Dto\ContactDto;
use Pipetic\Bundle\Pipeline\AbstractTransformer;

abstract class AbstractContactTransformer extends AbstractTransformer
{
    protected function doTransform(mixed $record): mixed
    {
        /** @var ContactDto $contact */
        return $this->transformContact($record);
    }

    abstract protected function transformContact(ContactDto $contact): ?ContactDto;
}
