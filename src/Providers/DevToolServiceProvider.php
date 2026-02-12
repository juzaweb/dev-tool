<?php

namespace Juzaweb\DevTool\Providers;

use Juzaweb\DevTool\Commands\GithubReleaseModuleCommand;
use Juzaweb\Modules\Core\Providers\ServiceProvider;

class DevToolServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/dev-tool.php', 'dev-tool');

        $this->app->register(ConsoleServiceProvider::class);
    }

    public function boot()
    {
        $this->commands(
            [
                GithubReleaseModuleCommand::class,
            ]
        );
    }
}
