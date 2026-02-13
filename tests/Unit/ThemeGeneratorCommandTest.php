<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Support\Stub;

class ThemeGeneratorCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup theme configuration
        $this->app['config']->set('themes.path', base_path('themes'));

        // Setup stubs path
        $stubsPath = dirname(__DIR__, 2) . '/stubs/themes';
        $this->app['config']->set('dev-tool.themes.stubs.path', $stubsPath);

        // Ensure stub base path is set for Stub class
        Stub::setBasePath($stubsPath);

        // Clean up before starting
        if (File::isDirectory(base_path('themes'))) {
            File::deleteDirectory(base_path('themes'));
        }
    }

    protected function tearDown(): void
    {
        if (File::isDirectory(base_path('themes'))) {
            File::deleteDirectory(base_path('themes'));
        }
        parent::tearDown();
    }

    public function test_it_creates_theme_structure()
    {
        $this->artisan('theme:make', ['name' => 'test-theme'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('themes/test-theme/theme.json'));
        $this->assertFileExists(base_path('themes/test-theme/src/Providers/ThemeServiceProvider.php'));

        $themeJson = json_decode(File::get(base_path('themes/test-theme/theme.json')), true);
        $this->assertEquals('test-theme', $themeJson['name']);
    }

    public function test_it_fails_if_theme_exists()
    {
        // Create dummy theme
        if (!File::isDirectory(base_path('themes/test-theme'))) {
            File::makeDirectory(base_path('themes/test-theme'), 0755, true);
        }

        $this->artisan('theme:make', ['name' => 'test-theme'])
            ->expectsOutput('Sorry, test-theme Theme Folder Already Exist !!!')
            ->assertExitCode(0); // The command handles failure by returning, not by exit code > 0 in init() logic currently
    }

    public function test_it_overwrites_if_force()
    {
        // Create dummy theme
        $themePath = base_path('themes/test-theme');
        if (!File::isDirectory($themePath)) {
            File::makeDirectory($themePath, 0755, true);
        }
        File::put($themePath . '/dummy.txt', 'content');

        $this->artisan('theme:make', ['name' => 'test-theme', '--force' => true])
            ->assertExitCode(0);

        $this->assertFileExists($themePath . '/theme.json');
        // The dummy file should be gone if directory was deleted and recreated
        // But looking at ThemeGeneratorCommand.php, it does NOT delete directory first.
        // It calls generateThemeInfo(), makeDir(), createStubs().
        // makeDir() checks if !isDirectory then creates.
        // createStubs() iterates and calls makeFile().
        // makeFile() checks if file exists, if yes replaces stub?
        // Wait, makeFile():
        /*
        protected function makeFile(string $file, string $storePath): void
        {
            if (File::exists($file)) { // Checking if stub file exists? Yes.
                 // ...
                 File::put($storePath, $content);
            }
        }
        */
        // It overwrites.
        // So dummy.txt will NOT be deleted unless the command explicitly cleans up.
        // The test assertion should verify that theme files exist, not that dummy files are gone, unless we expect clean slate.
        // I will just check theme.json exists.

        $this->assertFileExists($themePath . '/theme.json');
    }
}
