<?php

namespace Marktic\Newsletter\Lists\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasId\RecordHasId;
use Marktic\Newsletter\Base\Models\Behaviours\HasOwner\HasOwnerRecordTrait;
use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableTrait;

/**
 * Trait NewsletterListTrait
 */
trait NewsletterListTrait
{
    use RecordHasId;
    use HasOwnerRecordTrait;
    use TimestampableTrait;


    protected ?string $name = null;

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

}
