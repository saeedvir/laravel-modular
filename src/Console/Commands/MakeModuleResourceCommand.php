<?php

declare(strict_types=1);

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;
use Laravel\Modular\Services\ModuleStubService;
use Laravel\Modular\Exceptions\ModuleException;

/**
 * Command to generate an API resource for a module.
 */
class MakeModuleResourceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-resource {module : The module name} {name : The resource name} {--collection : Generate a resource collection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API resource for a module';

    /**
     * Execute the console command.
     *
     * @param ModuleManager $moduleManager
     * @param ModuleStubService $stubService
     * @return int
     */
    public function handle(ModuleManager $moduleManager, ModuleStubService $stubService): int
    {
        $moduleName = $this->argument('module');
        $resourceName = $this->argument('name');
        $isCollection = $this->option('collection');

        if (!$moduleManager->exists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return self::FAILURE;
        }

        $module = $moduleManager->get($moduleName);
        $resourcePath = $module['path'] . '/app/Http/Resources/' . $resourceName . '.php';

        if (file_exists($resourcePath)) {
            $this->error("Resource '{$resourceName}' already exists!");
            return self::FAILURE;
        }

        try {
            $replacements = $stubService->getReplacements($moduleName);
            $replacements['RESOURCE_NAME'] = $resourceName;

            $stubType = $isCollection ? 'resource-collection' : 'resource';

            $stubService->createFromStub(
                $stubType,
                $resourcePath,
                $replacements
            );

            $this->info("Resource '{$resourceName}' created successfully for module '{$moduleName}'!");
            return self::SUCCESS;

        } catch (ModuleException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
