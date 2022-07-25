<?php

namespace Marktic\Newsletter\Base\Actions\Behaviours;

use Marktic\Newsletter\Base\Exceptions\NewsletterException;

trait HasOwner
{
    protected ?string $owner = null;
    protected ?int $owner_id = null;

    public function forOwner($owner = null, $owner_id = null): static
    {
        $this->owner = $owner;
        $this->owner_id = $owner_id;
        return $this;
    }

    protected function checkOwnerWithArray(array &$data): void
    {
        $data['owner'] = $data['owner'] ?? $this->owner;
        $data['owner_id'] = $data['owner_id'] ?? $this->owner_id;

        if (empty($data['owner_id']) || empty($data['owner'])) {
            throw new NewsletterException("Missing owner in newsletter data");
        }
    }
}