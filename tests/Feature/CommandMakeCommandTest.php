<?php

namespace Juzaweb\DevTool\Tests\Feature;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Support\Stub;

class CommandMakeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));
        $this->app['config']->set('modules.paths.generator.command.path', 'Console');
        $this->app['config']->set('modules.paths.generator.command.generate', true);
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

    public function test_it_creates_command_file()
    {
        $this->artisan('module:make-command', ['name' => 'CreatePostCommand', 'module' => 'Blog'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/Console/CreatePostCommand.php'));

        $content = File::get(base_path('modules/Blog/Console/CreatePostCommand.php'));

        $this->assertStringContainsString('class CreatePostCommand extends Command', $content);
        $this->assertStringContainsString('protected $name = \'command:name\';', $content);
    }

    public function test_it_creates_command_file_with_custom_command_name()
    {
        $this->artisan('module:make-command', ['name' => 'AnotherCommand', 'module' => 'Blog', '--command' => 'blog:another'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/Console/AnotherCommand.php'));

        $content = File::get(base_path('modules/Blog/Console/AnotherCommand.php'));

        $this->assertStringContainsString('protected $name = \'blog:another\';', $content);
    }
}
