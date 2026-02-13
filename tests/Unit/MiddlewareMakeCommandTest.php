<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Support\Stub;

class MiddlewareMakeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));

        // Middleware configuration
        $this->app['config']->set('modules.paths.generator.filter.path', 'src/Http/Middleware');
        $this->app['config']->set('modules.paths.generator.filter.generate', true);

        // Stubs path
        $this->app['config']->set('dev-tool.modules.stubs.path', dirname(__DIR__, 2) . '/stubs/modules/');
        $this->app['config']->set('modules.stubs.path', dirname(__DIR__, 2) . '/stubs/modules/');

        Stub::setBasePath(dirname(__DIR__, 2) . '/stubs/modules/');

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
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path('modules'));
        parent::tearDown();
    }

    public function test_it_creates_middleware_file()
    {
        $this->artisan('module:make-middleware', ['name' => 'CheckUserRole', 'module' => 'Blog'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Http/Middleware/CheckUserRole.php'));

        $content = File::get(base_path('modules/Blog/src/Http/Middleware/CheckUserRole.php'));

        $this->assertStringContainsString('class CheckUserRole', $content);
        $this->assertStringContainsString('namespace Juzaweb\Modules\Blog\Http\Middleware;', $content);
    }
}
