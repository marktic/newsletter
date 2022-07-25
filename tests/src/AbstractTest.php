<?php

namespace Marktic\Newsletter\Tests;

use Bytic\Phpqa\PHPUnit\TestCase;
use Marktic\Newsletter\NewsletterServiceProvider;
use Mockery;
use Nip\Config\Config;
use Nip\Container\Utility\Container;

/**
 * Class AbstractTest
 */
abstract class AbstractTest extends TestCase
{

    protected function loadConfig($data = [])
    {
        $config = config();
        $configNew = new Config(['mkt_promotion' => $data], true);
        Container::container()->set('config', $config->merge($configNew));
    }

    protected function loadConfigFromFixture($name)
    {
        $config = require TEST_FIXTURE_PATH . '/config/' . $name . '.php';
        $this->loadConfig($config);
    }

    protected function loadServiceProvider(): NewsletterServiceProvider
    {
        $container = Container::container();
        $provider = new NewsletterServiceProvider();
        $provider->setContainer($container);
        $provider->register();
        return $provider;
    }

    protected function loadFakeTranslator()
    {
        $translator = Mockery::mock('translator');
        $translator->shouldReceive('trans')->andReturnArg(0);

        $container = Container::container();
        $container->set('translator', $translator);
    }
}
