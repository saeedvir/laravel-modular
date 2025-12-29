<?php

declare(strict_types=1);

namespace Laravel\Modular\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

/**
 * Service for optimizing module migration operations.
 */
class ModuleMigrationService
{
    /**
     * Run migrations for a specific module.
     *
     * @param string $modulePath
     * @return bool
     */
    public function runModuleMigrations(string $modulePath): bool
    {
        $migrationPath = $modulePath . '/database/migrations';

        if (!File::exists($migrationPath)) {
            return true; // No migrations to run
        }

        try {
            Artisan::call('migrate', [
                '--path' => \str_replace(base_path() . '/', '', $migrationPath),
                '--force' => true,
            ]);

            if ($this->isDebugMode()) {
                Log::info('Module migrations completed', ['path' => $migrationPath]);
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Module migration failed', [
                'path' => $migrationPath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get pending migrations for a module.
     *
     * @param string $modulePath
     * @return array<string>
     */
    public function getPendingMigrations(string $modulePath): array
    {
        $migrationPath = $modulePath . '/database/migrations';

        if (!File::exists($migrationPath)) {
            return [];
        }

        $migrationFiles = File::files($migrationPath);
        $pendingMigrations = [];

        foreach ($migrationFiles as $file) {
            if (str_ends_with($file->getFilename(), '.php')) {
                $pendingMigrations[] = $file->getFilename();
            }
        }

        return $pendingMigrations;
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
