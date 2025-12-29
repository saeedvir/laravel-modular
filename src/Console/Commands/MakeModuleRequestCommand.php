<?php

declare(strict_types=1);

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;
use Laravel\Modular\Services\ModuleStubService;
use Laravel\Modular\Exceptions\ModuleException;

/**
 * Command to generate a form request for a module.
 */
class MakeModuleRequestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-request {module : The module name} {name : The request name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new form request for a module';

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
        $requestName = $this->argument('name');

        if (!$moduleManager->exists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return self::FAILURE;
        }

        $module = $moduleManager->get($moduleName);
        $requestPath = $module['path'] . '/app/Http/Requests/' . $requestName . '.php';

        if (file_exists($requestPath)) {
            $this->error("Request '{$requestName}' already exists!");
            return self::FAILURE;
        }

        try {
            $replacements = $stubService->getReplacements($moduleName);
            $replacements['REQUEST_NAME'] = $requestName;

            $stubService->createFromStub(
                'request',
                $requestPath,
                $replacements
            );

            $this->info("Request '{$requestName}' created successfully for module '{$moduleName}'!");
            return self::SUCCESS;

        } catch (ModuleException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
