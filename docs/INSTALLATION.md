# Installation Guide

This guide will walk you through installing and setting up **Laravel Modular** in your Laravel application.

## 📋 Requirements

Before installing, ensure your system meets these requirements:

- **PHP**: ^8.2 or higher
- **Laravel**: ^11.0 or ^12.0
- **Composer**: Latest version recommended
- **Extensions**: 
  - `php-json`
  - `php-mbstring`
  - `php-fileinfo`

## 📦 Installation

### Step 1: Install via Composer

Install the package using Composer:

```bash
composer require your-vendor/laravel-modular
```

The package will automatically register itself via Laravel's package auto-discovery feature.

### Step 2: Publish Configuration (Optional)

If you want to customize the package configuration, publish the config file:

```bash
php artisan vendor:publish --tag=module-config
```

This creates `config/module.php` with the following default settings:

```php
return [
    'path' => base_path('modules'),
    'disabled' => [],
    'cache' => [
        'enabled' => env('MODULE_CACHE_ENABLED', true),
        'key' => 'laravel_modular_cache',
        'lifetime' => 86400, // 24 hours
    ],
    'auto_register_routes' => true,
    'stubs_path' => null,
];
```

### Step 3: Set Up Composer Merge Plugin

The package uses `wikimedia/composer-merge-plugin` for automatic module autoloading. It's already included as a dependency, but you need to configure it in your **root** `composer.json`.

#### 3.1: Configure Merge Plugin

Add this to your root `composer.json`:

```json
{
    "extra": {
        "merge-plugin": {
            "include": [
                "modules/*/composer.json"
            ]
        }
    }
}
```

If you already have an `extra` section, just add the `merge-plugin` configuration to it.

#### 3.2: Allow the Plugin

**Required**: Allow the composer merge plugin in your root `composer.json`:

```json
{
    "config": {
        "allow-plugins": {
            "wikimedia/composer-merge-plugin": true
        }
    }
}
```

> **Note**: Only `wikimedia/composer-merge-plugin` is required for Laravel Modular.
> 
> Other plugins like `php-http/discovery` or `pestphp/pest-plugin` are **NOT needed** by this package. Only include them if other packages in your project require them.

### Step 4: Update Composer Autoloader

Run composer dump-autoload to register the merge plugin:

```bash
composer dump-autoload
```

## ✅ Verify Installation

Check that the package is installed correctly:

```bash
php artisan module:list
```

You should see:

```
All Modules:
+------+--------+------+----------+
| Name | Status | Path | Provider |
+------+--------+------+----------+

Total: 0 module(s)
```

If you see this output, the package is successfully installed!

## 🚀 Create Your First Module

Now let's create your first module to verify everything works:

```bash
# Create a module
php artisan module:make Blog

# Update autoloader
composer dump-autoload

# Verify it was created
php artisan module:list
```

You should now see:

```
All Modules:
+------+-----------+------------------+--------------------------------+
| Name | Status    | Path             | Provider                       |
+------+-----------+------------------+--------------------------------+
| Blog | ✓ Enabled | /path/to/modules | Modules\Blog\Providers\Blog... |
+------+-----------+------------------+--------------------------------+

Total: 1 module(s)
```

## 🎯 Next Steps

After installation, you can:

1. **Create modules**: `php artisan module:make ModuleName`
2. **Add controllers**: `php artisan module:controller ModuleName ControllerName`
3. **Add models**: Create in `modules/ModuleName/app/Models/`
4. **Add routes**: Edit `modules/ModuleName/routes/web.php` or `api.php`
5. **Add views**: Create in `modules/ModuleName/resources/views/`

