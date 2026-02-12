<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;
use Juzaweb\Modules\Core\Modules\Support\Stub;

class JobMakeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Setup module configuration
        $this->app['config']->set('modules.paths.modules', base_path('modules'));
        $this->app['config']->set('modules.namespace', 'Modules');
        $this->app['config']->set('modules.paths.generator.jobs.path', 'src/Jobs');
        $this->app['config']->set('modules.paths.generator.jobs.generate', true);

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

    public function test_it_creates_job_file()
    {
        $this->artisan('module:make-job', ['name' => 'ProcessPodcast', 'module' => 'Blog'])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Jobs/ProcessPodcast.php'));

        $content = File::get(base_path('modules/Blog/src/Jobs/ProcessPodcast.php'));

        $this->assertStringContainsString('class ProcessPodcast implements ShouldQueue', $content);
        $this->assertStringContainsString('namespace Modules\Blog\Jobs;', $content);
    }

    public function test_it_creates_sync_job_file()
    {
        $this->artisan('module:make-job', ['name' => 'SyncJob', 'module' => 'Blog', '--sync' => true])
            ->assertExitCode(0);

        $this->assertFileExists(base_path('modules/Blog/src/Jobs/SyncJob.php'));

        $content = File::get(base_path('modules/Blog/src/Jobs/SyncJob.php'));

        $this->assertStringContainsString('class SyncJob', $content);
        $this->assertStringNotContainsString('implements ShouldQueue', $content);
        $this->assertStringContainsString('namespace Modules\Blog\Jobs;', $content);
    }
}
