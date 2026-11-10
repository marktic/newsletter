<?php

namespace Marktic\Newsletter\NewsletterFilters\FilterTypes;

use Marktic\Newsletter\NewsletterFilters\Exceptions\InvalidFilter;

/**
 * Only subscriptions belonging to one of the given lists.
 */
class ListFilterType extends AbstractFilterType
{
    public const NAME = 'list';

    public function normalizeValues(mixed $values): array
    {
        $ids = [];
        foreach ($this->valuesToList($values) as $value) {
            if (!ctype_digit($value) || (int)$value < 1) {
                throw new InvalidFilter(sprintf('Invalid list id [%s]', $value));
            }
            $ids[] = (int)$value;
        }
        if (empty($ids)) {
            throw new InvalidFilter('At least one list is required');
        }
        return array_values(array_unique($ids));
    }

    public function applyToQuery($query, array $values, string $table): void
    {
        $query->where($this->column($table, 'list_id') . ' IN ?', array_map('intval', $values));
    }

    public function describe(array $values): string
    {
        return 'Lists: ' . implode(', ', $values);
    }
}
