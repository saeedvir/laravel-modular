<?php

declare(strict_types=1);

namespace Laravel\Modular;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Modular\Contracts\ModuleManagerInterface;
use Laravel\Modular\Exceptions\ModuleException;
use Laravel\Modular\Services\ModuleCacheService;
use Laravel\Modular\Services\ModulePerformanceService;
use Laravel\Modular\Services\ModuleStubService;
<<<<<<< HEAD
use Laravel\Modular\Services\ModuleStatusService;
=======
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c

/**
 * Module manager for handling module operations.
 *
 * @author Laravel Modular Package
 * @since 1.0.0
 */
class ModuleManager implements ModuleManagerInterface
{
    protected string $modulePath;
    protected array $modules = [];
    protected bool $cached = false;
    protected ModuleCacheService $cacheService;
    protected ModulePerformanceService $performanceService;
    protected ModuleStubService $stubService;
<<<<<<< HEAD
    protected ModuleStatusService $statusService;
=======
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c

    /**
     * Create a new module manager instance.
     *
     * @param ModuleCacheService|null $cacheService
     * @param ModulePerformanceService|null $performanceService
     * @param ModuleStubService|null $stubService
     */
    public function __construct(
        ?ModuleCacheService $cacheService = null,
        ?ModulePerformanceService $performanceService = null,
<<<<<<< HEAD
        ?ModuleStubService $stubService = null,
        ?ModuleStatusService $statusService = null
=======
        ?ModuleStubService $stubService = null
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
    ) {
        $this->modulePath = config('module.path', base_path('modules'));
        $this->cacheService = $cacheService ?? new ModuleCacheService();
        $this->performanceService = $performanceService ?? new ModulePerformanceService();
        $this->stubService = $stubService ?? new ModuleStubService();
<<<<<<< HEAD
        $this->statusService = $statusService ?? new ModuleStatusService($this->modulePath);
=======
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
    }

