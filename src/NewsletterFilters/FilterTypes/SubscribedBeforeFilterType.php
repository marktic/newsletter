<?php

namespace Marktic\Newsletter\NewsletterFilters\FilterTypes;

/**
 * Only subscriptions created on or before the given date.
 */
class SubscribedBeforeFilterType extends AbstractDateFilterType
{
    public const NAME = 'subscribed_before';
    protected const OPERATOR = '<=';

    public function describe(array $values): string
    {
        return 'Subscribed on or before ' . ($values['date'] ?? '');
    }
}
