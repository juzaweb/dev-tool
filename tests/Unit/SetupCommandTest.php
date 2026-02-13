<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;

class SetupCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));
        $this->app['config']->set('modules.paths.assets', base_path('public/modules'));
    }

    protected function tearDown(): void
    {
        if (File::isDirectory(base_path('modules'))) {
            File::deleteDirectory(base_path('modules'));
        }
        if (File::isDirectory(base_path('public/modules'))) {
            File::deleteDirectory(base_path('public/modules'));
        }
        parent::tearDown();
    }

    public function test_it_creates_modules_and_assets_directories()
    {
        // Ensure directories do not exist
        if (File::isDirectory(base_path('modules'))) {
            File::deleteDirectory(base_path('modules'));
        }
        if (File::isDirectory(base_path('public/modules'))) {
            File::deleteDirectory(base_path('public/modules'));
        }

        $this->artisan('module:setup')
            ->assertExitCode(0);

        $this->assertDirectoryExists(base_path('modules'));
        $this->assertDirectoryExists(base_path('public/modules'));
    }
}
