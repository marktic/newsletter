<?php

namespace Marktic\Newsletter\Newsletters\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasId\RecordHasId;
use Marktic\Newsletter\Base\Models\Behaviours\HasOwner\HasOwnerRecordTrait;
use Marktic\Newsletter\Base\Models\Behaviours\Timestampable\TimestampableTrait;
use Marktic\Newsletter\Lists\Models\NewsletterList;

/**
 * Trait NewsletterNewsletterTrait
 *
 * @method NewsletterList getNewsletterList()
 */
trait NewsletterNewsletterTrait
{
    use RecordHasId;
    use HasOwnerRecordTrait;
    use TimestampableTrait;

    protected ?string $uuid = null;
    protected ?string $type = null;
    protected ?int $list_id = null;
    protected ?string $name = null;
    protected ?string $subject = null;
    protected ?string $content = null;
    protected ?string $grapesjs_data = null;
    protected ?string $status = null;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_SENDING = 'sending';
    public const STATUS_SENT = 'sent';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SCHEDULED,
            self::STATUS_SENDING,
            self::STATUS_SENT,
        ];
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getListId(): ?int
    {
        return $this->list_id;
    }

    public function setListId(?int $list_id): void
    {
        $this->list_id = $list_id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getGrapesjsData(): ?string
    {
        return $this->grapesjs_data;
    }

    public function setGrapesjsData(?string $grapesjs_data): void
    {
        $this->grapesjs_data = $grapesjs_data;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    public function isSending(): bool
    {
        return $this->status === self::STATUS_SENDING;
    }

    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }
}
