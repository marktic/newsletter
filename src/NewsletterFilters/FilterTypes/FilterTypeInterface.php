<?php

namespace Marktic\Newsletter\NewsletterFilters\FilterTypes;

use Marktic\Newsletter\NewsletterFilters\Exceptions\InvalidFilter;

/**
 * A newsletter filter type restricts which subscriptions receive a newsletter.
 */
interface FilterTypeInterface
{
    public function getName(): string;

    public function getLabel(): string;

    /**
     * Validates and normalizes raw input (e.g. request data) into the values stored on the filter.
     *
     * @throws InvalidFilter
     */
    public function normalizeValues(mixed $values): array;

    /**
     * Restricts a subscriptions select query using the stored filter values.
     *
     * @param \Nip\Database\Query\Select $query
     */
    public function applyToQuery($query, array $values, string $table): void;

    public function describe(array $values): string;
}
