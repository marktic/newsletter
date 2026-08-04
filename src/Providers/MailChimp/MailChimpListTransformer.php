<?php

namespace Marktic\Newsletter\Providers\MailChimp;

use Marktic\Newsletter\Lists\Dto\ListDto;
use Marktic\Newsletter\Providers\AbstractProvider\Contracts\ListTransformerInterface;

class MailChimpListTransformer implements ListTransformerInterface
{
    public function toProviderFormat(ListDto $list): array
    {
        return [
            'name' => $list->name,
        ];
    }

    public function fromProviderFormat(array $data): ListDto
    {
        $dto = new ListDto();
        $dto->remoteId = $data['id'] ?? null;
        $dto->name = $data['name'] ?? null;

        return $dto;
    }
}
