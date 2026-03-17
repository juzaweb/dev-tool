<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use Juzaweb\DevTool\Tests\TestCase;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\Finder\SplFileInfo;

class PublishAgentsCommandTest extends TestCase
{
    protected MockInterface|Filesystem $files;
    protected string $baseSourcePath;
    protected string $destinationPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = Mockery::mock(Filesystem::class);
        $this->app->instance(Filesystem::class, $this->files);

        // The command class calculates the source directory like this:
        // dirname(__DIR__, 2) . '/agents' (where __DIR__ is src/Commands)
        $commandDir = realpath(__DIR__ . '/../../src/Commands'); // Changed from ../../../ to ../../ since the test moved up one directory
        $this->baseSourcePath = dirname($commandDir, 2) . '/agents';

        $this->destinationPath = base_path('.agent');
    }

    public function test_it_publishes_all_agents()
    {
        $this->files->shouldReceive('isDirectory')->with($this->baseSourcePath)->once()->andReturn(true);
        $this->files->shouldReceive('isDirectory')->with($this->destinationPath)->once()->andReturn(false);
        $this->files->shouldReceive('makeDirectory')->with($this->destinationPath, 0755, true)->once()->andReturn(true);

        $mockFile1 = Mockery::mock(SplFileInfo::class);
        $mockFile1->shouldReceive('getPathname')->andReturn($this->baseSourcePath . DIRECTORY_SEPARATOR . 'rules/test-rule.md');

        $this->files->shouldReceive('allFiles')->with($this->baseSourcePath)->once()->andReturn([$mockFile1]);

        $this->files->shouldReceive('directories')->with($this->baseSourcePath)->once()->andReturn([$this->baseSourcePath . DIRECTORY_SEPARATOR . 'rules']);
        $this->files->shouldReceive('directories')->with($this->baseSourcePath . DIRECTORY_SEPARATOR . 'rules')->once()->andReturn([]);

        $targetDir = $this->destinationPath . DIRECTORY_SEPARATOR . 'rules';
        $this->files->shouldReceive('isDirectory')->with($targetDir)->once()->andReturn(false);
        $this->files->shouldReceive('makeDirectory')->with($targetDir, 0755, true)->once()->andReturn(true);

        $targetPath = $this->destinationPath . DIRECTORY_SEPARATOR . 'rules/test-rule.md';
        $this->files->shouldReceive('exists')->with($targetPath)->once()->andReturn(false);
        $this->files->shouldReceive('copy')->with($this->baseSourcePath . DIRECTORY_SEPARATOR . 'rules/test-rule.md', $targetPath)->once()->andReturn(true);

        $this->artisan('agents:publish')
            ->expectsOutputToContain('Publishing dev-tool agents...')
            ->expectsOutputToContain('Agents published successfully!')
            ->assertExitCode(0);
    }

    public function test_it_publishes_only_skills()
    {
        $sourcePath = $this->baseSourcePath . DIRECTORY_SEPARATOR . 'skills';
        $targetDestination = $this->destinationPath . DIRECTORY_SEPARATOR . 'skills';

        $this->files->shouldReceive('isDirectory')->with($sourcePath)->once()->andReturn(true);
        $this->files->shouldReceive('isDirectory')->with($targetDestination)->once()->andReturn(false);
        $this->files->shouldReceive('makeDirectory')->with($targetDestination, 0755, true)->once()->andReturn(true);

        $mockFile1 = Mockery::mock(SplFileInfo::class);
        $mockFile1->shouldReceive('getPathname')->andReturn($sourcePath . DIRECTORY_SEPARATOR . 'test-skill.md');

        $this->files->shouldReceive('allFiles')->with($sourcePath)->once()->andReturn([$mockFile1]);
        $this->files->shouldReceive('directories')->with($sourcePath)->once()->andReturn([]);

        $targetPath = $targetDestination . DIRECTORY_SEPARATOR . 'test-skill.md';
        $this->files->shouldReceive('exists')->with($targetPath)->once()->andReturn(false);
        $this->files->shouldReceive('copy')->with($sourcePath . DIRECTORY_SEPARATOR . 'test-skill.md', $targetPath)->once()->andReturn(true);

        $this->artisan('agents:publish', ['--skills' => true])
            ->expectsOutputToContain('Publishing skills...')
            ->expectsOutputToContain('Agents published successfully!')
            ->assertExitCode(0);
    }

    public function test_it_publishes_only_rules()
    {
        $sourcePath = $this->baseSourcePath . DIRECTORY_SEPARATOR . 'rules';
        $targetDestination = $this->destinationPath . DIRECTORY_SEPARATOR . 'rules';

        $this->files->shouldReceive('isDirectory')->with($sourcePath)->once()->andReturn(true);
        $this->files->shouldReceive('isDirectory')->with($targetDestination)->once()->andReturn(true);

        $mockFile1 = Mockery::mock(SplFileInfo::class);
        $mockFile1->shouldReceive('getPathname')->andReturn($sourcePath . DIRECTORY_SEPARATOR . 'test-rule.md');

        $this->files->shouldReceive('allFiles')->with($sourcePath)->once()->andReturn([$mockFile1]);
        $this->files->shouldReceive('directories')->with($sourcePath)->once()->andReturn([]);

        $targetPath = $targetDestination . DIRECTORY_SEPARATOR . 'test-rule.md';
        $this->files->shouldReceive('exists')->with($targetPath)->once()->andReturn(false);
        $this->files->shouldReceive('copy')->with($sourcePath . DIRECTORY_SEPARATOR . 'test-rule.md', $targetPath)->once()->andReturn(true);

        $this->artisan('agents:publish', ['--rules' => true])
            ->expectsOutputToContain('Publishing rules...')
            ->expectsOutputToContain('Agents published successfully!')
            ->assertExitCode(0);
    }

    public function test_it_asks_for_confirmation_when_file_exists()
    {
        $this->files->shouldReceive('isDirectory')->with($this->baseSourcePath)->once()->andReturn(true);
        $this->files->shouldReceive('isDirectory')->with($this->destinationPath)->once()->andReturn(true);

        $mockFile1 = Mockery::mock(SplFileInfo::class);
        $mockFile1->shouldReceive('getPathname')->andReturn($this->baseSourcePath . DIRECTORY_SEPARATOR . 'test-rule.md');

        $this->files->shouldReceive('allFiles')->with($this->baseSourcePath)->once()->andReturn([$mockFile1]);
        $this->files->shouldReceive('directories')->with($this->baseSourcePath)->once()->andReturn([]);

        $targetPath = $this->destinationPath . DIRECTORY_SEPARATOR . 'test-rule.md';

        $this->files->shouldReceive('exists')->with($targetPath)->once()->andReturn(true);
        $this->files->shouldNotReceive('copy');

        $this->artisan('agents:publish')
            ->expectsConfirmation('File already exists: test-rule.md. Overwrite?', 'no')
            ->expectsOutputToContain('Agents published successfully!')
            ->assertExitCode(0);
    }

    public function test_it_forces_overwrite_when_file_exists()
    {
        $this->files->shouldReceive('isDirectory')->with($this->baseSourcePath)->once()->andReturn(true);
        $this->files->shouldReceive('isDirectory')->with($this->destinationPath)->once()->andReturn(true);

        $mockFile1 = Mockery::mock(SplFileInfo::class);
        $mockFile1->shouldReceive('getPathname')->andReturn($this->baseSourcePath . DIRECTORY_SEPARATOR . 'test-rule.md');

        $this->files->shouldReceive('allFiles')->with($this->baseSourcePath)->once()->andReturn([$mockFile1]);
        $this->files->shouldReceive('directories')->with($this->baseSourcePath)->once()->andReturn([]);

        $targetPath = $this->destinationPath . DIRECTORY_SEPARATOR . 'test-rule.md';

        $this->files->shouldReceive('exists')->with($targetPath)->once()->andReturn(true);
        $this->files->shouldReceive('copy')->with($this->baseSourcePath . DIRECTORY_SEPARATOR . 'test-rule.md', $targetPath)->once()->andReturn(true);

        $this->artisan('agents:publish', ['--force' => true])
            ->expectsOutputToContain('Agents published successfully!')
            ->assertExitCode(0);
    }

    public function test_it_returns_error_if_source_directory_does_not_exist()
    {
        $this->files->shouldReceive('isDirectory')->with($this->baseSourcePath)->once()->andReturn(false);

        $this->artisan('agents:publish')
            ->expectsOutputToContain("Source directory does not exist: {$this->baseSourcePath}")
            ->assertExitCode(1);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
