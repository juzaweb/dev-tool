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

use Arcanedev\LogViewer\Commands\Command;
use Symfony\Component\Console\Input\InputOption;

class ClearLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'log:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all generated log files';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('force') || $this->confirm('This will delete all the log files, Do you wish to continue?')) {
            $this->logViewer->clear();
            $this->info('Successfully cleared the logs!');
        }

        return static::SUCCESS;
    }

    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Force the operation to run when in production'],
        ];
    }
}
