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
        \Juzaweb\DevTool\Commands\Commands\CommandMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\ControllerMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\DisableCommand::class,
        \Juzaweb\DevTool\Commands\Commands\DumpCommand::class,
        \Juzaweb\DevTool\Commands\Commands\EnableCommand::class,
        \Juzaweb\DevTool\Commands\Commands\EventMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\JobMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\ListenerMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\MailMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\MiddlewareMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\NotificationMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\ProviderMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\RouteProviderMakeCommand::class,
        \Juzaweb\Modules\Core\Modules\Commands\ModuleInstallCommand::class,
        \Juzaweb\DevTool\Commands\Commands\ListCommand::class,
        \Juzaweb\DevTool\Commands\Commands\ModuleDeleteCommand::class,
        \Juzaweb\DevTool\Commands\Commands\ModuleMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\Databases\FactoryMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\PolicyMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\RequestMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\RuleMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\Databases\MigrateCommand::class,
        \Juzaweb\DevTool\Commands\Commands\Databases\MigrateRefreshCommand::class,
        \Juzaweb\DevTool\Commands\Commands\Databases\MigrateResetCommand::class,
        \Juzaweb\DevTool\Commands\Commands\Databases\MigrateFreshCommand::class,
        \Juzaweb\DevTool\Commands\Commands\Databases\MigrateRollbackCommand::class,
        \Juzaweb\DevTool\Commands\Commands\Databases\MigrateStatusCommand::class,
        \Juzaweb\DevTool\Commands\Commands\Databases\MigrationMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\ModelMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\ModelShowCommand::class,
        \Juzaweb\DevTool\Commands\Commands\PublishCommand::class,
        \Juzaweb\DevTool\Commands\Commands\PublishConfigurationCommand::class,
        \Juzaweb\DevTool\Commands\Commands\PublishMigrationCommand::class,
        \Juzaweb\DevTool\Commands\Commands\PublishTranslationCommand::class,
        \Juzaweb\DevTool\Commands\Commands\Databases\SeedCommand::class,
        \Juzaweb\DevTool\Commands\Commands\Databases\SeedMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\SetupCommand::class,
        \Juzaweb\DevTool\Commands\Commands\UnUseCommand::class,
        \Juzaweb\DevTool\Commands\Commands\UpdateCommand::class,
        \Juzaweb\DevTool\Commands\Commands\UseCommand::class,
        \Juzaweb\DevTool\Commands\Commands\ResourceMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\TestMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\ComponentClassMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\ComponentViewMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\RepositoryMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\RepositoryEloquentMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\DatatableMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\LinkCommand::class,
        \Juzaweb\DevTool\Commands\Commands\Cruds\AdminCrudMakeCommand::class,
        \Juzaweb\DevTool\Commands\Commands\Cruds\CrudMakeCommand::class,
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
