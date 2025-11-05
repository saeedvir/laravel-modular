<?php

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;

class ListModulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:list {--disabled : Show only disabled modules}
                            {--enabled : Show only enabled modules}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all modules';

    /**
     * Execute the console command.
     */
    public function handle(ModuleManager $moduleManager): int
    {
        $showDisabled = $this->option('disabled');
        $showEnabled = $this->option('enabled');

        // Get modules based on options
        if ($showDisabled) {
            $modules = $moduleManager->disabled();
            $this->info('Disabled Modules:');
        } elseif ($showEnabled) {
            $modules = $moduleManager->enabled();
            $this->info('Enabled Modules:');
        } else {
            $modules = $moduleManager->all();
            $this->info('All Modules:');
        }

        if (empty($modules)) {
            $this->warn('No modules found.');
            return self::SUCCESS;
        }

        // Prepare table data
        $tableData = [];
        foreach ($modules as $module) {
            $tableData[] = [
                $module['name'],
                $module['enabled'] ? '<info>✓ Enabled</info>' : '<comment>✗ Disabled</comment>',
                $module['path'],
                $module['provider'] ?? 'N/A',
            ];
        }

        // Display table
        $this->table(
            ['Name', 'Status', 'Path', 'Provider'],
            $tableData
        );

        $this->newLine();
        $this->info('Total: ' . count($modules) . ' module(s)');

        return self::SUCCESS;
    }
}
