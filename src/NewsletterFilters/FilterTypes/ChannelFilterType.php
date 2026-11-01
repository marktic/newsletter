<?php

namespace Marktic\Newsletter\NewsletterFilters\FilterTypes;

use Marktic\Newsletter\NewsletterFilters\Exceptions\InvalidFilter;

/**
 * Only subscriptions that include at least one of the given channels.
 */
class ChannelFilterType extends AbstractFilterType
{
    public const NAME = 'channel';

    public function normalizeValues(mixed $values): array
    {
        $channels = [];
        foreach ($this->valuesToList($values) as $value) {
            if (!preg_match('/^[a-z0-9_-]+$/i', $value)) {
                throw new InvalidFilter(sprintf('Invalid channel [%s]', $value));
            }
            $channels[] = strtolower($value);
        }
        if (empty($channels)) {
            throw new InvalidFilter('At least one channel is required');
        }
        return array_values(array_unique($channels));
    }

    public function applyToQuery($query, array $values, string $table): void
    {
        $column = $this->column($table, 'channels');
        $conditions = [];
        foreach ($values as $channel) {
            $conditions[] = $query->getCondition('FIND_IN_SET(?, ' . $column . ') > 0', $channel);
        }
        $condition = array_shift($conditions);
        foreach ($conditions as $other) {
            $condition = $condition->or_($other);
        }
        if ($condition) {
            $query->where($condition);
        }
    }

    public function describe(array $values): string
    {
        return 'Channels: ' . implode(', ', $values);
    }
}
