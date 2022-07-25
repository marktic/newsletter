<?php

use Nip\Config\Config;
use Nip\Container\Container;
use Nip\Inflector\Inflector;

define('PROJECT_BASE_PATH', __DIR__ . '/..');
define('TEST_BASE_PATH', __DIR__);
define('TEST_FIXTURE_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'fixtures');

Container::setInstance(new Container());
Container::getInstance()->set('inflector', Inflector::instance());
Container::getInstance()->set('config', new Config([], true));

require dirname(__DIR__) . '/vendor/autoload.php';
