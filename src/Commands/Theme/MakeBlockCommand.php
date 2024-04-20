<?php
/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzaweb/cms
 * @author     Juzaweb Team <admin@juzaweb.com>
 * @link       https://juzaweb.com
 * @license    MIT
 */

namespace Juzaweb\DevTool\Commands\Theme;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Juzaweb\CMS\Facades\Theme;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeBlockCommand extends Command
{
    protected $name = 'theme:make-block';

    public function handle(): int
    {
        $name = preg_replace("/\W/", '_', $this->argument('name'));
        $name = strtolower($name);

        $themeName = $this->argument('theme');
        $theme = Theme::find($themeName);

        if ($theme === null) {
            $this->error("Theme {$themeName} does not exists.");
            return self::FAILURE;
        }

        $register = json_decode($theme->getContents('register.json'), true, 512, JSON_THROW_ON_ERROR);
        $blocks = Arr::get($register, 'blocks', []);

        if (Arr::get($blocks, $name)) {
            $this->error("Block {$name} already exists.");
            return self::FAILURE;
        }

        $blocks[$name] = [
            "label" => ucwords(str_replace('_', ' ', $name)),
            "view" => "theme::components.blocks.{$name}"
        ];

        $dataFile = $theme->getPath("data/blocks/{$name}.json");
        $viewFile = $theme->getPath("views/components/blocks/{$name}.twig");

        if (!$this->option('force') && file_exists($dataFile)) {
            $this->error("File {$dataFile} already exists.");
            return self::FAILURE;
        }

        if (!$this->option('force') && file_exists($viewFile)) {
            $this->error("File {$viewFile} already exists.");
            return self::FAILURE;
        }

        File::makeDirectory(dirname($dataFile), 0777, true, true);
        File::makeDirectory(dirname($viewFile), 0777, true, true);

        $dataPath = __DIR__ . '/../../../stubs/theme/blocks/data.stub';
        File::put($dataFile, File::get($dataPath));
        $this->info("Generate success file {$dataFile}");

        $viewPath = __DIR__ . '/../../../stubs/theme/blocks/view.stub';
        File::put($viewFile, File::get($viewPath));
        $this->info("Generate success file {$viewFile}");

        $register['blocks'] = $blocks;
        File::put(
            $theme->getPath('register.json'),
            json_encode($register, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
        );

        $this->info("Update success data file register.json");

        return self::SUCCESS;
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of block will be make.'],
            ['theme', InputArgument::REQUIRED, 'The name of theme.'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Force overwrite existing files.'],
        ];
    }
}
