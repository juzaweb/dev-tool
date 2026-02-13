<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Commands\Modules\ModelShowCommand;
use Juzaweb\DevTool\Tests\TestCase;
use ReflectionMethod;

class ModelShowCommandTest extends TestCase
{
    protected string $originalCwd;
    protected string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a temporary directory for testing
        $this->originalCwd = getcwd();
        $this->tempDir = sys_get_temp_dir() . '/juzaweb_test_' . uniqid();

        if (!file_exists($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }

        // Change CWD to temp dir so glob works relatively
        chdir($this->tempDir);

        // Setup module configuration
        $this->app['config']->set('modules.namespace', 'modules');
        $this->app['config']->set('dev-tool.modules.paths.generator.model.path', 'src/Models');

        // Create a dummy module in the temp dir
        // Since we chdir'd, 'modules' is relative to tempDir
        $modulesPath = $this->tempDir . '/modules';

        if (!File::isDirectory($modulesPath . '/Blog/src/Models')) {
            File::makeDirectory($modulesPath . '/Blog/src/Models', 0755, true);
        }

        // Create a dummy model file
        $content = <<<PHP
<?php

namespace modules\Blog\src\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
}
PHP;
        File::put($modulesPath . '/Blog/src/Models/Post.php', $content);
    }

    protected function tearDown(): void
    {
        // Restore CWD
        chdir($this->originalCwd);

        // Cleanup temp dir
        if (File::isDirectory($this->tempDir)) {
            File::deleteDirectory($this->tempDir);
        }

        parent::tearDown();
    }

    public function test_qualify_model_finds_model_in_module()
    {
        $command = new ModelShowCommand();
        $command->setLaravel($this->app);

        $method = new ReflectionMethod(ModelShowCommand::class, 'qualifyModel');
        $method->setAccessible(true);

        $result = $method->invoke($command, 'Post');

        $this->assertEquals('modules\Blog\src\Models\Post', $result);
    }

    public function test_qualify_model_returns_fully_qualified_class_if_exists()
    {
        $command = new ModelShowCommand();
        $command->setLaravel($this->app);

        $method = new ReflectionMethod(ModelShowCommand::class, 'qualifyModel');
        $method->setAccessible(true);

        // Use the test class itself as it definitely exists
        $result = $method->invoke($command, self::class);

        $this->assertEquals(self::class, $result);
    }

    public function test_qualify_model_returns_original_if_not_found()
    {
        $command = new ModelShowCommand();
        $command->setLaravel($this->app);

        $method = new ReflectionMethod(ModelShowCommand::class, 'qualifyModel');
        $method->setAccessible(true);

        $result = $method->invoke($command, 'NonExistentModel');

        $this->assertEquals('NonExistentModel', $result);
    }
}
