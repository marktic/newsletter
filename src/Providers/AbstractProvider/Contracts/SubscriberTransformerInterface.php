<?php

namespace Marktic\Newsletter\Providers\AbstractProvider\Contracts;

use Marktic\Newsletter\Contacts\Dto\ContactDto;
use Marktic\Newsletter\Lists\Dto\ListDto;

interface SubscriberTransformerInterface
{
    public function toProviderFormat(ContactDto $contact): array;

    public function fromProviderFormat(array $data): ContactDto;
}
