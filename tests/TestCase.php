<?php

namespace Juzaweb\DevTool\Tests;

use Juzaweb\DevTool\Providers\DevToolServiceProvider;
use Juzaweb\Modules\Core\Facades\Module;
use Juzaweb\Modules\Core\Facades\Theme;
use Juzaweb\Modules\Core\Providers\CoreServiceProvider;
use Juzaweb\QueryCache\QueryCacheServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            QueryCacheServiceProvider::class,
            CoreServiceProvider::class,
            DevToolServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Theme' => Theme::class,
            'Module' => Module::class,
        ];
    }
}
