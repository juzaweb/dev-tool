<?php

namespace Juzaweb\DevTool\Providers;

use Juzaweb\DevTool\Commands\GithubReleaseModuleCommand;
use Juzaweb\Modules\Core\Providers\ServiceProvider;

class DevToolServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
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
