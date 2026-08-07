<?php

namespace Marktic\Newsletter\Providers\Newsman;

use Marktic\Newsletter\Lists\Dto\ListDto;
use Marktic\Newsletter\Providers\AbstractProvider\Contracts\ListTransformerInterface;

class NewsmanListTransformer implements ListTransformerInterface
{
    public function toProviderFormat(ListDto $list): array
    {
        return [
            'list_name' => $list->name,
        ];
    }

    public function fromProviderFormat(array $data): ListDto
    {
        $dto = new ListDto();
        $dto->remoteId = (string)($data['list_id'] ?? $data['id'] ?? '');
        $dto->name = $data['list_name'] ?? $data['name'] ?? null;

        return $dto;
    }
}
