<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Support\Stub;
use Juzaweb\Modules\Core\Models\Model;

class CrudMakeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));
        $this->app['config']->set('dev-tool.modules.namespace', 'Juzaweb');

        // Generator configuration
        $this->app['config']->set('modules.paths.generator.datatable.path', 'src/Http/DataTables');
        $this->app['config']->set('modules.paths.generator.datatable.generate', true);
        $this->app['config']->set('modules.paths.generator.datatable.namespace', 'Http\\DataTables');

        $this->app['config']->set('modules.paths.generator.controller.path', 'src/Http/Controllers');
        $this->app['config']->set('modules.paths.generator.controller.generate', true);
        $this->app['config']->set('modules.paths.generator.controller.namespace', 'Http\\Controllers');

        $this->app['config']->set('modules.paths.generator.request.path', 'src/Http/Requests');
        $this->app['config']->set('modules.paths.generator.request.generate', true);
        $this->app['config']->set('modules.paths.generator.request.namespace', 'Http\\Requests');

        $this->app['config']->set('modules.paths.generator.views.path', 'src/resources/views');
        $this->app['config']->set('modules.paths.generator.views.generate', true);

        $this->app['config']->set('modules.paths.generator.routes.path', 'src/routes');
        $this->app['config']->set('modules.paths.generator.routes.generate', true);

        // Stubs path
        $stubsPath = dirname(__DIR__, 2) . '/stubs/modules/';
        $this->app['config']->set('dev-tool.modules.stubs.path', $stubsPath);
        $this->app['config']->set('modules.stubs.path', $stubsPath);

        Stub::setBasePath($stubsPath);

        // Clean up before starting
        if (File::isDirectory(base_path('modules'))) {
            File::deleteDirectory(base_path('modules'));
        }

        // Create a dummy module
        File::makeDirectory(base_path('modules/Blog'), 0755, true);

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

        // Create routes file
        File::makeDirectory(base_path('modules/Blog/src/routes'), 0755, true);
        File::put(base_path('modules/Blog/src/routes/admin.php'), "<?php\n\nuse Illuminate\Support\Facades\Route;\n");

        // Create dummy Model
        File::makeDirectory(base_path('modules/Blog/src/Models'), 0755, true);
        $modelPath = base_path('modules/Blog/src/Models/Post.php');

        File::put($modelPath, "<?php
namespace Juzaweb\Modules\Blog\Models;

use Juzaweb\Modules\Core\Models\Model;

class Post extends Model
{
    protected \$fillable = ['title', 'content'];
}
");

        // Manually load the model
        require_once $modelPath;
    }

    protected function tearDown(): void
    {
        if (File::isDirectory(base_path('modules'))) {
            File::deleteDirectory(base_path('modules'));
        }
        parent::tearDown();
    }

    public function test_it_creates_crud_files()
    {
        $this->artisan('module:make-crud', ['model' => 'Post', 'module' => 'Blog'])
            ->assertExitCode(0);

        // Assert Controller
        $this->assertFileExists(base_path('modules/Blog/src/Http/Controllers/PostController.php'));

        // Assert Request
        $this->assertFileExists(base_path('modules/Blog/src/Http/Requests/PostRequest.php'));
        $this->assertFileExists(base_path('modules/Blog/src/Http/Requests/PostActionsRequest.php'));

        // Assert DataTable
        $this->assertFileExists(base_path('modules/Blog/src/Http/DataTables/PostsDataTable.php'));

        // Assert Views
        $this->assertFileExists(base_path('modules/Blog/src/resources/views/post/index.blade.php'));
        $this->assertFileExists(base_path('modules/Blog/src/resources/views/post/form.blade.php'));

        // Assert Routes
        $routeContent = File::get(base_path('modules/Blog/src/routes/admin.php'));
        $this->assertStringContainsString("Route::admin('posts', PostController::class);", $routeContent);
        $this->assertStringContainsString("use Juzaweb\Modules\Blog\Http\Controllers\PostController;", $routeContent);
    }

    public function test_it_creates_api_crud_files()
    {
        $this->artisan('module:make-crud', ['model' => 'Post', 'module' => 'Blog', '--api' => true])
            ->assertExitCode(0);

        // Assert API Controller
        $this->assertFileExists(base_path('modules/Blog/src/Http/Controllers/APIs/PostController.php'));
    }
}
