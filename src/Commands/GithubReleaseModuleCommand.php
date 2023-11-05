<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang
 * @link       https://juzaweb.com
 * @license    GNU V2
 */

namespace Juzaweb\DevTool\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

class GithubReleaseModuleCommand extends Command
{
    protected $name = 'github:release-module';

    protected $description = 'Create release module';

    public function handle(): void
    {
        $token = $this->getGithubToken();
        $repo = $this->runCmd('git config --get remote.origin.url | sed \'s/.*[:|\/]\([^/]*\/[^/]*\)\.git$/\1/\'');
        $lastTag = $this->getLastTag();

        if (empty($lastTag)) {
            $body = $this->runCmd("git log --no-merges --pretty=format:\"* %s\" | sort | uniq");
        } else {
            $body = $this->runCmd("git log --no-merges --pretty=format:\"* %s\" {$lastTag}..HEAD | sort | uniq");
        }

        if (empty($body)) {
            $this->info('Nothing to release');
            return;
        }

        $body = collect(explode("\n", $body))
            ->filter(
                function ($item) {
                    return !empty($item) && !str_contains($item, ':construction:');
                }
            )
            ->implode("\n");

        $newTag = $this->getReleaseVersion($lastTag);

        $this->info("Release v{$newTag}");

        $release = Http::withHeaders(
            [
                'Authorization' => "Bearer {$token}",
                'Content-Type' => 'application/json',
            ]
        )
            ->post(
                "https://api.github.com/repos/{$repo}/releases",
                [
                    'tag_name' => $newTag,
                    'name' => $newTag,
                    'target_commitish' => 'master',
                    'body' => $body,
                ]
            )
            ->throw();

        if ($this->option('changelog')) {
            File::prepend(
                base_path($this->argument('path')."/changelog.md"),
                "### v{$newTag} \n{$body}\n\n"
            );
        }

        $this->info('Released url: '. $release->json()['html_url']);
    }

    protected function getLastTag(): string
    {
        try {
            $lastTag = $this->runCmd('git describe --abbrev=0 --tags');
        } catch (\Exception $e) {
            $lastTag = '';
        }

        return $lastTag;
    }

    protected function getGithubToken(): string
    {
        $token = config('dev-tool.release.github_token');
        if (empty($token)) {
            do {
                $token = $this->ask('Please enter your github token: ');
            } while (empty($token));
        }

        return $token;
    }

    protected function getReleaseVersion(string $lastTag): string
    {
        if ($version = $this->option('ver')) {
            return $version;
        }

        if (empty($lastTag)) {
            return '1.0.0';
        }

        $split = explode('.', $lastTag);
        if (count($split) > 2) {
            $split[count($split) - 1] += 1;
            $newTag = implode('.', $split);
        } else {
            $newTag = $lastTag.'.1';
        }

        return $newTag;
    }
    
    protected function runCmd(string|array $command): string
    {
        $path = $this->argument('path');

        if (is_array($command)) {
            $process = new Process($command, base_path($path));
        } else {
            $process = Process::fromShellCommandline($command, base_path($path));
        }

        $process->setTimeout(30);

        $process->mustRun();

        return trim($process->getOutput());
    }

    protected function getArguments(): array
    {
        return [
            ['path', InputArgument::REQUIRED, 'Module path.'],
        ];
    }
    
    protected function getOptions(): array
    {
        return [
            ['ver', null, InputOption::VALUE_OPTIONAL, 'Version to release. Auto increment version if not set', null],
            ['changelog', null, InputOption::VALUE_OPTIONAL, 'Write to changelog.md. Default: true', true],
        ];
    }
}
