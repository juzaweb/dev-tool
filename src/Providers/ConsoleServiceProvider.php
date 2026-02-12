<?php

namespace Juzaweb\DevTool\Providers;

use Illuminate\Support\ServiceProvider;
use Juzaweb\Modules\Core\Modules\Support\Stub;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * The available commands
     * @var array
     */
    protected array $commands = [
        \Juzaweb\DevTool\Commands\Modules\CommandMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\ControllerMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\DisableCommand::class,
        \Juzaweb\DevTool\Commands\Modules\DumpCommand::class,
        \Juzaweb\DevTool\Commands\Modules\EnableCommand::class,
        \Juzaweb\DevTool\Commands\Modules\EventMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\JobMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\ListenerMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\MailMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\MiddlewareMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\NotificationMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\ProviderMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\RouteProviderMakeCommand::class,
        \Juzaweb\Modules\Core\Modules\Commands\ModuleInstallCommand::class,
        \Juzaweb\DevTool\Commands\Modules\ListCommand::class,
        \Juzaweb\DevTool\Commands\Modules\ModuleDeleteCommand::class,
        \Juzaweb\DevTool\Commands\Modules\ModuleMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\Databases\FactoryMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\PolicyMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\RequestMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\RuleMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\Databases\MigrateCommand::class,
        \Juzaweb\DevTool\Commands\Modules\Databases\MigrateRefreshCommand::class,
        \Juzaweb\DevTool\Commands\Modules\Databases\MigrateResetCommand::class,
        \Juzaweb\DevTool\Commands\Modules\Databases\MigrateFreshCommand::class,
        \Juzaweb\DevTool\Commands\Modules\Databases\MigrateRollbackCommand::class,
        \Juzaweb\DevTool\Commands\Modules\Databases\MigrateStatusCommand::class,
        \Juzaweb\DevTool\Commands\Modules\Databases\MigrationMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\ModelMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\ModelShowCommand::class,
        \Juzaweb\DevTool\Commands\Modules\PublishCommand::class,
        \Juzaweb\DevTool\Commands\Modules\PublishConfigurationCommand::class,
        \Juzaweb\DevTool\Commands\Modules\PublishMigrationCommand::class,
        \Juzaweb\DevTool\Commands\Modules\PublishTranslationCommand::class,
        \Juzaweb\DevTool\Commands\Modules\Databases\SeedCommand::class,
        \Juzaweb\DevTool\Commands\Modules\Databases\SeedMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\SetupCommand::class,
        \Juzaweb\DevTool\Commands\Modules\UnUseCommand::class,
        \Juzaweb\DevTool\Commands\Modules\UpdateCommand::class,
        \Juzaweb\DevTool\Commands\Modules\UseCommand::class,
        \Juzaweb\DevTool\Commands\Modules\ResourceMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\TestMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\ComponentClassMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\ComponentViewMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\RepositoryMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\RepositoryEloquentMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\DatatableMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\LinkCommand::class,
        \Juzaweb\DevTool\Commands\Modules\Cruds\AdminCrudMakeCommand::class,
        \Juzaweb\DevTool\Commands\Modules\Cruds\CrudMakeCommand::class,
    ];

    public function boot()
    {
        $this->setupStubPath();
        $this->registerNamespaces();
    }

    public function register(): void
    {
        $this->commands($this->commands);
    }

    /**
     * Setup stub path.
     */
    public function setupStubPath()
    {
        $path = $this->app['config']->get('modules.stubs.path');

        Stub::setBasePath($path);

        // $this->app->booted(function ($app) {
        //     /** @var RepositoryInterface $moduleRepository */
        //     $moduleRepository = $app[RepositoryInterface::class];
        //     if ($moduleRepository->config('stubs.enabled') === true) {
        //         Stub::setBasePath($moduleRepository->config('stubs.path'));
        //     }
        // });
    }

    public function provides(): array
    {
        return $this->commands;
    }

    /**
     * Register package's namespaces.
     */
    protected function registerNamespaces()
    {
        $stubsPath = dirname(__DIR__, 2) . '/stubs';

        $this->publishes([
            $stubsPath => resource_path('stubs'),
        ], 'stubs');
    }
}
