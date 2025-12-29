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
<<<<<<< HEAD
use Laravel\Modular\Console\Commands\MakeModuleModelCommand;
use Laravel\Modular\Console\Commands\MakeModuleTestCommand;
use Laravel\Modular\Console\Commands\ModuleCacheCommand;
use Laravel\Modular\Console\Commands\ModuleOptimizeCommand;
use Laravel\Modular\Console\Commands\ModuleTestCommand;
use Laravel\Modular\Console\Commands\EnableModuleCommand;
use Laravel\Modular\Console\Commands\DisableModuleCommand;
use Laravel\Modular\Console\Commands\ModuleStatusCommand;
=======
use Laravel\Modular\Console\Commands\ModuleCacheCommand;
use Laravel\Modular\Console\Commands\ModuleTestCommand;
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
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
        // Register services as singletons
        $this->app->singleton(ModuleCacheService::class);
        $this->app->singleton(ModulePerformanceService::class);
        $this->app->singleton(ModuleStubService::class);

        // Register module manager
        $this->app->singleton(ModuleManager::class, function ($app) {
            return new ModuleManager(
                $app->make(ModuleCacheService::class),
                $app->make(ModulePerformanceService::class),
                $app->make(ModuleStubService::class)
            );
        });

        // Register facade alias
        $this->app->alias(ModuleManager::class, 'module');

<<<<<<< HEAD
        // Register module service providers
        $this->registerModuleProviders();

=======
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
        // Merge config
        $this->mergeConfigFrom(__DIR__.'/../config/module.php', 'module');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/module.php' => config_path('module.php'),
        ], 'module-config');

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
<<<<<<< HEAD
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
=======
                ModuleCacheCommand::class,
                ModuleTestCommand::class,
            ]);
        }

        // Register module service providers
        // Note: Autoloading is handled by composer merge plugin
        $this->registerModuleProviders();
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
    }

    /**
     * Register all enabled module service providers.
     * Composer merge plugin handles autoloading automatically.
     */
    protected function registerModuleProviders(): void
    {
        $moduleManager = $this->app->make(ModuleManager::class);
        $modules = $moduleManager->enabled();

        foreach ($modules as $module) {
            if (!empty($module['provider']) && class_exists($module['provider'])) {
                $this->app->register($module['provider']);
            }
        }
    }
}
