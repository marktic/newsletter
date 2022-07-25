<?php

namespace Marktic\Newsletter\Base\Models\Behaviours\HasOwner;

use Nip\Records\Record;

/**
 * @method Record getNewsletterOwner()
 */
trait HasOwnerRecordTrait
{

    protected ?string $owner = null;

    protected ?int $owner_id = null;

    /**
     * @return string|null
     */
    public function getOwner(): ?string
    {
        return $this->owner;
    }

    /**
     * @param string|null $owner
     */
    public function setOwner(?string $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return int|null
     */
    public function getOwnerId(): ?int
    {
        return $this->owner_id;
    }

    /**
     * @param int|null $owner_id
     */
    public function setOwnerId(?int $owner_id): void
    {
        $this->owner_id = $owner_id;
    }
}
