<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Support\Stub;

class SeedMakeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));

        // Seeder configuration
        $this->app['config']->set('modules.paths.generator.seeder.path', 'database/seeders');
        $this->app['config']->set('modules.paths.generator.seeder.generate', true);
        $this->app['config']->set('dev-tool.modules.paths.generator.seeder.namespace', 'Database\\Seeders');

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

    public function test_it_creates_seeder_file()
    {
        $this->artisan('module:make-seed', ['name' => 'Post', 'module' => 'Blog'])
            ->assertExitCode(0);

        // Expect PostTableSeeder
        $this->assertFileExists(base_path('modules/Blog/database/seeders/PostTableSeeder.php'));

        $content = File::get(base_path('modules/Blog/database/seeders/PostTableSeeder.php'));

        $this->assertStringContainsString('class PostTableSeeder extends Seeder', $content);
        $this->assertStringContainsString('namespace Juzaweb\Modules\Blog\Database\Seeders;', $content);
    }

    public function test_it_creates_master_seeder_file()
    {
        $this->artisan('module:make-seed', ['name' => 'Blog', 'module' => 'Blog', '--master' => true])
            ->assertExitCode(0);

        // Expect BlogDatabaseSeeder
        $this->assertFileExists(base_path('modules/Blog/database/seeders/BlogDatabaseSeeder.php'));

        $content = File::get(base_path('modules/Blog/database/seeders/BlogDatabaseSeeder.php'));

        $this->assertStringContainsString('class BlogDatabaseSeeder extends Seeder', $content);
        $this->assertStringContainsString('namespace Juzaweb\Modules\Blog\Database\Seeders;', $content);
    }
}
