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
use Juzaweb\DevTool\Support\DemoContentBuilder;
use Symfony\Component\Console\Input\InputOption;

class MakeDemoContentCommand extends Command
{
    protected $name = 'make:demo-content';

    protected $description = 'Make demo content';

    public function handle(): void
    {
        DemoContentBuilder::make()
            ->setSiteId($this->option('site'))
            ->setLimit($this->option('limit'))
            ->setPostType($this->option('post-type'))
            ->generate();
    }

    protected function getOptions(): array
    {
        return [
            ['site', null, InputOption::VALUE_OPTIONAL, 'Site id', null],
            ['limit', null, InputOption::VALUE_OPTIONAL, 'Limit', 10],
            ['post-type', null, InputOption::VALUE_OPTIONAL, 'Post type'],
        ];
    }
}
