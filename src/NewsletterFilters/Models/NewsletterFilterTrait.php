<?php

namespace Marktic\Newsletter\NewsletterFilters\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasId\RecordHasId;
use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;

/**
 * Trait NewsletterFilterTrait
 *
 * @method NewsletterNewsletter getNewsletterNewsletter()
 */
trait NewsletterFilterTrait
{
    use RecordHasId;

    protected ?int $id_newsletter = null;
    protected ?string $type = null;
    protected ?string $values = null;

    public function getIdNewsletter(): ?int
    {
        return $this->id_newsletter;
    }

    public function setIdNewsletter(?int $id_newsletter): void
    {
        $this->id_newsletter = $id_newsletter;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getValues(): ?array
    {
        if ($this->values === null) {
            return null;
        }
        return json_decode($this->values, true);
    }

    public function setValues($values): void
    {
        if (is_array($values)) {
            $this->values = json_encode($values);
        } else {
            $this->values = $values;
        }
    }
}
