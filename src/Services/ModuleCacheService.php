<?php

declare(strict_types=1);

namespace Laravel\Modular\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Service for managing module cache operations.
 */
class ModuleCacheService
{
    private string $cacheKey;
    private int $cacheLifetime;
    private bool $cacheEnabled;

    /**
     * Create a new module cache service instance.
     */
    public function __construct()
    {
        $this->cacheKey = config('module.cache.key', 'laravel_modular_cache');
        $this->cacheLifetime = config('module.cache.lifetime', 86400);
        $this->cacheEnabled = config('module.cache.enabled', true);
    }

    /**
     * Get cached modules data.
     *
     * @return array<string, array<string, mixed>>|null
     */
    public function get(): ?array
    {
        if (!$this->cacheEnabled) {
            return null;
        }

        try {
            $cached = Cache::get($this->cacheKey);

            if ($cached && is_array($cached)) {
                if ($this->isDebugMode()) {
                    Log::debug('Module cache hit', ['key' => $this->cacheKey]);
                }

                return $cached;
            }
        } catch (\Exception $e) {
            if ($this->isDebugMode()) {
                Log::warning('Failed to retrieve module cache', [
                    'key' => $this->cacheKey,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return null;
    }

    /**
     * Store modules data in cache.
     *
     * @param array<string, array<string, mixed>> $modules
     * @return bool
     */
    public function put(array $modules): bool
    {
        if (!$this->cacheEnabled) {
            return false;
        }

        try {
            $success = Cache::put($this->cacheKey, $modules, $this->cacheLifetime);

            if ($success && $this->isDebugMode()) {
                Log::debug('Module cache stored', [
                    'key' => $this->cacheKey,
                    'modules_count' => count($modules),
                    'lifetime' => $this->cacheLifetime
                ]);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('Failed to store module cache', [
                'key' => $this->cacheKey,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Clear module cache.
     *
     * @return bool
     */
    public function clear(): bool
    {
        try {
            $success = Cache::forget($this->cacheKey);

            if ($success && $this->isDebugMode()) {
                Log::info('Module cache cleared', ['key' => $this->cacheKey]);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('Failed to clear module cache', [
                'key' => $this->cacheKey,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Check if cache is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->cacheEnabled;
    }

    /**
     * Get cache key.
     *
     * @return string
     */
    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    /**
     * Get cache lifetime in seconds.
     *
     * @return int
     */
    public function getCacheLifetime(): int
    {
        return $this->cacheLifetime;
    }

    /**
     * Check if application is in debug mode.
     *
     * @return bool
     */
    protected function isDebugMode(): bool
    {
        return config('app.debug', false) && config('module.debug_mode', false);
    }
}
