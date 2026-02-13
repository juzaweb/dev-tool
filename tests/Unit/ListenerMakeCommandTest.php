<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Support\Stub;

class ListenerMakeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));
        $this->app['config']->set('modules.paths.generator.listener.path', 'src/Listeners');
        $this->app['config']->set('modules.paths.generator.listener.generate', true);
        $this->app['config']->set('dev-tool.modules.paths.generator.event', ['path' => 'src/Events', 'generate' => false, 'namespace' => 'Events']);
        $this->app['config']->set('dev-tool.modules.stubs.path', dirname(__DIR__, 2) . '/stubs/modules/');
        $this->app['config']->set('modules.stubs.path', dirname(__DIR__, 2) . '/stubs/modules/');
        $this->app['config']->set('modules.namespace', 'Modules');

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

    public function test_it_creates_listener()
    {
        $this->artisan('module:make-listener', ['name' => 'NotifyUserListener', 'module' => 'Blog'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Listeners/NotifyUserListener.php'));

        $content = File::get(base_path('modules/Blog/src/Listeners/NotifyUserListener.php'));

        $this->assertStringContainsString('class NotifyUserListener', $content);
        $this->assertStringContainsString('namespace Modules\Blog\Listeners;', $content);
        $this->assertStringContainsString('public function handle($event)', $content);
    }

    public function test_it_creates_listener_with_event_option()
    {
        $this->artisan('module:make-listener', ['name' => 'NotifyUserListener', 'module' => 'Blog', '--event' => 'UserRegistered'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Listeners/NotifyUserListener.php'));

        $content = File::get(base_path('modules/Blog/src/Listeners/NotifyUserListener.php'));

        $this->assertStringContainsString('class NotifyUserListener', $content);
        $this->assertStringContainsString('namespace Modules\Blog\Listeners;', $content);
        $this->assertStringContainsString('use Modules\Blog\Events\UserRegistered;', $content);
        $this->assertStringContainsString('public function handle(UserRegistered $event)', $content);
    }

    public function test_it_creates_queued_listener()
    {
        $this->artisan('module:make-listener', ['name' => 'NotifyUserListener', 'module' => 'Blog', '--queued' => true])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Listeners/NotifyUserListener.php'));

        $content = File::get(base_path('modules/Blog/src/Listeners/NotifyUserListener.php'));

        $this->assertStringContainsString('class NotifyUserListener implements ShouldQueue', $content);
        $this->assertStringContainsString('use Illuminate\Contracts\Queue\ShouldQueue;', $content);
        $this->assertStringContainsString('use InteractsWithQueue;', $content);
    }

    public function test_it_creates_queued_listener_with_event_option()
    {
        $this->artisan('module:make-listener', ['name' => 'NotifyUserListener', 'module' => 'Blog', '--queued' => true, '--event' => 'UserRegistered'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Listeners/NotifyUserListener.php'));

        $content = File::get(base_path('modules/Blog/src/Listeners/NotifyUserListener.php'));

        $this->assertStringContainsString('class NotifyUserListener implements ShouldQueue', $content);
        $this->assertStringContainsString('use Illuminate\Contracts\Queue\ShouldQueue;', $content);
        $this->assertStringContainsString('use InteractsWithQueue;', $content);
        $this->assertStringContainsString('use Modules\Blog\Events\UserRegistered;', $content);
        $this->assertStringContainsString('public function handle(UserRegistered $event)', $content);
    }
}
