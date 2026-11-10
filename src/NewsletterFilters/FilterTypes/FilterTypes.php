<?php

namespace Marktic\Newsletter\NewsletterFilters\FilterTypes;

use Marktic\Newsletter\NewsletterFilters\Exceptions\InvalidFilter;
use Marktic\Newsletter\Utility\PackageConfig;

/**
 * Registry of available newsletter filter types.
 * Extra types can be registered via the `filters.types` config key.
 */
class FilterTypes
{
    public const DEFAULTS = [
        ListFilterType::class,
        ChannelFilterType::class,
        SubscribedAfterFilterType::class,
        SubscribedBeforeFilterType::class,
    ];

    /**
     * @var FilterTypeInterface[]|null
     */
    protected static ?array $types = null;

    /**
     * @return FilterTypeInterface[]
     */
    public static function all(): array
    {
        if (static::$types === null) {
            static::$types = [];
            foreach (static::configuredClasses() as $class) {
                static::register(is_object($class) ? $class : new $class());
            }
        }
        return static::$types;
    }

    public static function register(FilterTypeInterface $type): void
    {
        static::$types ??= [];
        static::$types[$type->getName()] = $type;
    }

    public static function has(?string $name): bool
    {
        return $name !== null && isset(static::all()[$name]);
    }

    /**
     * @throws InvalidFilter
     */
    public static function get(?string $name): FilterTypeInterface
    {
        if (!static::has($name)) {
            throw new InvalidFilter(sprintf('Unknown newsletter filter type [%s]', (string)$name));
        }
        return static::all()[$name];
    }

    /**
     * @internal
     */
    public static function reset(): void
    {
        static::$types = null;
    }

    protected static function configuredClasses(): array
    {
        try {
            $configured = PackageConfig::filterTypes();
        } catch (\Throwable) {
            $configured = [];
        }
        return array_merge(static::DEFAULTS, $configured);
    }
}
