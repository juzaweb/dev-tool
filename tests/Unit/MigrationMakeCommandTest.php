<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Support\Stub;

class MigrationMakeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));

        // Migration configuration
        $this->app['config']->set('modules.paths.generator.migration.path', 'database/migrations');
        $this->app['config']->set('modules.paths.generator.migration.generate', true);

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

    public function test_it_creates_migration_file_plain()
    {
        $this->artisan('module:make-migration', ['name' => 'some_migration', 'module' => 'Blog'])
            ->assertExitCode(0);

        $files = File::files(base_path('modules/Blog/database/migrations'));
        $this->assertCount(1, $files);
        $file = $files[0];
        $this->assertStringContainsString('some_migration', $file->getFilename());

        $content = File::get($file->getPathname());
        $this->assertStringContainsString('return new class extends Migration', $content);
        $this->assertStringContainsString('public function up()', $content);
        $this->assertStringContainsString('public function down()', $content);
    }

    public function test_it_creates_migration_file_create()
    {
        $this->artisan('module:make-migration', [
            'name' => 'create_posts_table',
            'module' => 'Blog',
            '--fields' => 'title:string, body:text'
        ])
            ->assertExitCode(0);

        $files = File::files(base_path('modules/Blog/database/migrations'));
        $this->assertCount(1, $files);
        $file = $files[0];
        $this->assertStringContainsString('create_posts_table', $file->getFilename());

        $content = File::get($file->getPathname());
        $this->assertStringContainsString('return new class extends Migration', $content);
        $this->assertStringContainsString('Schema::create(\'posts\', function (Blueprint $table) {', $content);
        $this->assertStringContainsString('$table->string(\'title\');', $content);
        $this->assertStringContainsString('$table->text(\'body\');', $content);
    }

    public function test_it_creates_migration_file_add()
    {
        $this->artisan('module:make-migration', [
            'name' => 'add_active_to_posts_table',
            'module' => 'Blog',
            '--fields' => 'active:boolean'
        ])
            ->assertExitCode(0);

        $files = File::files(base_path('modules/Blog/database/migrations'));
        $this->assertCount(1, $files);
        $file = $files[0];
        $this->assertStringContainsString('add_active_to_posts_table', $file->getFilename());

        $content = File::get($file->getPathname());
        $this->assertStringContainsString('return new class extends Migration', $content);
        $this->assertStringContainsString('Schema::table(\'posts\', function (Blueprint $table) {', $content);
        $this->assertStringContainsString('$table->boolean(\'active\');', $content);
    }

    public function test_it_creates_migration_file_delete()
    {
        $this->artisan('module:make-migration', [
            'name' => 'remove_title_from_posts_table',
            'module' => 'Blog',
            '--fields' => 'title:string'
        ])
            ->assertExitCode(0);

        $files = File::files(base_path('modules/Blog/database/migrations'));
        $this->assertCount(1, $files);
        $file = $files[0];
        $this->assertStringContainsString('remove_title_from_posts_table', $file->getFilename());

        $content = File::get($file->getPathname());
        $this->assertStringContainsString('return new class extends Migration', $content);
        $this->assertStringContainsString('Schema::table(\'posts\', function (Blueprint $table) {', $content);
        $this->assertStringContainsString('$table->dropColumn(\'title\');', $content);
    }

    public function test_it_creates_migration_file_drop()
    {
        $this->artisan('module:make-migration', [
            'name' => 'drop_posts_table',
            'module' => 'Blog'
        ])
            ->assertExitCode(0);

        $files = File::files(base_path('modules/Blog/database/migrations'));
        $this->assertCount(1, $files);
        $file = $files[0];
        $this->assertStringContainsString('drop_posts_table', $file->getFilename());

        $content = File::get($file->getPathname());
        $this->assertStringContainsString('return new class extends Migration', $content);
        $this->assertStringContainsString('Schema::dropIfExists(\'posts\');', $content);
    }
}
