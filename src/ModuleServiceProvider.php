<?php

declare(strict_types=1);

namespace Laravel\Modular;

use Illuminate\Support\ServiceProvider;
use Laravel\Modular\Console\Commands\MakeModuleCommand;
use Laravel\Modular\Console\Commands\RemoveModuleCommand;
use Laravel\Modular\Console\Commands\ListModulesCommand;
use Laravel\Modular\Console\Commands\MakeModuleControllerCommand;
use Laravel\Modular\Console\Commands\MakeModuleRequestCommand;
use Laravel\Modular\Console\Commands\MakeModuleResourceCommand;
use Laravel\Modular\Console\Commands\MakeModuleMigrationCommand;
use Laravel\Modular\Console\Commands\MakeModuleFactoryCommand;
use Laravel\Modular\Console\Commands\MakeModuleSeederCommand;
use Laravel\Modular\Console\Commands\MakeModuleModelCommand;
use Laravel\Modular\Console\Commands\MakeModuleTestCommand;
use Laravel\Modular\Console\Commands\ModuleCacheCommand;
use Laravel\Modular\Console\Commands\ModuleOptimizeCommand;
use Laravel\Modular\Console\Commands\ModuleTestCommand;
use Laravel\Modular\Console\Commands\EnableModuleCommand;
use Laravel\Modular\Console\Commands\DisableModuleCommand;
use Laravel\Modular\Console\Commands\ModuleStatusCommand;
use Laravel\Modular\Services\ModuleCacheService;
use Laravel\Modular\Services\ModulePerformanceService;
use Laravel\Modular\Services\ModuleStubService;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config first (safe)
        $this->mergeConfigFrom(__DIR__ . '/../config/module.php', 'module');

        // Core services (lazy, safe)
        $this->app->singleton(ModuleCacheService::class);
        $this->app->singleton(ModulePerformanceService::class);
        $this->app->singleton(ModuleStubService::class);

        $this->app->singleton(ModuleManager::class, function ($app) {
            return new ModuleManager(
                $app->make(ModuleCacheService::class),
                $app->make(ModulePerformanceService::class),
                $app->make(ModuleStubService::class)
            );
        });

        $this->app->alias(ModuleManager::class, 'module');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/module.php' => config_path('module.php'),
        ], 'module-config');

        // Register module providers ONLY after app is fully booted
        $this->app->booted(function () {
            $this->registerModuleProviders();
        });

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
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
            ]);
        }
    }

    /**
     * Register all enabled module service providers.
     */
    protected function registerModuleProviders(): void
    {
        $moduleManager = $this->app->make(ModuleManager::class);

        foreach ($moduleManager->enabled() as $module) {
            if (!empty($module['provider']) && class_exists($module['provider'])) {
                $this->app->register($module['provider']);
            }
        }
    }
}
