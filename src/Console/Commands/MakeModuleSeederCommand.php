<?php

declare(strict_types=1);

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;
use Laravel\Modular\Services\ModuleStubService;
use Laravel\Modular\Exceptions\ModuleException;

/**
 * Command to generate a seeder for a module.
 */
class MakeModuleSeederCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-seeder {module : The module name} {name : The seeder name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new seeder for a module';

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
        $seederName = $this->argument('name');

        if (!$moduleManager->exists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return self::FAILURE;
        }

        $module = $moduleManager->get($moduleName);
        $seederPath = $module['path'] . '/database/seeders/' . $seederName . '.php';

        if (file_exists($seederPath)) {
            $this->error("Seeder '{$seederName}' already exists!");
            return self::FAILURE;
        }

        try {
            $replacements = $stubService->getReplacements($moduleName);
            $replacements['SEEDER_NAME'] = $seederName;

            $stubService->createFromStub(
                'seeder',
                $seederPath,
                $replacements
            );

            $this->info("Seeder '{$seederName}' created successfully for module '{$moduleName}'!");
            $this->line("Location: {$seederPath}");
            
            return self::SUCCESS;

        } catch (ModuleException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
