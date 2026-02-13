<?php

namespace Juzaweb\DevTool\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;

class PublishAgentsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'agents:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish dev-tool agents to .agent directory';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $files): int
    {
        $baseSourcePath = dirname(__DIR__, 2) . '/agents';
        $destinationPath = base_path('.agent');

        // Determine which folder to publish
        $folder = $this->getPublishFolder();
        $sourcePath = $folder ? $baseSourcePath . DIRECTORY_SEPARATOR . $folder : $baseSourcePath;

        if (!$files->isDirectory($sourcePath)) {
            $this->components->error("Source directory does not exist: {$sourcePath}");
            return 1;
        }

        $this->components->info($folder ? "Publishing {$folder}..." : 'Publishing dev-tool agents...');

        // Create destination directory if it doesn't exist
        $targetDestination = $folder ? $destinationPath . DIRECTORY_SEPARATOR . $folder : $destinationPath;
        if (!$files->isDirectory($targetDestination)) {
            $files->makeDirectory($targetDestination, 0755, true);
        }

        // Get all files and directories from source
        $items = $files->allFiles($sourcePath);
        $directories = $this->getDirectories($files, $sourcePath);

        // Create directories first
        foreach ($directories as $directory) {
            $relativePath = str_replace($sourcePath . DIRECTORY_SEPARATOR, '', $directory);
            $targetDir = $targetDestination . DIRECTORY_SEPARATOR . $relativePath;

            if (!$files->isDirectory($targetDir)) {
                $files->makeDirectory($targetDir, 0755, true);
            }
        }

        // Copy files
        foreach ($items as $file) {
            $relativePath = str_replace($sourcePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $targetPath = $targetDestination . DIRECTORY_SEPARATOR . $relativePath;

            if ($files->exists($targetPath) && !$this->option('force')) {
                if (!$this->components->confirm("File already exists: {$relativePath}. Overwrite?")) {
                    continue;
                }
            }

            $files->copy($file->getPathname(), $targetPath);
            $this->components->task($relativePath, fn() => true);
        }

        $this->components->info('Agents published successfully!');

        return 0;
    }

    /**
     * Get all directories recursively.
     */
    protected function getDirectories(Filesystem $files, string $path): array
    {
        $directories = [];

        foreach ($files->directories($path) as $directory) {
            $directories[] = $directory;
            $directories = array_merge($directories, $this->getDirectories($files, $directory));
        }

        return $directories;
    }

    /**
     * Get the folder to publish based on options.
     */
    protected function getPublishFolder(): ?string
    {
        if ($this->option('skills')) {
            return 'skills';
        }

        if ($this->option('rules')) {
            return 'rules';
        }

        return null;
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Overwrite existing files'],
            ['skills', null, InputOption::VALUE_NONE, 'Publish only skills folder'],
            ['rules', null, InputOption::VALUE_NONE, 'Publish only rules folder'],
        ];
    }
}
