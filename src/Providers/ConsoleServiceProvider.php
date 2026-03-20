<?php

namespace Juzaweb\DevTool\Providers;

use Illuminate\Support\ServiceProvider;
use Juzaweb\DevTool\Commands\Modules\CommandMakeCommand;
use Juzaweb\DevTool\Commands\Modules\ComponentClassMakeCommand;
use Juzaweb\DevTool\Commands\Modules\ComponentViewMakeCommand;
use Juzaweb\DevTool\Commands\Modules\ControllerMakeCommand;
use Juzaweb\DevTool\Commands\Modules\Cruds\AdminCrudMakeCommand;
use Juzaweb\DevTool\Commands\Modules\Cruds\APICrudMakeCommand;
use Juzaweb\DevTool\Commands\Modules\Cruds\CrudMakeCommand;
use Juzaweb\DevTool\Commands\Modules\Databases\FactoryMakeCommand;
use Juzaweb\DevTool\Commands\Modules\Databases\MigrateCommand;
use Juzaweb\DevTool\Commands\Modules\Databases\MigrateFreshCommand;
use Juzaweb\DevTool\Commands\Modules\Databases\MigrateRefreshCommand;
use Juzaweb\DevTool\Commands\Modules\Databases\MigrateResetCommand;
use Juzaweb\DevTool\Commands\Modules\Databases\MigrateRollbackCommand;
use Juzaweb\DevTool\Commands\Modules\Databases\MigrateStatusCommand;
use Juzaweb\DevTool\Commands\Modules\Databases\MigrationMakeCommand;
use Juzaweb\DevTool\Commands\Modules\Databases\SeedCommand;
use Juzaweb\DevTool\Commands\Modules\Databases\SeedMakeCommand;
use Juzaweb\DevTool\Commands\Modules\DatatableMakeCommand;
use Juzaweb\DevTool\Commands\Modules\DumpCommand;
use Juzaweb\DevTool\Commands\Modules\EventMakeCommand;
use Juzaweb\DevTool\Commands\Modules\JobMakeCommand;
use Juzaweb\DevTool\Commands\Modules\ListenerMakeCommand;
use Juzaweb\DevTool\Commands\Modules\MailMakeCommand;
use Juzaweb\DevTool\Commands\Modules\MiddlewareMakeCommand;
use Juzaweb\DevTool\Commands\Modules\ModelMakeCommand;
use Juzaweb\DevTool\Commands\Modules\ModelShowCommand;
use Juzaweb\DevTool\Commands\Modules\ModuleDeleteCommand;
use Juzaweb\DevTool\Commands\Modules\ModuleMakeCommand;
use Juzaweb\DevTool\Commands\Modules\NotificationMakeCommand;
use Juzaweb\DevTool\Commands\Modules\PolicyMakeCommand;
use Juzaweb\DevTool\Commands\Modules\ProviderMakeCommand;
use Juzaweb\DevTool\Commands\Modules\PublishCommand;
use Juzaweb\DevTool\Commands\Modules\PublishConfigurationCommand;
use Juzaweb\DevTool\Commands\Modules\PublishMigrationCommand;
use Juzaweb\DevTool\Commands\Modules\PublishTranslationCommand;
use Juzaweb\DevTool\Commands\Modules\RequestMakeCommand;
use Juzaweb\DevTool\Commands\Modules\ResourceMakeCommand;
use Juzaweb\DevTool\Commands\Modules\RouteProviderMakeCommand;
use Juzaweb\DevTool\Commands\Modules\RuleMakeCommand;
use Juzaweb\DevTool\Commands\Modules\SetupCommand;
use Juzaweb\DevTool\Commands\Modules\TestMakeCommand;
use Juzaweb\DevTool\Commands\Modules\UnUseCommand;
use Juzaweb\DevTool\Commands\Modules\UseCommand;
use Juzaweb\DevTool\Commands\PublishAgentsCommand;
use Juzaweb\DevTool\Commands\Themes\DownloadStyleCommand;
use Juzaweb\DevTool\Commands\Themes\DownloadTemplateCommand;
use Juzaweb\DevTool\Commands\Themes\MakeControllerCommand;
use Juzaweb\DevTool\Commands\Themes\MakePageBlockCommand;
use Juzaweb\DevTool\Commands\Themes\MakeTemplateCommand;
use Juzaweb\DevTool\Commands\Themes\MakeViewCommand;
use Juzaweb\DevTool\Commands\Themes\MakeWidgetCommand;
use Juzaweb\DevTool\Commands\Themes\ThemeGeneratorCommand;
use Juzaweb\DevTool\Commands\Themes\ThemeSeedCommand;
use Juzaweb\Modules\Core\Modules\Support\Stub;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * The available commands
     */
    protected array $commands = [
        PublishAgentsCommand::class,
        CommandMakeCommand::class,
        ControllerMakeCommand::class,
        DumpCommand::class,
        EventMakeCommand::class,
        JobMakeCommand::class,
        ListenerMakeCommand::class,
        MailMakeCommand::class,
        MiddlewareMakeCommand::class,
        NotificationMakeCommand::class,
        ProviderMakeCommand::class,
        RouteProviderMakeCommand::class,
        ModuleDeleteCommand::class,
        ModuleMakeCommand::class,
        FactoryMakeCommand::class,
        PolicyMakeCommand::class,
        RequestMakeCommand::class,
        RuleMakeCommand::class,
        MigrateCommand::class,
        MigrateRefreshCommand::class,
        MigrateResetCommand::class,
        MigrateFreshCommand::class,
        MigrateRollbackCommand::class,
        MigrateStatusCommand::class,
        MigrationMakeCommand::class,
        ModelMakeCommand::class,
        ModelShowCommand::class,
        PublishCommand::class,
        PublishConfigurationCommand::class,
        PublishMigrationCommand::class,
        PublishTranslationCommand::class,
        SeedCommand::class,
        SeedMakeCommand::class,
        SetupCommand::class,
        UnUseCommand::class,
        UseCommand::class,
        ResourceMakeCommand::class,
        TestMakeCommand::class,
        ComponentClassMakeCommand::class,
        ComponentViewMakeCommand::class,
        DatatableMakeCommand::class,
        AdminCrudMakeCommand::class,
        APICrudMakeCommand::class,
        CrudMakeCommand::class,
        // Theme commands
        DownloadStyleCommand::class,
        DownloadTemplateCommand::class,
        MakeControllerCommand::class,
        MakePageBlockCommand::class,
        MakeTemplateCommand::class,
        MakeViewCommand::class,
        MakeWidgetCommand::class,
        ThemeGeneratorCommand::class,
        ThemeSeedCommand::class,
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
        $path = $this->app['config']->get('dev-tool.modules.stubs.path');

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
        $stubsPath = dirname(__DIR__, 2).'/stubs';

        $this->publishes([
            $stubsPath => resource_path('stubs'),
        ], 'stubs');
    }
}
