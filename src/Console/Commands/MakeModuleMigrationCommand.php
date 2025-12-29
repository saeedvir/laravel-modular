<?php

declare(strict_types=1);

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;
use Laravel\Modular\Services\ModuleStubService;
use Laravel\Modular\Exceptions\ModuleException;

/**
 * Command to generate a migration for a module.
 */
class MakeModuleMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-migration {module : The module name} {name : The migration name} {--create= : Create a new table} {--table= : Modify an existing table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration for a module';

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
        $migrationName = $this->argument('name');
        $createTable = $this->option('create');
        $modifyTable = $this->option('table');

        if (!$moduleManager->exists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return self::FAILURE;
        }

        $module = $moduleManager->get($moduleName);
        $timestamp = date('Y_m_d_His');
        $migrationFileName = $timestamp . '_' . $migrationName . '.php';
        $migrationPath = $module['path'] . '/database/migrations/' . $migrationFileName;

        if (file_exists($migrationPath)) {
            $this->error("Migration '{$migrationName}' already exists!");
            return self::FAILURE;
        }

        try {
            $replacements = $stubService->getReplacements($moduleName);
            $replacements['MIGRATION_NAME'] = $migrationName;
            $replacements['TABLE_NAME'] = $createTable ?: $modifyTable ?: strtolower($moduleName) . 's';

            $stubType = 'migration';
            if ($createTable) {
                $stubType = 'create-migration';
                $replacements['TABLE_NAME'] = $createTable;
            } elseif ($modifyTable) {
                $stubType = 'table-migration';
                $replacements['TABLE_NAME'] = $modifyTable;
            }

            $stubService->createFromStub(
                $stubType,
                $migrationPath,
                $replacements
            );

            $this->info("Migration '{$migrationName}' created successfully for module '{$moduleName}'!");
            $this->line("Location: {$migrationPath}");
            
            return self::SUCCESS;

        } catch (ModuleException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
