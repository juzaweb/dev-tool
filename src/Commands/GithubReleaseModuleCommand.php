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
        $path = $this->argument('path');

        if (!File::isDirectory($path)) {
            $path = base_path($path);
        }

        $lastTag = $this->getLastTag($path);
        $repo = $this->getRepo($path);

        if (empty($lastTag)) {
            $body = $this->runCmd($path, "git log --no-merges --pretty=format:\"* %s\" | uniq");
        } else {
            $body = $this->runCmd($path, "git log --no-merges --pretty=format:\"* %s\" {$lastTag}..HEAD | uniq");
        }

        if (empty($body)) {
            $this->info('Nothing to release');
            return;
        }

        $body = collect(explode("\n", $body))
            ->filter(fn ($item) => !empty($item) && !str_contains($item, ':construction:'))
            ->implode("\n");

        $newTag = $this->getReleaseVersion($lastTag);

        if ($this->option('changelog')) {
            $this->info('Add changelog');

            File::prepend(
                "{$path}/changelog.md",
                "### v{$newTag} \n{$body}\n\n"
            );

            $this->runCmd($path, 'git add changelog.md');

            $this->runCmd($path, "git commit -o changelog.md -m 'memo: Add changelog v{$newTag}'");

            $this->runCmd($path, 'git push');
        }

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
                    'target_commitish' => $this->option('target'),
                    'body' => $body,
                ]
            )
            ->throw();

        $this->info('Released url: '. $release->json()['html_url']);
    }

    protected function getLastTag(string $path): string
    {
        try {
            $lastTag = $this->runCmd($path, 'git ls-remote --tags --sort=-committerdate | head -1');
        } catch (\Exception $e) {
            $lastTag = '';
        }

        return last(explode('/', $lastTag));
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
            ++$split[count($split) - 1];
            $newTag = implode('.', $split);
        } else {
            $newTag = $lastTag.'.1';
        }

        return get_version_by_tag($newTag);
    }

    protected function runCmd(string $path, string|array $command): string
    {
        if (is_array($command)) {
            $process = new Process($command, $path);
        } else {
            $process = Process::fromShellCommandline($command, $path);
        }

        $process->setTimeout(30);

        $process->mustRun();

        return trim($process->getOutput());
    }

    protected function getRepo(string $path): string
    {
        $repo = $this->runCmd($path, 'git config --get remote.origin.url | sed \'s/.*[:|\/]\([^/]*\/[^/]*\)\.git$/\1/\'');

        if (is_url($repo)) {
            $repo = ltrim(parse_url($repo, \PHP_URL_PATH), '/');
        }

        return $repo;
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
            ['target', null, InputOption::VALUE_OPTIONAL, 'Target branch to release', 'master'],
        ];
    }
}
