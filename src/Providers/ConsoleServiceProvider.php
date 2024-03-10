<?php

namespace Juzaweb\DevTool\Providers;

use Illuminate\Support\ServiceProvider;
use Juzaweb\DevTool\Commands\FindFillableColumnCommand;
use Juzaweb\DevTool\Commands\GithubReleaseModuleCommand;
use Juzaweb\DevTool\Commands\MakeDemoContentCommand;
use Juzaweb\DevTool\Commands\Plugin;
use Juzaweb\DevTool\Commands\Plugin\InstallCommand as PluginInstallCommand;
use Juzaweb\DevTool\Commands\Plugin\ListCommand;
use Juzaweb\DevTool\Commands\Plugin\Makers\ActionMakeCommand;
use Juzaweb\DevTool\Commands\Plugin\Makers\CommandMakeCommand;
use Juzaweb\DevTool\Commands\Plugin\Makers\ControllerMakeCommand;
use Juzaweb\DevTool\Commands\Plugin\Makers\EventMakeCommand;
use Juzaweb\DevTool\Commands\Plugin\Makers\JobMakeCommand;
use Juzaweb\DevTool\Commands\Plugin\Makers\ListenerMakeCommand;
use Juzaweb\DevTool\Commands\Plugin\Makers\MiddlewareMakeCommand;
use Juzaweb\DevTool\Commands\Plugin\Makers\ModelMakeCommand;
use Juzaweb\DevTool\Commands\Plugin\Makers\ModuleMakeCommand;
use Juzaweb\DevTool\Commands\Plugin\Makers\ProviderMakeCommand;
use Juzaweb\DevTool\Commands\Plugin\Makers\RequestMakeCommand;
use Juzaweb\DevTool\Commands\Plugin\Makers\ResourceMakeCommand;
use Juzaweb\DevTool\Commands\Plugin\Makers\RouteProviderMakeCommand;
use Juzaweb\DevTool\Commands\Plugin\Makers\RuleMakeCommand;
use Juzaweb\DevTool\Commands\Plugin\ModuleDeleteCommand;
use Juzaweb\DevTool\Commands\Plugin\Publish\PublishCommand;
use Juzaweb\DevTool\Commands\Plugin\SeedCommand;
use Juzaweb\DevTool\Commands\Plugin\Statuses\DisableCommand;
use Juzaweb\DevTool\Commands\Plugin\Statuses\EnableCommand;
use Juzaweb\DevTool\Commands\Resource;
use Juzaweb\DevTool\Commands\Theme;

class ConsoleServiceProvider extends ServiceProvider
{
    protected array $commands = [
        PluginInstallCommand::class,
        CommandMakeCommand::class,
        ControllerMakeCommand::class,
        DisableCommand::class,
        //DumpCommand::class,
        EnableCommand::class,
        EventMakeCommand::class,
        JobMakeCommand::class,
        ListenerMakeCommand::class,
        PublishCommand::class,
        //MailMakeCommand::class,
        MiddlewareMakeCommand::class,
        //NotificationMakeCommand::class,
        ProviderMakeCommand::class,
        RouteProviderMakeCommand::class,
        ListCommand::class,
        ModuleDeleteCommand::class,
        ModuleMakeCommand::class,
        //FactoryMakeCommand::class,
        //PolicyMakeCommand::class,
        RequestMakeCommand::class,
        RuleMakeCommand::class,
        Plugin\Migration\MigrateCommand::class,
        Plugin\Migration\MigrateRefreshCommand::class,
        Plugin\Migration\MigrateResetCommand::class,
        Plugin\Migration\MigrateRollbackCommand::class,
        Plugin\Migration\MigrateStatusCommand::class,
        Plugin\Migration\MigrationMakeCommand::class,
        ModelMakeCommand::class,
        SeedCommand::class,
        Plugin\Makers\SeedMakeCommand::class,
        ResourceMakeCommand::class,
        Plugin\Makers\TestMakeCommand::class,
        Theme\ThemeGeneratorCommand::class,
        Theme\ThemeListCommand::class,
        ActionMakeCommand::class,
        Plugin\Makers\DatatableMakeCommand::class,
        Resource\JuzawebResouceMakeCommand::class,
        Theme\GenerateDataThemeCommand::class,
        Theme\DownloadStyleCommand::class,
        Theme\DownloadTemplateCommand::class,
        Plugin\UpdateCommand::class,
        Theme\ThemeUpdateCommand::class,
        Theme\MakeBlockCommand::class,
        Plugin\Translation\ImportTranslationCommand::class,
        Plugin\Translation\TranslateViaGoogleCommand::class,
        Plugin\Translation\ExportTranslationCommand::class,
        Plugin\Makers\RepositoryMakeCommand::class,
        Theme\ExportTranslationCommand::class,
        Theme\ImportTranslationCommand::class,
        Theme\TranslateViaGoogleCommand::class,
        FindFillableColumnCommand::class,
        Resource\CRUDMakeCommand::class,
        GithubReleaseModuleCommand::class,
        Theme\ThemeActiveCommand::class,
        Plugin\Statuses\EnableAllCommand::class,
        MakeDemoContentCommand::class,
    ];

    /**
     * Register the commands.
     */
    public function register(): void
    {
        $this->commands($this->commands);

        // Register UI & router dev-tools
        if (is_dev_tool_enable()) {
            $this->app->register(UIServiceProvider::class);
            $this->app->register(RouteServiceProvider::class);
        }
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return $this->commands;
    }
}
