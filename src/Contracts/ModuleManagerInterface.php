<?php

declare(strict_types=1);

namespace Laravel\Modular\Contracts;

/**
 * Interface for module management operations.
 */
interface ModuleManagerInterface
{
    /**
     * Discover all modules in the modules directory.
     *
     * @return array<string, array<string, mixed>>
     */
    public function discover(): array;

    /**
     * Get all modules.
     *
     * @return array<string, array<string, mixed>>
     */
    public function all(): array;

    /**
     * Get enabled modules only.
     *
     * @return array<string, array<string, mixed>>
     */
    public function enabled(): array;

    /**
     * Get disabled modules only.
     *
     * @return array<string, array<string, mixed>>
     */
    public function disabled(): array;

    /**
     * Check if a module exists.
     *
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool;

    /**
     * Get a specific module by name.
     *
     * @param string $name
     * @return array<string, mixed>|null
     */
    public function get(string $name): ?array;

    /**
     * Get the path for modules or a specific module.
     *
     * @param string $name
     * @return string
     */
    public function getPath(string $name = ''): string;

    /**
     * Create a new module.
     *
     * @param string $name
     * @param string|null $template
     * @return bool
     * @throws \Laravel\Modular\Exceptions\ModuleException
     */
    public function create(string $name, ?string $template = null): bool;

    /**
     * Delete a module.
     *
     * @param string $name
     * @return bool
     * @throws \Laravel\Modular\Exceptions\ModuleException
     */
    public function delete(string $name): bool;

    /**
     * Clear module cache.
     *
     * @return void
     */
    public function clearCache(): void;

    /**
     * Get performance metrics for modules.
     *
     * @return array<string, mixed>
     */
    public function getPerformanceMetrics(): array;
}
