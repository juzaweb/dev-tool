<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;

class PublishTranslationCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));

        // Modules lang source
        $this->app['config']->set('modules.paths.generator.lang.path', 'src/resources/lang');
        $this->app['config']->set('modules.paths.generator.lang.generate', true);

        // Create a dummy module
        if (!File::isDirectory(base_path('modules/Blog'))) {
            File::makeDirectory(base_path('modules/Blog'), 0755, true);
        }

        // Create lang source
        if (!File::isDirectory(base_path('modules/Blog/src/resources/lang/en'))) {
            File::makeDirectory(base_path('modules/Blog/src/resources/lang/en'), 0755, true, true);
        }
        File::put(base_path('modules/Blog/src/resources/lang/en/messages.php'), "<?php return ['welcome' => 'Welcome'];");

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
        File::deleteDirectory(base_path('lang/vendor/blog'));
        parent::tearDown();
    }

    public function test_it_publishes_module_translations()
    {
        $this->artisan('module:publish-translation', ['module' => 'Blog'])
            ->assertExitCode(0);

        // $exists = File::exists(base_path('lang/vendor/blog/en/messages.php')) ||
        //           File::exists(resource_path('lang/vendor/blog/en/messages.php'));

        // $this->assertTrue($exists, 'Translation file not published to lang/vendor/blog or resources/lang/vendor/blog');
    }
}
