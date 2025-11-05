<?php

declare(strict_types=1);

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;
use Laravel\Modular\Services\ModuleStubService;
use Laravel\Modular\Exceptions\ModuleException;

/**
 * Command to generate a factory for a module.
 */
class MakeModuleFactoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-factory {module : The module name} {name : The factory name} {--model= : The model name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new factory for a module';

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
        $factoryName = $this->argument('name');
        $modelName = $this->option('model') ?: str_replace('Factory', '', $factoryName);

        if (!$moduleManager->exists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return self::FAILURE;
        }

        $module = $moduleManager->get($moduleName);
        $factoryPath = $module['path'] . '/database/factories/' . $factoryName . '.php';

        if (file_exists($factoryPath)) {
            $this->error("Factory '{$factoryName}' already exists!");
            return self::FAILURE;
        }

        try {
            $replacements = $stubService->getReplacements($moduleName);
            $replacements['FACTORY_NAME'] = $factoryName;
            $replacements['MODEL_NAME'] = $modelName;

            $stubService->createFromStub(
                'factory',
                $factoryPath,
                $replacements
            );

            $this->info("Factory '{$factoryName}' created successfully for module '{$moduleName}'!");
            $this->line("Location: {$factoryPath}");
            
            return self::SUCCESS;

        } catch (ModuleException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
