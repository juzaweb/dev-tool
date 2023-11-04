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
        $token = $this->ask('Please enter your github token: ');
        if (! $token) {
            $this->error('Please enter your github token.');
            return;
        }

        $repo = $this->runCmd('git config --get remote.origin.url | sed \'s/.*[:|\/]\([^/]*\/[^/]*\)\.git$/\1/\'');
        $lastTag = $this->runCmd('git describe --abbrev=0 --tags');
        $body = $this->runCmd("git log --no-merges --pretty=format:\"* %s\" {$lastTag}..HEAD | sort | uniq");
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

        File::prepend(base_path($this->argument('path')."/changelog.md"), "### v{$newTag} \n{$body}\n\n");

        $this->info($release->json());
    }

    protected function getReleaseVersion(string $lastTag): string
    {
        if ($version = $this->option('ver')) {
            return $version;
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
            ['ver', null, InputOption::VALUE_OPTIONAL, 'Version to release.', null],
        ];
    }
}