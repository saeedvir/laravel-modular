<?php

namespace Laravel\Modular\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array discover()
 * @method static array all()
 * @method static array enabled()
 * @method static array disabled()
 * @method static bool exists(string $name)
 * @method static array|null get(string $name)
 * @method static string getPath(string $name = '')
 * @method static bool create(string $name)
 * @method static bool delete(string $name)
 *
 * @see \Laravel\Modular\ModuleManager
 */
class Module extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \Laravel\Modular\ModuleManager::class;
    }
}
