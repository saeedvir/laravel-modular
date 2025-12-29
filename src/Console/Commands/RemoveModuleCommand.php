<?php

namespace Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Modular\ModuleManager;
use Illuminate\Support\Facades\File;

class RemoveModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:remove {name : The name of the module}
                            {--force : Force delete without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove an existing module';

    /**
     * Execute the console command.
     */
    public function handle(ModuleManager $moduleManager): int
    {
        $name = $this->argument('name');

        if (!$moduleManager->exists($name)) {
            $this->error("Module '{$name}' does not exist!");
            return self::FAILURE;
        }

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm("Are you sure you want to delete module '{$name}'? This action cannot be undone.")) {
                $this->info('Operation cancelled.');
                return self::SUCCESS;
            }
        }

        $this->info("Removing module '{$name}'...");

        if ($moduleManager->delete($name)) {
<<<<<<< HEAD
            $this->info("Module '{$name}' removed successfully!");
            $this->info("Run 'composer dump-autoload' to refresh mappings.");
=======
            // Remove from composer.json
            $this->removeFromComposerAutoload($name);
            
            $this->info("Module '{$name}' removed successfully!");
            $this->info("Don't forget to run: composer dump-autoload");
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
            
            return self::SUCCESS;
        }

        $this->error("Failed to remove module '{$name}'.");
        return self::FAILURE;
    }

<<<<<<< HEAD
=======
    /**
     * Remove module from composer.json autoloading
     */
    protected function removeFromComposerAutoload(string $name): void
    {
        $composerPath = base_path('composer.json');
        $composer = json_decode(File::get($composerPath), true);

        $studlyName = \Illuminate\Support\Str::studly($name);
        $namespace = "Modules\\{$studlyName}\\";

        if (isset($composer['autoload']['psr-4'][$namespace])) {
            unset($composer['autoload']['psr-4'][$namespace]);
            File::put($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->info("Removed module from composer.json autoloading.");
        }
    }
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c
}
