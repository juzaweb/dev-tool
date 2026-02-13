<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Support\Stub;

class ProviderMakeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));
        $this->app['config']->set('modules.paths.generator.provider.path', 'src/Providers');
        $this->app['config']->set('modules.paths.generator.provider.generate', true);

        $this->app['config']->set('dev-tool.modules.stubs.path', dirname(__DIR__, 2) . '/stubs/modules/');
        $this->app['config']->set('modules.stubs.path', dirname(__DIR__, 2) . '/stubs/modules/');

        Stub::setBasePath(dirname(__DIR__, 2) . '/stubs/modules/');

        // Create a dummy module
        if (!File::isDirectory(base_path('modules/Blog'))) {
            File::makeDirectory(base_path('modules/Blog'), 0755, true);
        }

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
        parent::tearDown();
    }

    public function test_it_creates_provider_file()
    {
        $this->artisan('module:make-provider', ['name' => 'PostServiceProvider', 'module' => 'Blog'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Providers/PostServiceProvider.php'));

        $content = File::get(base_path('modules/Blog/src/Providers/PostServiceProvider.php'));

        $this->assertStringContainsString('class PostServiceProvider extends ServiceProvider', $content);
    }

    public function test_it_creates_master_provider_file()
    {
        // We need to set up some paths for master provider stub rendering
        $this->app['config']->set('modules.paths.generator.views.path', 'src/resources/views');
        $this->app['config']->set('modules.paths.generator.lang.path', 'src/resources/lang');
        $this->app['config']->set('modules.paths.generator.config.path', 'src/config');
        $this->app['config']->set('modules.paths.generator.migration.path', 'database/migrations');
        $this->app['config']->set('modules.paths.generator.factory.path', 'database/factories');

        $this->artisan('module:make-provider', ['name' => 'BlogServiceProvider', 'module' => 'Blog', '--master' => true])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Providers/BlogServiceProvider.php'));

        $content = File::get(base_path('modules/Blog/src/Providers/BlogServiceProvider.php'));

        $this->assertStringContainsString('class BlogServiceProvider extends ServiceProvider', $content);
    }
}
