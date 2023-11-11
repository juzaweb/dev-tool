<?php

namespace Juzaweb\DevTool\Traits;

use Juzaweb\CMS\Exceptions\FileAlreadyExistException;
use Juzaweb\CMS\Support\Generators\FileGenerator;
use Juzaweb\CMS\Support\Stub;

trait ModuleCommandTrait
{
    /**
     * Get the plugin name.
     *
     * @return string
     */
    public function getModuleName(): string
    {
        if ($module = $this->argument('module')) {
            $module = app('plugins')->findOrFail($module);
            return $module->getName();
        }

        if ($module = config('dev-tool.dev_plugin_default')) {
            $module = app('plugins')->findOrFail($module);
            return $module->getName();
        }

        $cache = app('cache')->get('dev_plugin_last_chosse');

        $module = $this->choice(
            'Plugin',
            app('plugins')->all(true)->values()
                ->mapWithKeys(
                    function ($plugin, $key) {
                        return [$key + 1 => $plugin['name']];
                    }
                )
                ->toArray(),
            $cache
        );

        app('cache')->put('dev_plugin_last_chosse', $module);

        $module = app('plugins')->findOrFail($module);

        return $module->getName();
    }

    protected function makeFile($path, $contents): void
    {
        try {
            $overwriteFile = $this->hasOption('force') ? $this->option('force') : false;
            (new FileGenerator($path, $contents))
                ->withFileOverwrite($overwriteFile)
                ->generate();

            $path = realpath($path);
            $this->info("Created : {$path}");
        } catch (FileAlreadyExistException $e) {
            $path = realpath($path);
            $this->error("File : {$path} already exists.");
        }
    }

    protected function stubRender($file, $data): string
    {
        return (new Stub('/' . $file, $data))->render();
    }
}
