<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Support\Stub;

class ComponentClassMakeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));

        // component-class configuration
        $this->app['config']->set('modules.paths.generator.component-class.path', 'View/Components');
        $this->app['config']->set('modules.paths.generator.component-class.generate', true);
        $this->app['config']->set('dev-tool.modules.paths.generator.component-class.namespace', 'View\\Components');

        // component-view configuration
        $this->app['config']->set('modules.paths.generator.component-view.path', 'resources/views/components');
        $this->app['config']->set('modules.paths.generator.component-view.generate', true);

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

    public function test_it_creates_component_class_and_view()
    {
        $this->artisan('module:make-component', ['name' => 'Alert', 'module' => 'Blog'])
            ->assertExitCode(0);

        // Assert Component Class
        $this->assertFileExists(base_path('modules/Blog/src/View/Components/Alert.php'));
        $classContent = File::get(base_path('modules/Blog/src/View/Components/Alert.php'));

        $this->assertStringContainsString('class Alert extends Component', $classContent);
        $this->assertStringContainsString("return view('blog::components.alert');", $classContent);

        // Assert Component View
        $this->assertFileExists(base_path('modules/Blog/src/resources/views/components/alert.blade.php'));
        $viewContent = File::get(base_path('modules/Blog/src/resources/views/components/alert.blade.php'));

        $this->assertStringContainsString('<div>', $viewContent);
    }
}
