<?php

namespace Juzaweb\DevTool\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PublishAgentsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'agents:publish {--force : Overwrite existing files}';

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
        $sourcePath = dirname(__DIR__, 2) . '/agents';
        $destinationPath = base_path('.agent');

        if (!$files->isDirectory($sourcePath)) {
            $this->components->error("Source directory does not exist: {$sourcePath}");
            return 1;
        }

        $this->components->info('Publishing dev-tool agents...');

        // Create destination directory if it doesn't exist
        if (!$files->isDirectory($destinationPath)) {
            $files->makeDirectory($destinationPath, 0755, true);
        }

        // Get all files and directories from source
        $items = $files->allFiles($sourcePath);
        $directories = $this->getDirectories($files, $sourcePath);

        // Create directories first
        foreach ($directories as $directory) {
            $relativePath = str_replace($sourcePath . DIRECTORY_SEPARATOR, '', $directory);
            $targetDir = $destinationPath . DIRECTORY_SEPARATOR . $relativePath;

            if (!$files->isDirectory($targetDir)) {
                $files->makeDirectory($targetDir, 0755, true);
            }
        }

        // Copy files
        foreach ($items as $file) {
            $relativePath = str_replace($sourcePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $targetPath = $destinationPath . DIRECTORY_SEPARATOR . $relativePath;

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
}
