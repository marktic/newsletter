<?php

namespace Marktic\Newsletter;

use ByTIC\PackageBase\BaseBootableServiceProvider;
use Marktic\Newsletter\Utility\PackageConfig;

/**
 * Class NewsletterServiceProvider
 * @package ByTIC\NotifierBuilder
 */
class NewsletterServiceProvider extends BaseBootableServiceProvider
{
    public const NAME = 'mkt_newsletter';


    public function migrations(): ?string
    {
        if (PackageConfig::shouldRunMigrations()) {
            return dirname(__DIR__) . '/database/migrations/';
        }

        return null;
    }

    protected function translationsPath(): string
    {
        return __DIR__ . '/Bundle/Resources/lang/';
    }
}