    /**
     * Scan and discover all modules with caching and performance monitoring.
     *
     * @return array<string, array<string, mixed>>
     */
    public function discover(): array
    {
        $this->performanceService->startTimer('module_discovery');

        // Try to get from cache first
        if ($this->cached && !empty($this->modules)) {
            $this->performanceService->stopTimer('module_discovery', ['source' => 'memory_cache']);
            return $this->modules;
        }

        $cachedModules = $this->cacheService->get();
        if ($cachedModules !== null) {
            $this->modules = $cachedModules;
            $this->cached = true;
            $this->performanceService->stopTimer('module_discovery', ['source' => 'cache']);
            return $this->modules;
        }

        $this->modules = [];

        if (!File::exists($this->modulePath)) {
            if ($this->isDebugMode()) {
                Log::warning('Module path does not exist', ['path' => $this->modulePath]);
            }
            $this->performanceService->stopTimer('module_discovery', ['source' => 'filesystem', 'modules_found' => 0]);
            return $this->modules;
        }

        try {
            $directories = File::directories($this->modulePath);

            foreach ($directories as $directory) {
                $moduleName = basename($directory);
                $composerFile = $directory . '/composer.json';

                if (File::exists($composerFile)) {
                    try {
                        $this->modules[$moduleName] = [
                            'name' => $moduleName,
                            'path' => $directory,
                            'enabled' => $this->isModuleEnabled($moduleName),
                            'provider' => $this->getModuleProvider($moduleName, $composerFile),
                        ];
                    } catch (\Exception $e) {
                        Log::error("Failed to process module: {$moduleName}", [
                            'error' => $e->getMessage(),
                            'path' => $directory
                        ]);
                    }
                }
            }

            // Cache the results
            $this->cacheService->put($this->modules);
            $this->cached = true;

            if ($this->isDebugMode()) {
                Log::info('Module discovery completed', [
                    'modules_found' => count($this->modules),
                    'enabled_modules' => count(array_filter($this->modules, fn($m) => $m['enabled']))
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Module discovery failed', ['error' => $e->getMessage()]);
            throw new ModuleException('Failed to discover modules: ' . $e->getMessage(), 0, $e);
        }

        $this->performanceService->stopTimer('module_discovery', [
            'source' => 'filesystem',
            'modules_found' => count($this->modules)
        ]);

        return $this->modules;
    }

    /**
     * Get all modules
     */
    public function all(): array
    {
        return $this->discover();
    }

    /**
     * Get enabled modules
     */
    public function enabled(): array
    {
        return array_filter($this->discover(), fn($module) => $module['enabled']);
    }

    /**
     * Get disabled modules
     */
    public function disabled(): array
    {
        return array_filter($this->discover(), fn($module) => !$module['enabled']);
    }

    /**
     * Check if module exists
     */
    public function exists(string $name): bool
    {
        $this->discover();
        return isset($this->modules[$name]);
    }

    /**
     * Get module by name
     */
    public function get(string $name): ?array
    {
        $this->discover();
        return $this->modules[$name] ?? null;
    }

    /**
     * Get module path
     */
    public function getPath(string $name = ''): string
    {
        return $name ? $this->modulePath . '/' . $name : $this->modulePath;
    }

    /**
     * Check if application is in debug mode.
     */
<<<<<<< HEAD
    public function isDebugMode(): bool
    {
        return (bool) config('module.debug', config('app.debug', false));
=======
    protected function isDebugMode(): bool
    {
        return config('app.debug', false) && config('module.debug_mode', true);
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
    }

    /**
     * Check if module is enabled
     */
    protected function isModuleEnabled(string $name): bool
    {
<<<<<<< HEAD
        // Persistent state takes precedence
        $persistentStatus = $this->statusService->getStatus($name, true);
        
        // Check config as secondary/fallback if not explicitly disabled in persistent state
        if ($persistentStatus) {
            $disabledModules = config('module.disabled', []);
            return !in_array($name, $disabledModules);
        }

        return false;
    }

    /**
     * Enable a module.
     *
     * @param string $name
     * @return void
     */
    public function enable(string $name): void
    {
        $this->statusService->setStatus($name, true);
        $this->clearCache();
    }

    /**
     * Disable a module.
     *
     * @param string $name
     * @return void
     */
    public function disable(string $name): void
    {
        $this->statusService->setStatus($name, false);
        $this->clearCache();
=======
        $disabledModules = config('module.disabled', []);
        return !in_array($name, $disabledModules);
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
    }


    /**
     * Create a new module with validation and error handling.
     *
     * @param string $name
     * @param string|null $template
     * @return bool
     * @throws ModuleException
     */
    public function create(string $name, ?string $template = null): bool
    {
        $this->performanceService->startTimer('module_creation');

        // Validate module name
        $this->validateModuleName($name);

        $modulePath = $this->getPath($name);

        if (File::exists($modulePath)) {
            throw ModuleException::moduleAlreadyExists($name);
        }

        // Check permissions and disk space
        $this->validateSystemRequirements($modulePath);

        try {
            // Create module directory structure
            $this->createModuleStructure($name, $modulePath, $template);

            // Clear cache
            $this->clearCache();

            if ($this->isDebugMode()) {
                Log::info("Module created successfully", [
                    'name' => $name,
                    'path' => $modulePath,
                    'template' => $template ?? 'default'
                ]);
            }

            $this->performanceService->stopTimer('module_creation', [
                'module_name' => $name,
                'template' => $template ?? 'default'
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to create module: {$name}", [
                'error' => $e->getMessage(),
                'path' => $modulePath
            ]);

            // Clean up partial creation
            if (File::exists($modulePath)) {
                File::deleteDirectory($modulePath);
            }

            throw new ModuleException("Failed to create module '{$name}': " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Delete a module
     */
    public function delete(string $name): bool
    {
        if (!$this->exists($name)) {
            return false;
        }

        $modulePath = $this->getPath($name);
        File::deleteDirectory($modulePath);

        // Clear cache
        $this->cached = false;
        unset($this->modules[$name]);

        return true;
    }


    /**
     * Create route files for module
     */
    protected function createRouteFiles(string $name, string $studlyName, string $path): void
    {
<<<<<<< HEAD
        $moduleViewNameSpace = strtolower(str_replace("","", $name));
=======
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
        // web.php
        $webContent = "<?php\n\n";
        $webContent .= "use Illuminate\\Support\\Facades\\Route;\n\n";
        $webContent .= "/*\n";
        $webContent .= "|--------------------------------------------------------------------------\n";
        $webContent .= "| Web Routes\n";
        $webContent .= "|--------------------------------------------------------------------------\n";
        $webContent .= "|\n";
        $webContent .= "| Here is where you can register web routes for your {$studlyName} module.\n";
        $webContent .= "|\n";
        $webContent .= "*/\n\n";
<<<<<<< HEAD
        $webContent .= "Route::prefix('{$moduleViewNameSpace}')->group(function () {\n";
        $webContent .= "    Route::get('/', function () {\n";
        $webContent .= "        return view('{$moduleViewNameSpace}::index');\n";
        $webContent .= "    });\n";
=======
        $webContent .= "Route::prefix('{$name}')->group(function () {\n";
        $webContent .= "    // Add your web routes here\n";
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
        $webContent .= "});\n";

        File::put($path . '/routes/web.php', $webContent);

        // api.php
        $apiContent = "<?php\n\n";
        $apiContent .= "use Illuminate\\Support\\Facades\\Route;\n\n";
        $apiContent .= "/*\n";
        $apiContent .= "|--------------------------------------------------------------------------\n";
        $apiContent .= "| API Routes\n";
        $apiContent .= "|--------------------------------------------------------------------------\n";
        $apiContent .= "|\n";
        $apiContent .= "| Here is where you can register API routes for your {$studlyName} module.\n";
        $apiContent .= "|\n";
        $apiContent .= "*/\n\n";
        $apiContent .= "Route::prefix('{$name}')->group(function () {\n";
        $apiContent .= "    // Add your API routes here\n";
        $apiContent .= "});\n";

        File::put($path . '/routes/api.php', $apiContent);

        // Create config file
        $configContent = "<?php\n\n";
        $configContent .= "return [\n";
        $configContent .= "    'name' => '{$studlyName}',\n";
        $configContent .= "    'enabled' => true,\n";
        $configContent .= "];\n";

        File::put($path . '/config/config.php', $configContent);
    }

    /**
     * Clear module cache.
     *
     * @return void
     */
    public function clearCache(): void
    {
        $this->cacheService->clear();
        $this->cached = false;
        $this->modules = [];

        if ($this->isDebugMode()) {
            Log::info('Module cache cleared');
        }
    }

    /**
     * Get performance metrics for modules.
     *
     * @return array<string, mixed>
     */
    public function getPerformanceMetrics(): array
    {
        return $this->performanceService->getSummary();
    }

    /**
     * Validate module name.
     *
     * @param string $name
     * @throws ModuleException
     */
    protected function validateModuleName(string $name): void
    {
        if (empty($name)) {
            throw ModuleException::invalidModuleName($name);
        }

<<<<<<< HEAD
        // Must start with a letter and contain only alphanumeric characters, underscores, or hyphens
        if (!preg_match('/^[A-Za-z][A-Za-z0-9_-]*$/', $name)) {
            throw ModuleException::invalidModuleName($name);
        }

        if (\strlen($name) > 50) {
            throw ModuleException::invalidModuleName($name . " (too long)");
        }

        // Avoid reserved words
        $reserved = ['app', 'config', 'database', 'public', 'resources', 'routes', 'storage', 'tests', 'vendor', 'module', 'modules'];
        if (in_array(strtolower($name), $reserved)) {
            throw new ModuleException("The name '{$name}' is reserved and cannot be used as a module name.");
=======
        if (!preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $name)) {
            throw ModuleException::invalidModuleName($name);
        }

        if (strlen($name) > 50) {
            throw ModuleException::invalidModuleName($name);
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
        }
    }

    /**
     * Validate system requirements for module creation.
     *
     * @param string $path
     * @throws ModuleException
     */
    protected function validateSystemRequirements(string $path): void
    {
        $parentDir = dirname($path);

        // Check if parent directory exists and is writable
        if (!File::exists($parentDir)) {
            if (!File::makeDirectory($parentDir, 0755, true)) {
                throw ModuleException::insufficientPermissions($parentDir);
            }
        }

<<<<<<< HEAD
        if (!\is_writable($parentDir)) {
=======
        if (!is_writable($parentDir)) {
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
            throw ModuleException::insufficientPermissions($parentDir);
        }

        // Check available disk space (require at least 10MB)
<<<<<<< HEAD
        $freeBytes = \disk_free_space($parentDir);
=======
        $freeBytes = disk_free_space($parentDir);
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
        if ($freeBytes !== false && $freeBytes < 10 * 1024 * 1024) {
            throw ModuleException::insufficientDiskSpace();
        }
    }

    /**
     * Create module directory structure with template support.
     *
     * @param string $name
     * @param string $path
     * @param string|null $template
     * @throws ModuleException
     */
    protected function createModuleStructure(string $name, string $path, ?string $template = null): void
    {
        $studlyName = Str::studly($name);
        $template = $template ?? 'default';

        // Create directories
        $directories = [
            'app/Console',
            'app/Enums',
            'app/Helpers',
            'app/Http/Controllers',
            'app/Http/Controllers/Api',
            'app/Http/Middleware',
            'app/Http/Requests',
            'app/Http/Resources',
            'app/Models',
            'app/Providers',
            'app/Services',
            'app/View',
            'config',
            'database/migrations',
            'database/seeders',
            'database/factories',
            'routes',
            'resources/views',
            'resources/lang/en',
        ];

        foreach ($directories as $directory) {
            File::makeDirectory($path . '/' . $directory, 0755, true);
        }

        // Get replacements for stubs
        $replacements = $this->stubService->getReplacements($name);

        try {
            // Create composer.json
            $this->stubService->createFromStub(
                'composer',
                $path . '/composer.json',
                $replacements,
                $template
            );

            // Create service provider
            $this->stubService->createFromStub(
                'service-provider',
                $path . '/app/Providers/' . $studlyName . 'ServiceProvider.php',
                $replacements,
                $template
            );

            // Create controller
            $this->stubService->createFromStub(
                'controller',
                $path . '/app/Http/Controllers/' . $studlyName . 'Controller.php',
                $replacements,
                $template
            );

            // Create API controller
            $this->stubService->createFromStub(
                'api-controller',
                $path . '/app/Http/Controllers/Api/' . $studlyName . 'Controller.php',
                $replacements,
                $template
            );

            // Create model if not API-only template
            if ($template !== 'api') {
                $this->stubService->createFromStub(
                    'model',
                    $path . '/app/Models/' . $studlyName . '.php',
                    $replacements,
                    $template
                );
            }

            // Create request
            $this->stubService->createFromStub(
                'request',
                $path . '/app/Http/Requests/' . $studlyName . 'Request.php',
                $replacements,
                $template
            );

            // Create resource
            $this->stubService->createFromStub(
                'resource',
                $path . '/app/Http/Resources/' . $studlyName . 'Resource.php',
                $replacements,
                $template
            );
        } catch (\Exception $e) {
<<<<<<< HEAD
            Log::error("Failed to create module resources for {$name}", [
                'error' => $e->getMessage(),
                'path' => $path
            ]);
            throw new ModuleException("Failed to generate module files: " . $e->getMessage(), 0, $e);
=======
            // Fall back to legacy creation if stubs fail
            if ($this->isDebugMode()) {
                Log::warning('Stub creation failed, falling back to legacy method', [
                    'error' => $e->getMessage()
                ]);
            }

            $this->createLegacyModuleFiles($name, $studlyName, $path);
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
        }

        // Create route files
        $this->createRouteFiles($name, $studlyName, $path);

        // Create .gitkeep files
        File::put($path . '/resources/views/.gitkeep', '');
        File::put($path . '/resources/lang/en/.gitkeep', '');
    }

<<<<<<< HEAD
=======
    /**
     * Create module files using legacy method (fallback).
     *
     * @param string $name
     * @param string $studlyName
     * @param string $path
     */
    protected function createLegacyModuleFiles(string $name, string $studlyName, string $path): void
    {
        // Create composer.json
        $this->createComposerJson($name, $studlyName, $path);

        // Create service provider
        $this->createServiceProvider($name, $studlyName, $path);
    }
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c

    /**
     * Get module service provider class with error handling.
     *
     * @param string $name
     * @param string $composerFile
     * @return string|null
     */
    protected function getModuleProvider(string $name, string $composerFile): ?string
    {
        try {
            $composerContent = File::get($composerFile);
            $composer = json_decode($composerContent, true);

<<<<<<< HEAD
            if (\json_last_error() !== JSON_ERROR_NONE) {
=======
            if (json_last_error() !== JSON_ERROR_NONE) {
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
                throw ModuleException::invalidJson($composerFile);
            }

            if (isset($composer['extra']['laravel']['providers'][0])) {
                return $composer['extra']['laravel']['providers'][0];
            }

            // Default provider class name
            $studlyName = Str::studly($name);
            return "Modules\\{$studlyName}\\Providers\\{$studlyName}ServiceProvider";
        } catch (\Exception $e) {
            Log::error("Failed to read module provider from composer.json", [
                'file' => $composerFile,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }
}
