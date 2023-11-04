<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang
 * @link       https://juzaweb.com
 * @license    GNU V2
 */

namespace Juzaweb\DevTool\Commands\Theme;

use Illuminate\Console\Command;
use Juzaweb\CMS\Facades\Theme;
use Symfony\Component\Console\Input\InputArgument;

class ThemeActiveCommand extends Command
{
    protected $name = 'theme:active';

    public function handle(): int
    {
        $themeName = $this->argument('theme');

        $theme = Theme::find($themeName);

        if ($theme === null) {
            $this->error("Theme {$themeName} does not exists.");
            return self::FAILURE;
        }

        $theme->activate();

        $this->info("Theme {$themeName} is activated.");

        return self::SUCCESS;
    }

    protected function getArguments(): array
    {
        return [
            ['theme', InputArgument::REQUIRED, 'The name of theme.'],
        ];
    }
}
