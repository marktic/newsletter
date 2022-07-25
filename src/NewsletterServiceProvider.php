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

    public function register()
    {
        parent::register();
        $this->registerResources();
    }

    public function migrations(): ?string
    {
        if (PackageConfig::shouldRunMigrations()) {
            return dirname(__DIR__) . '/database/migrations/';
        }

        return null;
    }

    protected function registerResources()
    {
        if (false === $this->getContainer()->has('translator')) {
            return;
        }
        $translator = $this->getContainer()->get('translator');
        $folder = __DIR__ . '/Bundle/Resources/lang/';
        $languages = $this->getContainer()->get('translation.languages');


        foreach ($languages as $language) {
            $path = $folder . $language;
            if (is_dir($path)) {
                $translator->addResource('php', $path, $language);
            }
        }
    }

    protected function registerCommands()
    {
//        $this->commands(
//        );
    }

    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return array_merge(
            [
            ],
            parent::provides()
        );
    }
}
