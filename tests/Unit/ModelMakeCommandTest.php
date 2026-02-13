<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Support\Stub;

class ModelMakeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));

        // Model configuration
        $this->app['config']->set('modules.paths.generator.model.path', 'src/Models');
        $this->app['config']->set('modules.paths.generator.model.generate', true);
        $this->app['config']->set('dev-tool.modules.paths.generator.model.namespace', 'Models');

        // Migration configuration
        $this->app['config']->set('modules.paths.generator.migration.path', 'database/migrations');
        $this->app['config']->set('modules.paths.generator.migration.generate', true);

        // Controller configuration
        $this->app['config']->set('modules.paths.generator.controller.path', 'src/Http/Controllers');
        $this->app['config']->set('modules.paths.generator.controller.generate', true);
        $this->app['config']->set('modules.paths.generator.controller.namespace', 'Http\\Controllers');

        // Seeder configuration
        $this->app['config']->set('modules.paths.generator.seeder.path', 'database/seeders');
        $this->app['config']->set('modules.paths.generator.seeder.generate', true);
        $this->app['config']->set('modules.paths.generator.seeder.namespace', 'Database\\Seeders');

        // Request configuration
        $this->app['config']->set('modules.paths.generator.request.path', 'src/Http/Requests');
        $this->app['config']->set('modules.paths.generator.request.generate', true);
        $this->app['config']->set('modules.paths.generator.request.namespace', 'Http\\Requests');

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

    public function test_it_creates_model_file()
    {
        $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Models/Post.php'));

        $content = File::get(base_path('modules/Blog/src/Models/Post.php'));

        $this->assertStringContainsString('class Post extends Model', $content);
        $this->assertStringContainsString('namespace Juzaweb\Modules\Blog\Models;', $content);
    }

    public function test_it_creates_model_with_fillable()
    {
        $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog', '--fillable' => 'title,content'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Models/Post.php'));

        $content = File::get(base_path('modules/Blog/src/Models/Post.php'));

        $this->assertStringContainsString('protected $fillable = ["title","content"];', $content);
    }

    public function test_it_creates_model_with_migration()
    {
        $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog', '--migration' => true])
            ->assertExitCode(0);

        $files = File::files(base_path('modules/Blog/database/migrations'));
        $this->assertCount(1, $files);
        $this->assertStringContainsString('create_posts_table', $files[0]->getFilename());
    }

    public function test_it_creates_model_with_controller()
    {
        $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog', '--controller' => true])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Http/Controllers/PostController.php'));

        $content = File::get(base_path('modules/Blog/src/Http/Controllers/PostController.php'));
        $this->assertStringContainsString('class PostController extends AdminController', $content);
    }

    public function test_it_creates_model_with_seeder()
    {
        $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog', '--seed' => true])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/database/seeders/PostSeederTableSeeder.php'));

        $content = File::get(base_path('modules/Blog/database/seeders/PostSeederTableSeeder.php'));
        $this->assertStringContainsString('class PostSeederTableSeeder extends Seeder', $content);
    }

    public function test_it_creates_model_with_request()
    {
        $this->artisan('module:make-model', ['model' => 'Post', 'module' => 'Blog', '--request' => true])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Http/Requests/PostRequest.php'));

        $content = File::get(base_path('modules/Blog/src/Http/Requests/PostRequest.php'));
        $this->assertStringContainsString('class PostRequest extends FormRequest', $content);
    }
}
