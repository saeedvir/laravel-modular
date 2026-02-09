<?php

declare(strict_types=1);

namespace Laravel\Modular;

use Illuminate\Support\ServiceProvider;
use Laravel\Modular\Services\ModuleCacheService;
use Laravel\Modular\Services\ModulePerformanceService;
use Laravel\Modular\Services\ModuleStubService;
use Laravel\Modular\Console\Commands\{
    MakeModuleCommand,
    RemoveModuleCommand,
    ListModulesCommand,
    MakeModuleControllerCommand,
    MakeModuleRequestCommand,
    MakeModuleResourceCommand,
    MakeModuleMigrationCommand,
    MakeModuleFactoryCommand,
    MakeModuleSeederCommand,
    MakeModuleModelCommand,
    MakeModuleTestCommand,
    ModuleCacheCommand,
    ModuleOptimizeCommand,
    ModuleTestCommand,
    EnableModuleCommand,
    DisableModuleCommand,
    ModuleStatusCommand
};

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register container bindings.
     */
    public function register(): void
    {
        // Merge configuration (supports config cache)
        $this->mergeConfigFrom(
            __DIR__ . '/../config/module.php',
            'module'
        );

        /*
        |--------------------------------------------------------------------------
        | Core Services
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(ModuleCacheService::class);
        $this->app->singleton(ModulePerformanceService::class);
        $this->app->singleton(ModuleStubService::class);

        /*
        |--------------------------------------------------------------------------
        | Module Manager
        |--------------------------------------------------------------------------
        */

        $this->app->singleton(ModuleManager::class, function ($app) {
            return new ModuleManager(
                $app->make(ModuleCacheService::class),
                $app->make(ModulePerformanceService::class),
                $app->make(ModuleStubService::class)
            );
        });

        // Alias for easier resolution
        $this->app->alias(ModuleManager::class, 'module');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        /*
        |--------------------------------------------------------------------------
        | Register Enabled Module Providers
        |--------------------------------------------------------------------------
        |
        | Executed AFTER framework boot to ensure full container availability.
        |
        */
        $this->app->booted(fn() => $this->registerModuleProviders());
    }

    /**
     * Console-only boot logic.
     */
    protected function bootForConsole(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/module.php' => config_path('module.php'),
        ], 'module-config');

        // Register CLI commands
        $this->commands($this->consoleCommands());
    }

    /**
     * All console commands (lazy grouped).
     */
    protected function consoleCommands(): array
    {
        return [
            MakeModuleCommand::class,
            RemoveModuleCommand::class,
            ListModulesCommand::class,
            MakeModuleControllerCommand::class,
            MakeModuleRequestCommand::class,
            MakeModuleResourceCommand::class,
            MakeModuleMigrationCommand::class,
            MakeModuleFactoryCommand::class,
            MakeModuleSeederCommand::class,
            MakeModuleModelCommand::class,
            MakeModuleTestCommand::class,
            ModuleCacheCommand::class,
            ModuleOptimizeCommand::class,
            ModuleTestCommand::class,
            EnableModuleCommand::class,
            DisableModuleCommand::class,
            ModuleStatusCommand::class,
        ];
    }

    /**
     * Register enabled module service providers.
     */
    protected function registerModuleProviders(): void
    {
        /** @var ModuleManager $manager */
        $manager = $this->app->make(ModuleManager::class);

        /*
        |--------------------------------------------------------------------------
        | Use cached module list when possible
        |--------------------------------------------------------------------------
        */
        $modules = $manager->enabled();

        foreach ($modules as $module) {

            $provider = $module['provider'] ?? null;

            if (!$provider || !class_exists($provider)) {
                continue;
            }

            $this->app->register($provider);
        }
    }
}
