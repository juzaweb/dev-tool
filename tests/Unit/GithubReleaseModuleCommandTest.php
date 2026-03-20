<?php

namespace Juzaweb\DevTool\Tests\Unit;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Juzaweb\DevTool\Commands\GithubReleaseModuleCommand;
use Juzaweb\DevTool\Tests\TestCase;
use Mockery;

class GithubReleaseModuleCommandTest extends TestCase
{
    public function test_nothing_to_release()
    {
        $command = Mockery::mock(GithubReleaseModuleCommand::class.'[runCmd,getGithubToken]')
            ->shouldAllowMockingProtectedMethods();

        $command->shouldReceive('getGithubToken')->andReturn('fake-token');

        // Mock getRepo and getLastTag outputs
        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return is_string($cmd) && str_contains($cmd, 'git ls-remote --tags');
            })
            ->andReturn('refs/tags/v1.0.0');

        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return is_string($cmd) && str_contains($cmd, 'git config --get remote.origin.url');
            })
            ->andReturn('fake/repo');

        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return $cmd === 'git pull';
            })
            ->andReturn('');

        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return is_string($cmd) && str_contains($cmd, 'git log');
            })
            ->andReturn('');

        $this->app[Kernel::class]->registerCommand($command);

        File::shouldReceive('isDirectory')->andReturn(true);

        $this->artisan('github:release', [
            'path' => '/fake/path',
        ])
            ->expectsOutput('Nothing to release')
            ->assertExitCode(0);
    }

    public function test_release_with_changelog()
    {
        $command = Mockery::mock(GithubReleaseModuleCommand::class.'[runCmd,getGithubToken]')
            ->shouldAllowMockingProtectedMethods();

        $command->shouldReceive('getGithubToken')->andReturn('fake-token');

        // Mock getRepo and getLastTag outputs
        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return is_string($cmd) && str_contains($cmd, 'git ls-remote --tags');
            })
            ->andReturn('refs/tags/v1.0.0');

        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return is_string($cmd) && str_contains($cmd, 'git config --get remote.origin.url');
            })
            ->andReturn('fake/repo');

        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return $cmd === 'git pull';
            })
            ->andReturn('');

        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return is_string($cmd) && str_contains($cmd, 'git log');
            })
            ->andReturn("* Feature 1\n* Feature 2\n* :construction: WIP"); // :construction: should be filtered out

        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return $cmd === 'git add changelog.md';
            })
            ->andReturn('');

        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return is_string($cmd) && str_contains($cmd, 'git commit -o changelog.md');
            })
            ->andReturn('');

        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return $cmd === 'git push';
            })
            ->andReturn('');

        $this->app[Kernel::class]->registerCommand($command);

        File::shouldReceive('isDirectory')->andReturn(true);
        File::shouldReceive('prepend')
            ->withArgs(function ($path, $content) {
                return str_contains($path, 'changelog.md') && str_contains($content, 'v1.0.1') && str_contains($content, 'Feature 1') && ! str_contains($content, ':construction:');
            })
            ->once();

        Http::fake([
            'https://api.github.com/repos/fake/repo/releases' => Http::response(['html_url' => 'https://github.com/fake/repo/releases/tag/1.0.1'], 201),
        ]);

        $this->artisan('github:release', [
            'path' => '/fake/path',
        ])
            ->expectsOutput('Add changelog')
            ->expectsOutput('Release v1.0.1')
            ->expectsOutput('Released url: https://github.com/fake/repo/releases/tag/1.0.1')
            ->assertExitCode(0);

        Http::assertSent(function ($request) {
            return $request->url() == 'https://api.github.com/repos/fake/repo/releases' &&
                   $request['tag_name'] == '1.0.1' &&
                   $request['body'] == "* Feature 1\n* Feature 2" &&
                   $request->header('Authorization')[0] == 'Bearer fake-token';
        });
    }

    public function test_release_without_changelog()
    {
        $command = Mockery::mock(GithubReleaseModuleCommand::class.'[runCmd,getGithubToken]')
            ->shouldAllowMockingProtectedMethods();

        $command->shouldReceive('getGithubToken')->andReturn('fake-token');

        // Mock getRepo and getLastTag outputs
        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return is_string($cmd) && str_contains($cmd, 'git ls-remote --tags');
            })
            ->andReturn('refs/tags/v1.0.0');

        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return is_string($cmd) && str_contains($cmd, 'git config --get remote.origin.url');
            })
            ->andReturn('fake/repo');

        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return $cmd === 'git pull';
            })
            ->andReturn('');

        $command->shouldReceive('runCmd')
            ->withArgs(function ($path, $cmd) {
                return is_string($cmd) && str_contains($cmd, 'git log');
            })
            ->andReturn('* Fix bug 1');

        $this->app[Kernel::class]->registerCommand($command);

        File::shouldReceive('isDirectory')->andReturn(true);
        // Prepend shouldn't be called

        Http::fake([
            'https://api.github.com/repos/fake/repo/releases' => Http::response(['html_url' => 'https://github.com/fake/repo/releases/tag/1.0.1'], 201),
        ]);

        $this->artisan('github:release', [
            'path' => '/fake/path',
            '--changelog' => false,
        ])
            ->expectsOutput('Release v1.0.1')
            ->expectsOutput('Released url: https://github.com/fake/repo/releases/tag/1.0.1')
            ->doesntExpectOutput('Add changelog')
            ->assertExitCode(0);

        Http::assertSent(function ($request) {
            return $request->url() == 'https://api.github.com/repos/fake/repo/releases' &&
                   $request['tag_name'] == '1.0.1' &&
                   $request['body'] == '* Fix bug 1' &&
                   $request->header('Authorization')[0] == 'Bearer fake-token';
        });
    }
}
