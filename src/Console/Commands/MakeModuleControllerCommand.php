<?php

declare(strict_types=1);

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;
use Laravel\Modular\Services\ModuleStubService;
use Laravel\Modular\Exceptions\ModuleException;

/**
 * Command to generate a controller for a module.
 */
class MakeModuleControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-controller {module : The module name} {name : The controller name} {--api : Generate API controller} {--resource : Generate resource controller}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller for a module';

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
        $controllerName = $this->argument('name');
        $isApi = $this->option('api');
        $isResource = $this->option('resource');

        if (!$moduleManager->exists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return self::FAILURE;
        }

        $module = $moduleManager->get($moduleName);
        $controllerPath = $isApi 
            ? $module['path'] . '/app/Http/Controllers/Api/' . $controllerName . '.php'
            : $module['path'] . '/app/Http/Controllers/' . $controllerName . '.php';

        if (file_exists($controllerPath)) {
            $this->error("Controller '{$controllerName}' already exists!");
            return self::FAILURE;
        }

        try {
            $replacements = $stubService->getReplacements($moduleName);
            $replacements['CONTROLLER_NAME'] = $controllerName;

            $stubType = $isApi ? 'api-controller' : 'controller';
            if ($isResource) {
                $stubType = $isApi ? 'api-resource-controller' : 'resource-controller';
            }

            $stubService->createFromStub(
                $stubType,
                $controllerPath,
                $replacements
            );

            $this->info("Controller '{$controllerName}' created successfully for module '{$moduleName}'!");
            return self::SUCCESS;

        } catch (ModuleException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