See the [Quick Start Guide](../README.md#quick-start) for more details.

## 🔧 Configuration

### Module Path

Change where modules are stored (default: `base_path('modules')`):

```php
// config/module.php
'path' => base_path('app/Modules'),
```

### Disable Modules

Disable specific modules without deleting them:

```php
// config/module.php
'disabled' => [
    'OldModule',
    'UnusedModule',
],
```

### Cache Configuration

Control module caching:

```php
// config/module.php
'cache' => [
    'enabled' => env('MODULE_CACHE_ENABLED', true),
    'key' => 'laravel_modular_cache',
    'lifetime' => 86400, // 24 hours
],
```

You can also set in `.env`:

```env
MODULE_CACHE_ENABLED=true
```

### Custom Stubs

Use custom stub templates for generated files:

```php
// config/module.php
'stubs_path' => resource_path('stubs/modules'),
```

Then create your custom stubs in that directory.

## 🐛 Troubleshooting

### Module Not Found After Creation

**Problem**: Created a module but it's not appearing in `module:list`

**Solution**:
```bash
# Clear all caches
php artisan optimize:clear

# Regenerate autoloader
composer dump-autoload

# Clear module cache
php artisan cache:clear
```

### Class Not Found Errors

**Problem**: `Class 'Modules\YourModule\...' not found`

**Solution**:

1. Verify composer merge plugin is configured in root `composer.json`
2. Check the module has a `composer.json` file
3. Run `composer dump-autoload`
4. Verify PSR-4 namespace in module's `composer.json`:

```json
{
    "autoload": {
        "psr-4": {
            "Modules\\YourModule\\": "app/"
        }
    }
}
```

### Routes Not Working

**Problem**: Module routes return 404

**Solution**:

1. Clear route cache:
   ```bash
   php artisan route:clear
   ```

2. Verify routes are defined in `modules/YourModule/routes/web.php`

3. Check service provider is loading routes:
   ```bash
   php artisan route:list | grep YourModule
   ```

### Service Provider Not Loading

**Problem**: Module service provider not registering

**Solution**:

1. Verify module is enabled in `module:list`
2. Check `composer.json` has the provider in `extra.laravel.providers`:

```json
{
    "extra": {
        "laravel": {
            "providers": [
                "Modules\\YourModule\\Providers\\YourModuleServiceProvider"
            ]
        }
    }
}
```

3. Run `composer dump-autoload`
4. Clear config cache: `php artisan config:clear`

### Permission Errors on Windows

**Problem**: Cannot create module directories

**Solution**:

1. Run terminal as Administrator
2. Check `modules/` directory permissions
3. Ensure `storage/` and `bootstrap/cache/` are writable

### Composer Merge Plugin Not Working

**Problem**: Modules not auto-loading

**Solution**:

1. Verify `wikimedia/composer-merge-plugin` is installed:
   ```bash
   composer show wikimedia/composer-merge-plugin
   ```

2. Check root `composer.json` has merge-plugin config in `extra`

3. Ensure plugin is allowed in `composer.json`:
   ```json
   {
       "config": {
           "allow-plugins": {
               "wikimedia/composer-merge-plugin": true
           }
       }
   }
   ```

4. Run `composer update wikimedia/composer-merge-plugin`

## 🔄 Upgrading

### From Local Installation to Composer Package

If you previously installed from local path, switch to Packagist:

1. Remove local repository from `composer.json`:
   ```json
   // Remove this:
   "repositories": [
       {
           "type": "path",
           "url": "./packages/Modular"
       }
   ]
   ```

2. Update the package:
   ```bash
   composer require your-vendor/laravel-modular
   ```

### Updating to Latest Version

Update to the latest version:

```bash
composer update your-vendor/laravel-modular
```

Check the [CHANGELOG](../CHANGELOG.md) for breaking changes.

## 📊 Performance Optimization

### Production Setup

For production environments:

1. **Enable caching**:
   ```env
   MODULE_CACHE_ENABLED=true
   APP_DEBUG=false
   ```

2. **Optimize composer autoloader**:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. **Cache Laravel configuration**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Cache module discovery** (optional):
   ```bash
   php artisan module:cache
   ```

### Development Setup

For development:

1. **Disable caching for faster changes**:
   ```env
   MODULE_CACHE_ENABLED=false
   APP_DEBUG=true
   ```

2. **Clear caches frequently**:
   ```bash
   php artisan optimize:clear
   ```

## 🧪 Testing Installation

Create a test script to verify everything works:

```php
<?php
// test-modules.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test 1: Check if ModuleManager is available
echo "Test 1: ModuleManager... ";
$manager = app(\Laravel\Modular\ModuleManager::class);
echo "✓ OK\n";

// Test 2: Discover modules
echo "Test 2: Module discovery... ";
$modules = $manager->discover();
echo "✓ Found " . count($modules) . " module(s)\n";

// Test 3: Check enabled modules
echo "Test 3: Enabled modules... ";
$enabled = $manager->enabled();
echo "✓ " . count($enabled) . " enabled\n";

echo "\nAll tests passed! ✓\n";
```

Run it:
```bash
php test-modules.php
```

## 📚 Additional Resources

- [README](../README.md) - Package overview
- [Quick Start](../README.md#quick-start) - Get started quickly
- [Commands Reference](COMMANDS.md) - All available commands
- [Module Structure](MODULE_STRUCTURE.md) - Understanding module anatomy
- [Debug Logging](DEBUG_LOGGING.md) - Logging configuration
- [Performance](OPTIMIZATION_SUMMARY.md) - Performance tips

## ❓ Getting Help

If you encounter issues:

1. Check this troubleshooting section
2. Review the [README](../README.md)
3. Search [GitHub Issues](https://github.com/your-username/laravel-modular/issues)
4. Create a new issue with:
   - Laravel version
   - PHP version
   - Package version
   - Error messages
   - Steps to reproduce

## ✅ Installation Checklist

Use this checklist to ensure proper installation:

```
[ ] PHP 8.2+ installed
[ ] Laravel 11 or 12 project created
[ ] Package installed via composer
[ ] Composer merge plugin configured in composer.json
[ ] Config published (optional)
[ ] Autoloader dumped
[ ] module:list command works
[ ] First module created successfully
[ ] Module appears in module:list
[ ] Module routes working
[ ] Module views accessible
```

---

**Installation complete!** 🎉

You're now ready to start building modular Laravel applications!

Next: [Create your first module](../README.md#quick-start)
