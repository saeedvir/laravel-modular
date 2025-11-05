<?php

declare(strict_types=1);

namespace Laravel\Modular\Services;

use Illuminate\Support\Facades\Log;

/**
 * Service for monitoring module performance metrics.
 */
class ModulePerformanceService
{
    private array $metrics = [];
    private array $timers = [];

    /**
     * Start timing an operation.
     *
     * @param string $operation
     * @return void
     */
    public function startTimer(string $operation): void
    {
        $this->timers[$operation] = [
            'start' => microtime(true),
            'memory_start' => memory_get_usage(true),
        ];
    }

    /**
     * Stop timing an operation and record metrics.
     *
     * @param string $operation
     * @param array<string, mixed> $context
     * @return void
     */
    public function stopTimer(string $operation, array $context = []): void
    {
        if (!isset($this->timers[$operation])) {
            return;
        }

        $timer = $this->timers[$operation];
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $executionTime = $endTime - $timer['start'];
        $memoryUsage = $endMemory - $timer['memory_start'];

        $this->metrics[$operation] = [
            'execution_time' => $executionTime,
            'memory_usage' => $memoryUsage,
            'peak_memory' => memory_get_peak_usage(true),
            'context' => $context,
            'timestamp' => now()->toISOString(),
        ];

        unset($this->timers[$operation]);

        if($this->isDebugMode()){
            Log::debug("Performance metric recorded: {$operation}", [
            'execution_time_ms' => round($executionTime * 1000, 2),
            'memory_usage_mb' => round($memoryUsage / 1024 / 1024, 2),
            'context' => $context,
        ]);
        }

    }

    /**
     * Get all recorded metrics.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getMetrics(): array
    {
        return $this->metrics;
    }

    /**
     * Get metrics for a specific operation.
     *
     * @param string $operation
     * @return array<string, mixed>|null
     */
    public function getMetric(string $operation): ?array
    {
        return $this->metrics[$operation] ?? null;
    }

    /**
     * Clear all metrics.
     *
     * @return void
     */
    public function clearMetrics(): void
    {
        $this->metrics = [];
        $this->timers = [];
    }

    /**
     * Get summary of performance metrics.
     *
     * @return array<string, mixed>
     */
    public function getSummary(): array
    {
        if (empty($this->metrics)) {
            return [
                'total_operations' => 0,
                'total_execution_time' => 0,
                'total_memory_usage' => 0,
                'average_execution_time' => 0,
                'peak_memory_usage' => memory_get_peak_usage(true),
            ];
        }

        $totalExecutionTime = array_sum(array_column($this->metrics, 'execution_time'));
        $totalMemoryUsage = array_sum(array_column($this->metrics, 'memory_usage'));
        $operationCount = count($this->metrics);

        return [
            'total_operations' => $operationCount,
            'total_execution_time' => $totalExecutionTime,
            'total_memory_usage' => $totalMemoryUsage,
            'average_execution_time' => $totalExecutionTime / $operationCount,
            'peak_memory_usage' => max(array_column($this->metrics, 'peak_memory')),
            'operations' => array_keys($this->metrics),
        ];
    }

        /**
     * Check if application is in debug mode.
     *
     * @return bool
     */
    protected function isDebugMode(): bool
    {
        return config('app.debug', false);
    }
}
