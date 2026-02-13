<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Contracts\ActivatorInterface;
use Juzaweb\Modules\Core\Modules\Support\Stub;

class ModuleMakeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(ActivatorInterface::class, function ($mock) {
            $mock->shouldReceive('enable')->andReturn(true);
            $mock->shouldReceive('disable')->andReturn(true);
            $mock->shouldReceive('hasStatus')->andReturn(true);
            $mock->shouldReceive('setActiveByName')->andReturn(true);
            $mock->shouldReceive('delete')->andReturn(true);
            $mock->shouldReceive('reset')->andReturn(true);
        });

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));
        $this->app['config']->set('dev-tool.modules.namespace', 'Juzaweb');

        // Setup stubs path
        $stubsPath = dirname(__DIR__, 2) . '/stubs/modules/';
        $this->app['config']->set('dev-tool.modules.stubs.path', $stubsPath);
        $this->app['config']->set('modules.stubs.path', $stubsPath);

        Stub::setBasePath($stubsPath);

        // Clean up before starting
        if (File::isDirectory(base_path('modules'))) {
            File::deleteDirectory(base_path('modules'));
        }
    }

    protected function tearDown(): void
    {
        if (File::isDirectory(base_path('modules'))) {
            File::deleteDirectory(base_path('modules'));
        }
        parent::tearDown();
    }

    public function test_it_creates_module()
    {
        $this->artisan('module:make', ['name' => ['Blog']])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/blog/module.json'));
        $this->assertFileExists(base_path('modules/blog/src/Providers/BlogServiceProvider.php'));

        $moduleJson = json_decode(File::get(base_path('modules/blog/module.json')), true);
        $this->assertEquals('Blog', $moduleJson['name']);
        // Check if provider is registered in module.json
        $this->assertContains('Juzaweb\\Modules\\Blog\\Providers\\BlogServiceProvider', $moduleJson['providers']);
    }

    public function test_it_creates_module_plain()
    {
        $this->artisan('module:make', ['name' => ['PlainModule'], '--plain' => true])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/plain-module/module.json'));
        $this->assertFileDoesNotExist(base_path('modules/plain-module/src/Providers/PlainModuleServiceProvider.php'));

        $moduleJson = json_decode(File::get(base_path('modules/plain-module/module.json')), true);
        $this->assertEquals('PlainModule', $moduleJson['name']);
        // Check if provider is NOT registered in module.json
        $this->assertNotContains('Juzaweb\\PlainModule\\Providers\\PlainModuleServiceProvider', $moduleJson['providers']);
    }

    public function test_it_creates_module_api()
    {
        $this->artisan('module:make', ['name' => ['ApiModule'], '--api' => true])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/api-module/module.json'));
        // API module should still have service provider
        $this->assertFileExists(base_path('modules/api-module/src/Providers/ApiModuleServiceProvider.php'));
    }

    public function test_it_fails_if_module_exists()
    {
        // Create a dummy module
        if (!File::isDirectory(base_path('modules/blog'))) {
            File::makeDirectory(base_path('modules/blog'), 0755, true);
        }
        File::put(base_path('modules/blog/module.json'), json_encode(['name' => 'Blog', 'alias' => 'blog', 'providers' => [], 'files' => []]));

        $this->artisan('module:make', ['name' => ['Blog']])
            ->assertExitCode(1);
    }

    public function test_it_overwrites_if_force()
    {
        // Create a dummy module
        if (!File::isDirectory(base_path('modules/blog'))) {
            File::makeDirectory(base_path('modules/blog'), 0755, true);
        }
        File::put(base_path('modules/blog/module.json'), json_encode(['name' => 'Blog', 'alias' => 'blog', 'providers' => [], 'files' => []]));
        // Create a dummy file to verify overwrite
        File::put(base_path('modules/blog/dummy.txt'), 'content');

        $this->artisan('module:make', ['name' => ['Blog'], '--force' => true])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/blog/module.json'));
        // The dummy file should be gone if directory was deleted and recreated
        $this->assertFileDoesNotExist(base_path('modules/blog/dummy.txt'));

        $moduleJson = json_decode(File::get(base_path('modules/blog/module.json')), true);
        $this->assertEquals('Blog', $moduleJson['name']);
    }
}
