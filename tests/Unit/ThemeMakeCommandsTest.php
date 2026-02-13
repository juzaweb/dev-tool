<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Support\Stub;
use Juzaweb\Modules\Core\Facades\Theme;

class ThemeMakeCommandsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup theme configuration
        $this->app['config']->set('themes.path', base_path('themes'));

        $stubsPath = dirname(__DIR__, 2) . '/stubs/themes';
        $this->app['config']->set('dev-tool.themes.stubs.path', $stubsPath);

        // Ensure stub base path is set for Stub class
        Stub::setBasePath($stubsPath);

        // Clean up before starting
        if (File::isDirectory(base_path('themes'))) {
            File::deleteDirectory(base_path('themes'));
        }

        // Mock the Theme creation part by running the command
        $this->artisan('theme:make', ['name' => 'test-theme']);

        // Clear the Theme repository cache so it sees the newly created theme
        $this->app->forgetInstance(\Juzaweb\Modules\Core\Contracts\Theme::class);
        Theme::clearResolvedInstance(\Juzaweb\Modules\Core\Contracts\Theme::class);
    }

    protected function tearDown(): void
    {
        if (File::isDirectory(base_path('themes'))) {
            File::deleteDirectory(base_path('themes'));
        }
        parent::tearDown();
    }

    public function test_it_creates_controller()
    {
        $this->artisan('theme:make-controller', [
            'name' => 'TestController',
            'theme' => 'test-theme',
        ])->assertExitCode(0);

        $this->assertFileExists(base_path('themes/test-theme/src/Http/Controllers/TestController.php'));
    }

    public function test_it_creates_template()
    {
        $this->artisan('theme:make-template', [
            'name' => 'custom-page',
            'theme' => 'test-theme',
        ])->assertExitCode(0);

        $this->assertFileExists(base_path('themes/test-theme/src/resources/views/templates/custom-page.blade.php'));

        // Verify StyleServiceProvider update
        $providerPath = base_path('themes/test-theme/src/Providers/StyleServiceProvider.php');
        $this->assertFileExists($providerPath);

        $providerContent = File::get($providerPath);
        $this->assertStringContainsString("PageTemplate::make(", $providerContent);
        $this->assertStringContainsString("'custom-page',", $providerContent);
    }

    public function test_it_fails_create_template_home()
    {
         $this->artisan('theme:make-template', [
            'name' => 'home',
            'theme' => 'test-theme',
        ])->assertExitCode(1);
    }

    public function test_it_creates_view()
    {
        $this->artisan('theme:make-view', [
            'name' => 'test-view',
            'theme' => 'test-theme',
        ])->assertExitCode(0);

        $this->assertFileExists(base_path('themes/test-theme/src/resources/views/test-view.blade.php'));
    }

    public function test_it_creates_widget()
    {
        $this->artisan('theme:make-widget', [
            'name' => 'TestWidget',
            'theme' => 'test-theme',
        ])->assertExitCode(0);

        $this->assertFileExists(base_path('themes/test-theme/src/resources/views/components/widgets/testwidget/show.blade.php'));
        $this->assertFileExists(base_path('themes/test-theme/src/resources/views/components/widgets/testwidget/form.blade.php'));

        // Verify ThemeServiceProvider update
        $providerPath = base_path('themes/test-theme/src/Providers/ThemeServiceProvider.php');
        $this->assertFileExists($providerPath);

        $providerContent = File::get($providerPath);
        $this->assertStringContainsString("Widget::make(", $providerContent);
        $this->assertStringContainsString("'testwidget',", $providerContent);
    }

    public function test_it_creates_block()
    {
        $this->artisan('theme:make-block', [
            'name' => 'TestBlock',
            'theme' => 'test-theme',
        ])->assertExitCode(0);

        $this->assertFileExists(base_path('themes/test-theme/src/resources/views/components/blocks/testblock/view.blade.php'));
        $this->assertFileExists(base_path('themes/test-theme/src/resources/views/components/blocks/testblock/form.blade.php'));

        // Verify StyleServiceProvider update
        $providerPath = base_path('themes/test-theme/src/Providers/StyleServiceProvider.php');
        $this->assertFileExists($providerPath);

        $providerContent = File::get($providerPath);
        $this->assertStringContainsString("PageBlock::make(", $providerContent);
        $this->assertStringContainsString("'testblock',", $providerContent);
    }
}
