<?php

namespace Marktic\Newsletter\Utility;

use Exception;
use Marktic\Newsletter\NewsletterServiceProvider;
use Nip\Utility\Traits\SingletonTrait;

/**
 * Class PackageConfig
 * @package ByTIC\PackageBase\Utility
 */
class PackageConfig extends \ByTIC\PackageBase\Utility\PackageConfig
{
    use SingletonTrait;

    protected $name = NewsletterServiceProvider::NAME;

    public static function configPath(): string
    {
        return __DIR__ . '/../../config/mkt_newsletter.php';
    }

    public static function tableName($name, $default = null)
    {
        return static::instance()->get('tables.' . $name, $default);
    }

    public static function defaultCurrencyCode($default = null)
    {
        return static::instance()->get('currencies.default', $default);
    }

    public static function rulesCondition($default = [])
    {
        return static::instance()->get('rules.conditions', $default);
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public static function databaseConnection(): ?string
    {
        return (string)static::instance()->get('database.connection');
    }

    public static function shouldRunMigrations(): bool
    {
        return static::instance()->get('database.migrations', false) !== false;
    }

    public static function editorDriver(): string
    {
        return (string)static::instance()->get('editor.driver', 'grapesjs');
    }

    public static function editorOptions(): array
    {
        return (array)static::instance()->get('editor.options', []);
    }

    /**
     * Extra newsletter filter type classes (implementing FilterTypeInterface).
     */
    public static function filterTypes(): array
    {
        return (array)static::instance()->get('filters.types', []);
    }

    /**
     * Class name (or container id) of the PreviewEmailSenderInterface implementation.
     */
    public static function previewEmailSender(): ?string
    {
        $sender = static::instance()->get('preview.sender');
        return $sender ? (string)$sender : null;
    }

    public static function previewEmailFrom(): ?string
    {
        $from = static::instance()->get('preview.from');
        return $from ? (string)$from : null;
    }

    /**
     * Reset the singleton instance.
     *
     * Intended for use in tests only, to ensure each test reads a fresh
     * copy of the configuration from the container.
     *
     * @internal
     */
    public static function resetInstance(): void
    {
        static::$instance = null;
    }
}
