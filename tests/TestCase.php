<?php

namespace Juzaweb\DevTool\Tests;

use Juzaweb\DevTool\Providers\DevToolServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            DevToolServiceProvider::class,
        ];
    }
}
