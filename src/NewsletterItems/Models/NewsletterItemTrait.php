<?php

namespace Marktic\Newsletter\NewsletterItems\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasId\RecordHasId;
use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;

/**
 * Trait NewsletterItemTrait
 *
 * @method NewsletterNewsletter getNewsletterNewsletter()
 */
trait NewsletterItemTrait
{
    use RecordHasId;

    protected ?int $id_newsletter = null;
    protected ?int $record_id = null;
    protected ?string $record_type = null;
    protected ?string $status = null;

    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_ERROR = 'error';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_SENT,
            self::STATUS_ERROR,
        ];
    }

    public function getIdNewsletter(): ?int
    {
        return $this->id_newsletter;
    }

    public function setIdNewsletter(?int $id_newsletter): void
    {
        $this->id_newsletter = $id_newsletter;
    }

    public function getRecordId(): ?int
    {
        return $this->record_id;
    }

    public function setRecordId(?int $record_id): void
    {
        $this->record_id = $record_id;
    }

    public function getRecordType(): ?string
    {
        return $this->record_type;
    }

    public function setRecordType(?string $record_type): void
    {
        $this->record_type = $record_type;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    public function isError(): bool
    {
        return $this->status === self::STATUS_ERROR;
    }
}
