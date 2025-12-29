<?php

declare(strict_types=1);

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;

/**
 * Command to disable a module.
 */
class DisableModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:disable {module : The name of the module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable a specific module';

    /**
     * Execute the console command.
     *
     * @param ModuleManager $moduleManager
     * @return int
     */
    public function handle(ModuleManager $moduleManager): int
    {
        $name = $this->argument('module');

        if (!$moduleManager->exists($name)) {
            $this->error("Module '{$name}' does not exist!");
            return self::FAILURE;
        }

        $module = $moduleManager->get($name);
        if (!$module['enabled']) {
            $this->info("Module '{$name}' is already disabled.");
            return self::SUCCESS;
        }

        $moduleManager->disable($name);
        $this->info("Module '{$name}' has been disabled.");

        return self::SUCCESS;
    }
}
