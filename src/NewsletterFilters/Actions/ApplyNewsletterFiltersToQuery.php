<?php

namespace Marktic\Newsletter\NewsletterFilters\Actions;

use Marktic\Newsletter\NewsletterFilters\Exceptions\InvalidFilter;
use Marktic\Newsletter\NewsletterFilters\FilterTypes\FilterTypes;
use Marktic\Newsletter\NewsletterFilters\Models\NewsletterFilter;
use Marktic\Newsletter\Utility\NewsletterModels;

/**
 * Restricts a subscriptions select query with all filters of a newsletter (combined with AND).
 */
class ApplyNewsletterFiltersToQuery
{
    /**
     * @var NewsletterFilter[]
     */
    protected iterable $filters = [];
    protected ?string $table = null;

    /**
     * @param NewsletterFilter[] $filters
     */
    public static function for(iterable $filters): self
    {
        $action = new static();
        $action->filters = $filters;
        return $action;
    }

    public function withTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param \Nip\Database\Query\Select $query
     * @return \Nip\Database\Query\Select
     * @throws InvalidFilter
     */
    public function apply($query)
    {
        $table = $this->table ?? NewsletterModels::subscriptions()->getTable();
        foreach ($this->filters as $filter) {
            FilterTypes::get($filter->getType())->applyToQuery($query, (array)$filter->getValues(), $table);
        }
        return $query;
    }
}
