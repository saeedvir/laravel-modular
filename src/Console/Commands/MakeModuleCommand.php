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
            
            // Update composer.json to include module autoloading
            $this->updateComposerAutoload($name, $moduleManager);
            
            $this->info("Don't forget to run: composer dump-autoload");
            
            return self::SUCCESS;
        }

        $this->error("Failed to create module '{$name}'.");
        return self::FAILURE;
    }

    /**
     * Update composer.json with module autoloading
     */
    protected function updateComposerAutoload(string $name, ModuleManager $moduleManager): void
    {
        $composerPath = base_path('composer.json');
        $composer = json_decode(File::get($composerPath), true);

        $module = $moduleManager->get($name);
        $moduleComposerPath = $module['path'] . '/composer.json';
        
        if (File::exists($moduleComposerPath)) {
            $moduleComposer = json_decode(File::get($moduleComposerPath), true);
            
            if (isset($moduleComposer['autoload']['psr-4'])) {
                foreach ($moduleComposer['autoload']['psr-4'] as $namespace => $path) {
                    $fullPath = 'modules/' . $name . '/' . $path;
                    $composer['autoload']['psr-4'][$namespace] = $fullPath;
                }
                
                File::put($composerPath, json_encode($composer, JSON_PRETTY_PRINT));
                $this->info("Updated composer.json with module autoloading.");
            }
        }
    }
}
