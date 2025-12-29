<?php

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;
use Illuminate\Support\Facades\File;

class MakeModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make {name : The name of the module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module';

    /**
     * Execute the console command.
     */
    public function handle(ModuleManager $moduleManager): int
    {
        $name = $this->argument('name');

        if ($moduleManager->exists($name)) {
            $this->error("Module '{$name}' already exists!");
            return self::FAILURE;
        }

        $this->info("Creating module '{$name}'...");

        if ($moduleManager->create($name)) {
            $this->info("Module '{$name}' created successfully!");
            $this->info("Run 'composer dump-autoload' to register the new module.");
            
            return self::SUCCESS;
        }

        $this->error("Failed to create module '{$name}'.");
        return self::FAILURE;
    }

}
