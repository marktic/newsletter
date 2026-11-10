<?php

namespace Marktic\Newsletter\NewsletterFilters\Actions;

use Marktic\Newsletter\NewsletterFilters\Exceptions\InvalidFilter;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypeInterface;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypes;
use Marktic\Newsletter\NewsletterFilters\Models\NewsletterFilter;
use Marktic\Newsletter\Utility\NewsletterModels;
use Nip\Records\AbstractModels\RecordManager;

/**
 * Validates and stores a filter for a newsletter.
 *
 * CreateNewsletterFilter::for($newsletter)->withType('list')->withValues([1, 2])->execute();
 */
class CreateNewsletterFilter
{
    protected $newsletter;
    protected ?string $type = null;
    protected mixed $values = null;
    protected ?RecordManager $repository = null;
    /** @var FilterTypeInterface[]|null */
    protected ?array $filterTypes = null;

    public static function for($newsletter): self
    {
        $action = new static();
        $action->newsletter = $newsletter;
        return $action;
    }

    public function withType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function withValues(mixed $values): self
    {
        $this->values = $values;
        return $this;
    }

    public function withRepository(RecordManager $repository): self
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @param array<string, FilterTypeInterface> $types
     */
    public function withFilterTypes(array $types): self
    {
        $this->filterTypes = $types;
        return $this;
    }

    /**
     * @return NewsletterFilter
     * @throws InvalidFilter
     */
    public function execute()
    {
        $newsletterId = $this->newsletter?->getId();
        if (empty($newsletterId)) {
            throw new InvalidFilter('A saved newsletter is required to add filters');
        }

        $types = $this->filterTypes ?? FilterTypes::forNewsletter($this->newsletter);
        if ($this->type === null || !isset($types[$this->type])) {
            throw new InvalidFilter(sprintf('Newsletter filter type [%s] is not available', (string)$this->type));
        }
        $type = $types[$this->type];
        $values = $type->normalizeValues($this->values);

        /** @var NewsletterFilter $filter */
        $filter = $this->getRepository()->getNew();
        $filter->setIdNewsletter((int)$newsletterId);
        $filter->setType($type->getName());
        $filter->setValues($values);
        $filter->save();

        return $filter;
    }

    protected function getRepository(): RecordManager
    {
        return $this->repository ??= NewsletterModels::newsletterFilters();
    }
}
