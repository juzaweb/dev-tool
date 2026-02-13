<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Support\Stub;

class NotificationMakeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));
        $this->app['config']->set('modules.paths.generator.notifications.path', 'src/Notifications');
        $this->app['config']->set('modules.paths.generator.notifications.generate', true);
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

    public function test_it_creates_notification_file()
    {
        $this->artisan('module:make-notification', ['name' => 'NewNotification', 'module' => 'Blog'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Notifications/NewNotification.php'));

        $content = File::get(base_path('modules/Blog/src/Notifications/NewNotification.php'));

        $this->assertStringContainsString('class NewNotification extends Notification', $content);
        $this->assertStringContainsString('namespace Juzaweb\Modules\Blog\Notifications;', $content);
    }

    public function test_it_creates_notification_file_with_custom_namespace()
    {
        $this->app['config']->set('dev-tool.modules.paths.generator.notifications.namespace', 'Custom\Notifications');

        $this->artisan('module:make-notification', ['name' => 'CustomNotification', 'module' => 'Blog'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Notifications/CustomNotification.php'));

        $content = File::get(base_path('modules/Blog/src/Notifications/CustomNotification.php'));

        $this->assertStringContainsString('namespace Juzaweb\Modules\Blog\Custom\Notifications;', $content);
    }
}
