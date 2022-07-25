<?php

namespace Marktic\Newsletter\Consents\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasId\RecordHasId;
use Marktic\Newsletter\Base\Models\Behaviours\HasOwner\HasOwnerRecordTrait;
use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableTrait;

/**
 * Trait NewsletterConsentTrait
 */
trait NewsletterConsentTrait
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
