<?php

declare(strict_types=1);

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;

/**
 * Command to manage module cache.
 */
class ModuleCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:cache {action : The cache action (clear|status)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage module cache (clear, status)';

    /**
     * Execute the console command.
     *
     * @param ModuleManager $moduleManager
     * @return int
     */
    public function handle(ModuleManager $moduleManager): int
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'clear':
                $moduleManager->clearCache();
                $this->info('Module cache cleared successfully!');
                break;

            case 'status':
                $this->displayCacheStatus($moduleManager);
                break;

            default:
                $this->error("Unknown action '{$action}'. Available actions: clear, status");
                return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Display cache status information.
     *
     * @param ModuleManager $moduleManager
     * @return void
     */
    protected function displayCacheStatus(ModuleManager $moduleManager): void
    {
        $cacheService = app(\Laravel\Modular\Services\ModuleCacheService::class);
        
        $this->info('Module Cache Status:');
        $this->line('');
        
        $this->table([
            'Setting',
            'Value'
        ], [
            ['Cache Enabled', $cacheService->isEnabled() ? '✓ Yes' : '✗ No'],
            ['Cache Key', $cacheService->getCacheKey()],
            ['Cache Lifetime', $cacheService->getCacheLifetime() . ' seconds'],
            ['Cached Data', $cacheService->get() !== null ? '✓ Available' : '✗ Not cached'],
        ]);

        $metrics = $moduleManager->getPerformanceMetrics();
        if (!empty($metrics)) {
            $this->line('');
            $this->info('Performance Metrics:');
            $this->table([
                'Metric',
                'Value'
            ], [
                ['Total Operations', $metrics['total_operations']],
                ['Total Execution Time', round($metrics['total_execution_time'] * 1000, 2) . ' ms'],
                ['Average Execution Time', round($metrics['average_execution_time'] * 1000, 2) . ' ms'],
                ['Peak Memory Usage', round($metrics['peak_memory_usage'] / 1024 / 1024, 2) . ' MB'],
            ]);
        }
    }
}
