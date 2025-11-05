<?php

declare(strict_types=1);

namespace Laravel\Modular\Exceptions;

use Exception;

/**
 * Base exception for module-related errors.
 */
class ModuleException extends Exception
{
    /**
     * Create a new module exception instance.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create exception for module already exists.
     *
     * @param string $name
     * @return static
     */
    public static function moduleAlreadyExists(string $name): static
    {
        return new static("Module '{$name}' already exists.");
    }

    /**
     * Create exception for module not found.
     *
     * @param string $name
     * @return static
     */
    public static function moduleNotFound(string $name): static
    {
        return new static("Module '{$name}' not found.");
    }

    /**
     * Create exception for invalid module name.
     *
     * @param string $name
     * @return static
     */
    public static function invalidModuleName(string $name): static
    {
        return new static("Invalid module name '{$name}'. Module names must be valid PHP class names.");
    }

    /**
     * Create exception for insufficient permissions.
     *
     * @param string $path
     * @return static
     */
    public static function insufficientPermissions(string $path): static
    {
        return new static("Insufficient permissions to write to '{$path}'.");
    }

    /**
     * Create exception for insufficient disk space.
     *
     * @return static
     */
    public static function insufficientDiskSpace(): static
    {
        return new static('Insufficient disk space to create module.');
    }

    /**
     * Create exception for invalid JSON.
     *
     * @param string $file
     * @return static
     */
    public static function invalidJson(string $file): static
    {
        return new static("Invalid JSON in file '{$file}'.");
    }
}
