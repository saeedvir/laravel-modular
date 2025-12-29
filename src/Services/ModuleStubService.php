<?php

declare(strict_types=1);

namespace Laravel\Modular\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Laravel\Modular\Exceptions\ModuleException;

/**
 * Service for managing module stubs and templates.
 */
class ModuleStubService
{
    private string $defaultStubsPath;
    private ?string $customStubsPath;

    /**
     * Create a new module stub service instance.
     */
    public function __construct()
    {
        $this->defaultStubsPath = __DIR__ . '/../stubs';
        $this->customStubsPath = config('module.stubs_path');
    }

    /**
     * Get available templates.
     *
     * @return array<string>
     */
    public function getAvailableTemplates(): array
    {
        $templates = ['default'];

        $stubsPath = $this->getStubsPath();
        if (File::exists($stubsPath . '/templates')) {
            $templateDirs = File::directories($stubsPath . '/templates');
            foreach ($templateDirs as $dir) {
                $templates[] = basename($dir);
            }
        }

        return $templates;
    }

    /**
     * Get stub content for a specific file type.
     *
     * @param string $type
     * @param string $template
     * @return string
     * @throws ModuleException
     */
    public function getStub(string $type, string $template = 'default'): string
    {
        $stubPath = $this->getStubPath($type, $template);

        if (!File::exists($stubPath)) {
            throw ModuleException::moduleNotFound("Stub file not found: {$stubPath}");
        }

        return File::get($stubPath);
    }

    /**
     * Process stub content with replacements.
     *
     * @param string $content
     * @param array<string, string> $replacements
     * @return string
     */
    public function processStub(string $content, array $replacements): string
    {
        foreach ($replacements as $search => $replace) {
            $content = str_replace('{{' . $search . '}}', $replace, $content);
        }

        return $content;
    }

    /**
     * Get replacements for a module.
     *
     * @param string $name
     * @return array<string, string>
     */
    public function getReplacements(string $name): array
    {
        $studlyName = Str::studly($name);
        $lowerName = strtolower($name);
        $kebabName = Str::kebab($name);
        $snakeName = Str::snake($name);

        return [
            'MODULE_NAME' => $name,
            'MODULE_STUDLY' => $studlyName,
            'MODULE_LOWER' => $lowerName,
            'MODULE_KEBAB' => $kebabName,
            'MODULE_SNAKE' => $snakeName,
            'MODULE_NAMESPACE' => "Modules\\{$studlyName}",
            'COMPOSER_MODULE_NAMESPACE' => "Modules\\\\{$studlyName}",
            'MODULE_PROVIDER' => "{$studlyName}ServiceProvider",
            'CURRENT_YEAR' => date('Y'),
            'CURRENT_DATE' => date('Y-m-d'),
            'CURRENT_DATETIME' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * Create file from stub.
     *
     * @param string $stubType
     * @param string $outputPath
     * @param array<string, string> $replacements
     * @param string $template
     * @return bool
     * @throws ModuleException
     */
    public function createFromStub(
        string $stubType,
        string $outputPath,
        array $replacements,
        string $template = 'default'
    ): bool {
        $stubContent = $this->getStub($stubType, $template);
        $processedContent = $this->processStub($stubContent, $replacements);

        // Ensure directory exists
        $directory = dirname($outputPath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        return File::put($outputPath, $processedContent) !== false;
    }

    /**
     * Get the stubs path (custom or default).
     *
     * @return string
     */
    private function getStubsPath(): string
    {
        if ($this->customStubsPath && File::exists($this->customStubsPath)) {
            return $this->customStubsPath;
        }

        return $this->defaultStubsPath;
    }

    /**
     * Get the full path to a specific stub file.
     *
     * @param string $type
     * @param string $template
     * @return string
     */
    private function getStubPath(string $type, string $template): string
    {
        $stubsPath = $this->getStubsPath();

        if ($template !== 'default') {
            $templatePath = $stubsPath . "/templates/{$template}/{$type}.stub";
            if (File::exists($templatePath)) {
                return $templatePath;
            }
        }

        return $stubsPath . "/{$type}.stub";
    }
}
