<?php

namespace Marktic\Newsletter\ConsentStatements\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasId\RecordHasId;
use Marktic\Newsletter\Base\Models\Behaviours\HasOwner\HasOwnerRecordTrait;
use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableTrait;

/**
 * Trait NewsletterConsentTrait
 */
trait NewsletterConsentStatementTrait
{
    use RecordHasId;
    use TimestampableTrait;

    protected ?string $hash = null;
    protected ?string $text = null;

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
        $this->updateHash();
    }

    /**
     * @return string
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }

    protected function updateHash(): void
    {
        $this->hash = md5($this->text);
    }
}
