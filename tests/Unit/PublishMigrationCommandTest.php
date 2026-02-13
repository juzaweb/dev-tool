<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;

class PublishMigrationCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));

        // Modules migrations source
        $this->app['config']->set('modules.paths.generator.migration.path', 'database/migrations');
        $this->app['config']->set('modules.paths.generator.migration.generate', true);

        // Create a dummy module
        if (!File::isDirectory(base_path('modules/Blog'))) {
            File::makeDirectory(base_path('modules/Blog'), 0755, true);
        }

        // Create migration source
        if (!File::isDirectory(base_path('modules/Blog/database/migrations'))) {
            File::makeDirectory(base_path('modules/Blog/database/migrations'), 0755, true);
        }
        File::put(base_path('modules/Blog/database/migrations/2023_01_01_000000_create_posts_table.php'), '<?php class CreatePostsTable {}');

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
        if (File::exists(base_path('database/migrations/2023_01_01_000000_create_posts_table.php'))) {
            File::delete(base_path('database/migrations/2023_01_01_000000_create_posts_table.php'));
        }
        parent::tearDown();
    }

    public function test_it_publishes_module_migrations()
    {
        $this->artisan('module:publish-migration', ['module' => 'Blog'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('database/migrations/2023_01_01_000000_create_posts_table.php'));
    }
}
