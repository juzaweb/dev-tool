<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang
 * @link       https://juzaweb.com
 * @license    GNU V2
 */

namespace Juzaweb\DevTool\Commands\Plugin\Statuses;

use Illuminate\Console\Command;
use Juzaweb\CMS\Support\Plugin;

class EnableAllCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:enable-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable all plugins.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        /** @var Plugin[] $modules */
        $modules = $this->laravel['plugins']->all();

        foreach ($modules as $module) {
            $module->enable();
        }
    }
}
