<?php

namespace Marktic\Newsletter\NewsletterFilters\FilterTypes;

/**
 * Only subscriptions created on or after the given date.
 */
class SubscribedAfterFilterType extends AbstractDateFilterType
{
    public const NAME = 'subscribed_after';
    protected const OPERATOR = '>=';

    public function describe(array $values): string
    {
        return 'Subscribed on or after ' . ($values['date'] ?? '');
    }
}
