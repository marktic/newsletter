<?php

namespace Marktic\Newsletter\Providers\AbstractProvider\Contracts;

use Marktic\Newsletter\Lists\Dto\ListDto;

interface ListTransformerInterface
{
    public function toProviderFormat(ListDto $list): array;

    public function fromProviderFormat(array $data): ListDto;
}
