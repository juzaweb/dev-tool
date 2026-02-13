<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;

class PublishCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));
        $this->app['config']->set('modules.paths.assets', base_path('public/modules'));
        $this->app['config']->set('modules.paths.generator.assets.path', 'assets/public');
        $this->app['config']->set('modules.paths.generator.assets.generate', true);

        // Create a dummy module
        if (!File::isDirectory(base_path('modules/Blog'))) {
            File::makeDirectory(base_path('modules/Blog'), 0755, true);
        }

        // Create assets directory in module
        if (!File::isDirectory(base_path('modules/Blog/assets/public'))) {
            File::makeDirectory(base_path('modules/Blog/assets/public'), 0755, true);
        }
        File::put(base_path('modules/Blog/assets/public/style.css'), 'body { color: red; }');

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
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path('modules'));
        File::deleteDirectory(base_path('public/modules'));
        parent::tearDown();
    }

    public function test_it_publishes_module_assets()
    {
        $this->artisan('module:publish', ['module' => 'Blog'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('public/modules/blog/style.css'));
    }
}
