<?php

namespace Juzaweb\DevTool\Commands\Plugin\Migration;

use Illuminate\Support\Str;
use Juzaweb\CMS\Support\Config\GenerateConfigReader;
use Juzaweb\CMS\Support\Migrations\NameParser;
use Juzaweb\CMS\Support\Migrations\SchemaParser;
use Juzaweb\CMS\Support\Stub;
use Juzaweb\DevTool\Abstracts\GeneratorCommand;
use Juzaweb\DevTool\Traits\ModuleCommandTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MigrationMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:make-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration for the specified plugin.';

    /**
     * Run the command.
     */
    public function handle(): void
    {
        parent::handle();

        if (app()->environment() === 'testing') {
            return;
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The migration name will be created.'],
            ['module', InputArgument::OPTIONAL, 'The name of plugin will be created.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['fields', null, InputOption::VALUE_OPTIONAL, 'The specified fields table.', null],
            ['plain', null, InputOption::VALUE_NONE, 'Create plain migration.'],
        ];
    }

    /**
     * @return Stub
     */
    protected function getTemplateContents(): Stub
    {
        $parser = new NameParser($this->argument('name'));
        $module = $this->laravel['plugins']->findOrFail($this->getModuleName());

        if ($parser->isCreate()) {
            return Stub::create(
                '/migration/create.stub',
                [
                'class' => $this->getClass(),
                'table' => $module->getDomainName().'_'.$parser->getTableName(),
                'fields' => $this->getSchemaParser()->render(),
                ]
            );
        } elseif ($parser->isAdd()) {
            return Stub::create(
                '/migration/add.stub',
                [
                'class' => $this->getClass(),
                'table' => $parser->getTableName(),
                'fields_up' => $this->getSchemaParser()->up(),
                'fields_down' => $this->getSchemaParser()->down(),
                ]
            );
        } elseif ($parser->isDelete()) {
            return Stub::create(
                '/migration/delete.stub',
                [
                'class' => $this->getClass(),
                'table' => $parser->getTableName(),
                'fields_down' => $this->getSchemaParser()->up(),
                'fields_up' => $this->getSchemaParser()->down(),
                ]
            );
        } elseif ($parser->isDrop()) {
            return Stub::create(
                '/migration/drop.stub',
                [
                'class' => $this->getClass(),
                'table' => $parser->getTableName(),
                'fields' => $this->getSchemaParser()->render(),
                ]
            );
        }

        return Stub::create(
            '/migration/plain.stub',
            [
            'class' => $this->getClass(),
            ]
        );
    }

    public function getClass(): string
    {
        return $this->getClassName();
    }

    /**
     * @return string
     */
    private function getClassName(): string
    {
        return Str::studly($this->argument('name'));
    }

    /**
     * Get schema parser.
     *
     * @return SchemaParser
     */
    public function getSchemaParser(): SchemaParser
    {
        return new SchemaParser($this->option('fields'));
    }

    /**
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getDestinationFilePath(): string
    {
        $path = $this->laravel['plugins']->getModulePath($this->getModuleName());

        $generatorPath = GenerateConfigReader::read('migration');

        $monthPath = date('Y-m');

        return $path.$generatorPath->getPath()."/{$monthPath}/".$this->getFileName().'.php';
    }

    /**
     * @return string
     */
    private function getFileName(): string
    {
        return date('Y_m_d_His_').$this->getSchemaName();
    }

    /**
     * @return array|string
     */
    private function getSchemaName(): array|string
    {
        return $this->argument('name');
    }
}
