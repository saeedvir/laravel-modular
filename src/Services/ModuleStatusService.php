<?php

declare(strict_types=1);

namespace Laravel\Modular\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * Service for managing persistent module status.
 */
class ModuleStatusService
{
    protected string $statusFilePath;
    protected array $statuses = [];
    protected bool $loaded = false;

    /**
     * Create a new module status service instance.
     *
     * @param string $modulesPath
     */
    public function __construct(string $modulesPath)
    {
        $this->statusFilePath = $modulesPath . '/modules.json';
    }

    /**
     * Get module status (enabled/disabled).
     *
     * @param string $name
     * @param bool $default
     * @return bool
     */
    public function getStatus(string $name, bool $default = true): bool
    {
        $this->load();
        return $this->statuses[$name] ?? $default;
    }

    /**
     * Set module status.
     *
     * @param string $name
     * @param bool $active
     * @return void
     */
    public function setStatus(string $name, bool $active): void
    {
        $this->load();
        $this->statuses[$name] = $active;
        $this->save();
    }

    /**
     * Get all module statuses.
     *
     * @return array<string, bool>
     */
    public function all(): array
    {
        $this->load();
        return $this->statuses;
    }

    /**
     * Load statuses from file.
     *
     * @return void
     */
    protected function load(): void
    {
        if ($this->loaded) {
            return;
        }

        if (File::exists($this->statusFilePath)) {
            try {
                $content = File::get($this->statusFilePath);
                $data = json_decode($content, true);
                if (is_array($data)) {
                    $this->statuses = $data;
                }
            } catch (\Exception $e) {
                Log::error("Failed to load module statuses from {$this->statusFilePath}", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->loaded = true;
    }

    /**
     * Save statuses to file.
     *
     * @return void
     */
    protected function save(): void
    {
        try {
            $directory = dirname($this->statusFilePath);
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            File::put(
                $this->statusFilePath,
                json_encode($this->statuses, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
        } catch (\Exception $e) {
            Log::error("Failed to save module statuses to {$this->statusFilePath}", [
                'error' => $e->getMessage()
            ]);
        }
    }
}
