<?php

namespace Marktic\Newsletter\NewsletterFilters\Models;

use Marktic\Newsletter\Base\Models\Behaviours\HasId\RecordHasId;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypeInterface;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypes;
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

    public function getFilterType(): ?FilterTypeInterface
    {
        return FilterTypes::has($this->type) ? FilterTypes::get($this->type) : null;
    }

    public function getDescription(): string
    {
        $type = $this->getFilterType();
        if ($type === null) {
            return (string)$this->type . ': ' . json_encode($this->getValues());
        }
        return $type->describe((array)$this->getValues());
    }
}
