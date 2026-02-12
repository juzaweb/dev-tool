<?php

namespace Juzaweb\DevTool\Tests;

use Juzaweb\DevTool\Providers\DevToolServiceProvider;
use Juzaweb\Modules\Core\Providers\CoreServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            CoreServiceProvider::class,
            DevToolServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Theme' => \Juzaweb\Modules\Core\Facades\Theme::class,
            'Module' => \Juzaweb\Modules\Core\Facades\Module::class,
        ];
    }
}
