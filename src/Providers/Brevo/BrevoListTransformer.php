<?php

namespace Marktic\Newsletter\Providers\Brevo;

use Marktic\Newsletter\Lists\Dto\ListDto;
use Marktic\Newsletter\Providers\AbstractProvider\Contracts\ListTransformerInterface;

class BrevoListTransformer implements ListTransformerInterface
{
    public function toProviderFormat(ListDto $list): array
    {
        return [
            'name' => $list->name,
            'folderId' => 1,
        ];
    }

    public function fromProviderFormat(array $data): ListDto
    {
        $dto = new ListDto();
        $dto->remoteId = (string)($data['id'] ?? '');
        $dto->name = $data['name'] ?? null;

        return $dto;
    }
}
