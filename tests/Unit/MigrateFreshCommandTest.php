<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;

class MigrateFreshCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));

        // Create a dummy module
        if (!File::isDirectory(base_path('modules/Blog'))) {
            File::makeDirectory(base_path('modules/Blog'), 0755, true);
        }

        // Create module.json
        File::put(base_path('modules/Blog/module.json'), json_encode([
            'name' => 'Blog',
            'alias' => 'blog',
            'description' => 'Blog module',
            'keywords' => [],
            'active' => 1,
            'order' => 0,
            'providers' => [],
            'aliases' => [],
            'files' => [],
            'requires' => []
        ]));

        File::put(base_path('modules_statuses.json'), json_encode(['Blog' => true]));

        $this->app['modules']->scan();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path('modules'));
        if (File::exists(base_path('modules_statuses.json'))) {
            File::delete(base_path('modules_statuses.json'));
        }
        parent::tearDown();
    }

    public function test_it_migrates_fresh_module()
    {
        $this->artisan('module:migrate-fresh', ['module' => 'Blog'])
             ->assertExitCode(0);
    }
}
