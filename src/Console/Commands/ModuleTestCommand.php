<?php

declare(strict_types=1);

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;
use Illuminate\Support\Facades\File;

/**
 * Command to run tests for a specific module.
 */
class ModuleTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:test {module? : The module name} {--all : Run tests for all modules}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run tests for a specific module or all modules';

    /**
     * Execute the console command.
     *
     * @param ModuleManager $moduleManager
     * @return int
     */
    public function handle(ModuleManager $moduleManager): int
    {
        $moduleName = $this->argument('module');
        $runAll = $this->option('all');

        if (!$moduleName && !$runAll) {
            $this->error('Please specify a module name or use --all flag');
            return self::FAILURE;
        }

        if ($runAll) {
            return $this->runAllModuleTests($moduleManager);
        }

        return $this->runModuleTest($moduleManager, $moduleName);
    }

    /**
     * Run tests for a specific module.
     *
     * @param ModuleManager $moduleManager
     * @param string $moduleName
     * @return int
     */
    protected function runModuleTest(ModuleManager $moduleManager, string $moduleName): int
    {
        if (!$moduleManager->exists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return self::FAILURE;
        }

        $module = $moduleManager->get($moduleName);
        $testPath = $module['path'] . '/tests';

        if (!File::exists($testPath)) {
            $this->warn("No tests found for module '{$moduleName}'");
            return self::SUCCESS;
        }

        $this->info("Running tests for module: {$moduleName}");
        
        $command = "vendor/bin/phpunit {$testPath}";
        $exitCode = 0;
        
        \passthru($command, $exitCode);
        
        return $exitCode;
    }

    /**
     * Run tests for all modules.
     *
     * @param ModuleManager $moduleManager
     * @return int
     */
    protected function runAllModuleTests(ModuleManager $moduleManager): int
    {
        $modules = $moduleManager->enabled();
        $totalModules = count($modules);
        $testedModules = 0;
        $failedModules = [];

        $this->info("Running tests for {$totalModules} enabled modules...");

        foreach ($modules as $module) {
            $testPath = $module['path'] . '/tests';
            
            if (!File::exists($testPath)) {
                continue;
            }

            $testedModules++;
            $this->line("Testing module: {$module['name']}");
            
            $command = "vendor/bin/phpunit {$testPath}";
            $exitCode = 0;
            
            \passthru($command, $exitCode);
            
            if ($exitCode !== 0) {
                $failedModules[] = $module['name'];
            }
        }

        $this->newLine();
        $this->info("Test Summary:");
        $this->line("- Total modules: {$totalModules}");
        $this->line("- Modules with tests: {$testedModules}");
        $this->line("- Failed modules: " . count($failedModules));

        if (!empty($failedModules)) {
            $this->error("Failed modules: " . implode(', ', $failedModules));
            return self::FAILURE;
        }

        $this->info("All module tests passed!");
        return self::SUCCESS;
    }
}
