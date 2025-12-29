<?php

declare(strict_types=1);

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;

/**
 * Command to optimize module discovery by caching results.
 */
class ModuleOptimizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache module discovery results for improved performance';

    /**
     * Execute the console command.
     *
     * @param ModuleManager $moduleManager
     * @return int
     */
    public function handle(ModuleManager $moduleManager): int
    {
        $this->info('Optimizing module discovery...');

        // Clear existing cache first
        $moduleManager->clearCache();

        // Discover and cache
        $modules = $moduleManager->discover();
        $count = count($modules);

        $this->info("Successfully cached discovery results for {$count} modules.");
        
        if ($this->output->isVerbose()) {
            foreach ($modules as $name => $module) {
                $this->line("- {$name} (" . ($module['enabled'] ? 'enabled' : 'disabled') . ")");
            }
        }

        return self::SUCCESS;
    }
}
