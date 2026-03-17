<?php

namespace Juzaweb\DevTool\Tests\Unit\Commands;

use Illuminate\Support\Facades\File;
use Juzaweb\DevTool\Tests\TestCase;

class PublishAgentsCommandTest extends TestCase
{
    protected string $baseSourcePath;
    protected string $destinationPath;

    protected function setUp(): void
    {
        parent::setUp();

        // The path in PublishAgentsCommand is:
        // $baseSourcePath = dirname(__DIR__, 2) . '/agents';
        // In the app container, the agents are located at /app/agents
        // The command will resolve dirname(__DIR__, 2) internally
        // We set up baseSourcePath to explicitly use the existing agents directory for our tests to check files
        $this->baseSourcePath = '/app/agents';

        $this->destinationPath = base_path('.agent');

        // Ensure destination base_path() is clean
        if (File::isDirectory($this->destinationPath)) {
            File::deleteDirectory($this->destinationPath);
        }
    }

    public function test_it_publishes_all_agents()
    {
        // Add a dummy file to ensure it's there
        if (!File::isDirectory($this->baseSourcePath . '/rules')) {
            File::makeDirectory($this->baseSourcePath . '/rules', 0755, true);
        }
        File::put($this->baseSourcePath . '/rules/test-rule.md', 'Dummy Rule');

        $this->artisan('agents:publish')
            ->expectsOutputToContain('Publishing dev-tool agents...')
            ->expectsOutputToContain('Agents published successfully!')
            ->assertExitCode(0);

        $this->assertDirectoryExists($this->destinationPath);
        $this->assertDirectoryExists($this->destinationPath . '/rules');
        $this->assertFileExists($this->destinationPath . '/rules/test-rule.md');
    }

    public function test_it_publishes_only_skills()
    {
        // For testing purpose, since we know 'rules' exists in /app/agents,
        // we'll create a dummy 'skills' dir so the publish command has something to work with.
        if (!File::isDirectory($this->baseSourcePath . '/skills')) {
            File::makeDirectory($this->baseSourcePath . '/skills', 0755, true);
        }
        File::put($this->baseSourcePath . '/skills/test-skill.md', 'Dummy Skill');

        $this->artisan('agents:publish', ['--skills' => true])
            ->expectsOutputToContain('Publishing skills...')
            ->expectsOutputToContain('Agents published successfully!')
            ->assertExitCode(0);

        $this->assertDirectoryExists($this->destinationPath . '/skills');
        $this->assertFileExists($this->destinationPath . '/skills/test-skill.md');
        $this->assertFileDoesNotExist($this->destinationPath . '/rules/test-rule.md');

        // Clean up dummy
        File::deleteDirectory($this->baseSourcePath . '/skills');
    }

    public function test_it_publishes_only_rules()
    {
        if (!File::isDirectory($this->baseSourcePath . '/rules')) {
            File::makeDirectory($this->baseSourcePath . '/rules', 0755, true);
        }
        File::put($this->baseSourcePath . '/rules/test-rule.md', 'Dummy Rule');

        $this->artisan('agents:publish', ['--rules' => true])
            ->expectsOutputToContain('Publishing rules...')
            ->expectsOutputToContain('Agents published successfully!')
            ->assertExitCode(0);

        $this->assertDirectoryExists($this->destinationPath . '/rules');
        $this->assertFileExists($this->destinationPath . '/rules/test-rule.md');
    }

    public function test_it_asks_for_confirmation_when_file_exists()
    {
        if (!File::isDirectory($this->baseSourcePath . '/rules')) {
            File::makeDirectory($this->baseSourcePath . '/rules', 0755, true);
        }
        File::put($this->baseSourcePath . '/rules/test-rule.md', 'Dummy Rule');

        // First publish
        $this->artisan('agents:publish', ['--rules' => true])
            ->assertExitCode(0);

        // Second publish, should ask for confirmation
        // Note: the command outputs info using components, so it just asks using confirm
        $this->artisan('agents:publish', ['--rules' => true])
            ->expectsConfirmation('File already exists: test-rule.md. Overwrite?', 'no')
            ->expectsOutputToContain('Agents published successfully!')
            ->assertExitCode(0);
    }

    public function test_it_forces_overwrite_when_file_exists()
    {
        if (!File::isDirectory($this->baseSourcePath . '/rules')) {
            File::makeDirectory($this->baseSourcePath . '/rules', 0755, true);
        }
        File::put($this->baseSourcePath . '/rules/test-rule.md', 'Dummy Rule');

        // First publish
        $this->artisan('agents:publish', ['--rules' => true])
            ->assertExitCode(0);

        // Modify the published file
        File::put($this->destinationPath . '/rules/test-rule.md', 'Modified Rule');

        // Second publish with force option
        $this->artisan('agents:publish', ['--rules' => true, '--force' => true])
            ->expectsOutputToContain('Agents published successfully!')
            ->assertExitCode(0);

        // Check if file was overwritten (should contain original content or at least not be our 'Modified Rule')
        $this->assertNotEquals('Modified Rule', File::get($this->destinationPath . '/rules/test-rule.md'));
    }

    public function test_it_returns_error_if_source_directory_does_not_exist()
    {
        // The command uses dirname(__DIR__, 2) . '/agents' to find the source.
        // During testing, __DIR__ in the command might resolve to the symlinked package directory in vendor if this is run as a package.
        // Let's write a small temporary class mock or just mock the Filesystem.
        // Mocking the filesystem is easier since the command expects a Filesystem object injection.

        $mockFilesystem = \Mockery::mock(\Illuminate\Filesystem\Filesystem::class);
        $mockFilesystem->shouldReceive('isDirectory')->andReturn(false);
        $this->app->instance(\Illuminate\Filesystem\Filesystem::class, $mockFilesystem);

        // Get the path the command tries to check (dirname(__DIR__, 2) . '/agents' relative to the command class)
        // Which is usually /app/agents or wherever the command file is located relative to
        $expectedPath = dirname(__DIR__, 3) . '/src/Commands';
        // We don't know the exact string, but it should output the "Source directory does not exist" message.

        $this->artisan('agents:publish')
            ->expectsOutputToContain("Source directory does not exist:")
            ->assertExitCode(1);

        // Clean up mock
        \Mockery::close();
    }

    protected function tearDown(): void
    {
        if (File::isDirectory($this->destinationPath)) {
            File::deleteDirectory($this->destinationPath);
        }

        parent::tearDown();
    }
}
