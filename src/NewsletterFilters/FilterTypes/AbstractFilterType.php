<?php

namespace Marktic\Newsletter\NewsletterFilters\FilterTypes;

use Marktic\Newsletter\NewsletterFilters\Exceptions\InvalidFilter;

abstract class AbstractFilterType implements FilterTypeInterface
{
    public const NAME = '';

    public function getName(): string
    {
        return static::NAME;
    }

    public function getLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->getName()));
    }

    protected function valuesToList(mixed $values): array
    {
        if (is_string($values)) {
            $values = preg_split('/[\s,;]+/', $values);
        }
        if (!is_array($values)) {
            throw new InvalidFilter(sprintf('Invalid values for filter [%s]', $this->getName()));
        }

        $values = array_map(static fn($value) => is_scalar($value) ? trim((string)$value) : '', $values);
        return array_values(array_unique(array_filter($values, static fn($value) => $value !== '')));
    }

    protected function column(string $table, string $column): string
    {
        return '`' . $table . '`.`' . $column . '`';
    }
}
