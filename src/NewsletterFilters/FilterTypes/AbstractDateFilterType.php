<?php

namespace Marktic\Newsletter\NewsletterFilters\FilterTypes;

use DateTimeImmutable;
use Marktic\Newsletter\NewsletterFilters\Exceptions\InvalidFilter;

abstract class AbstractDateFilterType extends AbstractFilterType
{
    protected const OPERATOR = '>=';

    public function normalizeValues(mixed $values): array
    {
        if (is_array($values)) {
            $values = $values['date'] ?? reset($values);
        }
        $value = is_scalar($values) ? trim((string)$values) : '';
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if ($date === false || $date->format('Y-m-d') !== $value) {
            throw new InvalidFilter(sprintf('Invalid date [%s], expected format YYYY-MM-DD', $value));
        }
        return ['date' => $value];
    }

    public function applyToQuery($query, array $values, string $table): void
    {
        $query->where('DATE(' . $this->column($table, 'created_at') . ') ' . static::OPERATOR . ' ?', $values['date']);
    }
}
