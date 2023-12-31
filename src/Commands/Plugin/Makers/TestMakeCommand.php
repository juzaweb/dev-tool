<?php

namespace Juzaweb\DevTool\Commands\Plugin\Makers;

use Illuminate\Support\Str;
use Juzaweb\CMS\Support\Config\GenerateConfigReader;
use Juzaweb\CMS\Support\Stub;
use Juzaweb\DevTool\Abstracts\GeneratorCommand;
use Juzaweb\DevTool\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class TestMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    protected string $argumentName = 'name';
    protected $name = 'plugin:make-test';
    protected $description = 'Create a new test class for the specified plugin.';

    public function getDefaultNamespace(): string
    {
        $module = $this->laravel['plugins'];

        if ($this->option('feature')) {
            return $module->config('paths.generator.test-feature.namespace')
                ?: $module->config('paths.generator.test-feature.path', 'Tests/Feature');
        }

        return $module->config('paths.generator.test.namespace')
            ?: $module->config('paths.generator.test.path', 'Tests/Unit');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the form request class.'],
            ['module', InputArgument::OPTIONAL, 'The name of plugin will be used.'],
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
            ['feature', false, InputOption::VALUE_NONE, 'Create a feature test.'],
        ];
    }

    /**
     * @return string
     */
    protected function getTemplateContents(): string
    {
        $module = $this->laravel['plugins']->findOrFail($this->getModuleName());
        $stub = '/unit-test.stub';

        if ($this->option('feature')) {
            $stub = '/feature-test.stub';
        }

        return (new Stub($stub, [
            'NAMESPACE' => $this->getClassNamespace($module),
            'CLASS' => $this->getClass(),
        ]))->render();
    }

    /**
     * @return string
     */
    protected function getDestinationFilePath(): string
    {
        $path = $this->laravel['plugins']->getModulePath($this->getModuleName());

        if ($this->option('feature')) {
            $testPath = GenerateConfigReader::read('test-feature');
        } else {
            $testPath = GenerateConfigReader::read('test');
        }

        return $path . $testPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName(): string
    {
        return Str::studly($this->argument('name'));
    }
}
