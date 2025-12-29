<?php

declare(strict_types=1);

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;

/**
 * Command to show module status.
 */
class ModuleStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the status of all modules';

    /**
     * Execute the console command.
     *
     * @param ModuleManager $moduleManager
     * @return int
     */
    public function handle(ModuleManager $moduleManager): int
    {
        $modules = $moduleManager->all();

        if (empty($modules)) {
            $this->info('No modules found.');
            return self::SUCCESS;
        }

        $headers = ['Module', 'Status', 'Namespace', 'Provider'];
        $rows = [];

        foreach ($modules as $name => $module) {
            $rows[] = [
                $name,
                $module['enabled'] ? '<info>Enabled</info>' : '<comment>Disabled</comment>',
                "Modules\\\\" . \Illuminate\Support\Str::studly($name),
                $module['provider'] ?? 'N/A',
            ];
        }

        $this->table($headers, $rows);

        return self::SUCCESS;
    }
}
