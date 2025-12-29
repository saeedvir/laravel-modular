<?php

declare(strict_types=1);

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;
use Laravel\Modular\Services\ModuleStubService;
use Laravel\Modular\Exceptions\ModuleException;

/**
 * Command to generate a model for a module.
 */
class MakeModuleModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-model {module : The module name} {name : The model name} {--m|migration : Create a new migration file for the model} {--f|factory : Create a new factory for the model} {--s|seed : Create a new seeder for the model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model for a module';

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
        $modelName = $this->argument('name');

        if (!$moduleManager->exists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return self::FAILURE;
        }

        $module = $moduleManager->get($moduleName);
        $modelPath = $module['path'] . '/app/Models/' . $modelName . '.php';

        if (file_exists($modelPath)) {
            $this->error("Model '{$modelName}' already exists!");
            return self::FAILURE;
        }

        try {
            $replacements = $stubService->getReplacements($moduleName);
            $replacements['MODEL_NAME'] = $modelName;

            $stubService->createFromStub(
                'model',
                $modelPath,
                $replacements
            );

            $this->info("Model '{$modelName}' created successfully for module '{$moduleName}'!");

            if ($this->option('migration')) {
                $this->call('module:make-migration', [
                    'module' => $moduleName,
                    'name' => 'create_' . \Illuminate\Support\Str::snake(\Illuminate\Support\Str::plural($modelName)) . '_table'
                ]);
            }

            if ($this->option('factory')) {
                $this->call('module:make-factory', [
                    'module' => $moduleName,
                    'name' => $modelName . 'Factory'
                ]);
            }

            if ($this->option('seed')) {
                $this->call('module:make-seeder', [
                    'module' => $moduleName,
                    'name' => $modelName . 'Seeder'
                ]);
            }

            return self::SUCCESS;

        } catch (ModuleException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
