<?php

declare(strict_types=1);

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;

/**
 * Command to enable a module.
 */
class EnableModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:enable {module : The name of the module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable a specific module';

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
        if ($module['enabled']) {
            $this->info("Module '{$name}' is already enabled.");
            return self::SUCCESS;
        }

        $moduleManager->enable($name);
        $this->info("Module '{$name}' has been enabled.");

        return self::SUCCESS;
    }
}
