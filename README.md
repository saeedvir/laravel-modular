# Laravel Modular

[![Latest Version on Packagist](https://img.shields.io/packagist/v/saeedvir/laravel-modular.svg?style=flat-square)](https://packagist.org/packages/saeedvir/laravel-modular)
[![Total Downloads](https://img.shields.io/packagist/dt/saeedvir/laravel-modular.svg?style=flat-square)](https://packagist.org/packages/saeedvir/laravel-modular)
[![License](https://img.shields.io/packagist/l/saeedvir/laravel-modular.svg?style=flat-square)](https://packagist.org/packages/saeedvir/laravel-modular)

A powerful modular architecture package for Laravel applications that allows you to organize your codebase into independent, reusable modules with **automatic discovery** and **zero configuration**.

## ✨ Features

- 🚀 **Auto-Discovery** - Modules automatically discovered and registered via composer merge plugin
- 📦 **Zero Configuration** - Just run `composer dump-autoload` after creating modules
- ⚡ **Performance Optimized** - Built-in caching and lazy loading for production use
- 🎨 **Complete Module Structure** - Controllers, models, views, routes, migrations, translations
- 🔧 **Artisan Commands** - Comprehensive CLI tools for module management
- 📊 **Performance Monitoring** - Track module discovery and operation performance
- 🐛 **Debug-Aware Logging** - Respects `APP_DEBUG` for production-friendly logs
- 🧪 **Testing Support** - Built-in infrastructure for module testing
- 🎯 **Laravel 11 & 12** - Full support for modern Laravel versions

## 📋 Requirements

- PHP ^8.2
- Laravel ^11.0 or ^12.0
- Composer

## 📦 Installation

Install the package via composer:

```bash
composer require saeedvir/laravel-modular
```

The package will automatically register itself via Laravel's package auto-discovery.

**Configure composer merge plugin** in your root `composer.json`:

```json
{
    "extra": {
        "merge-plugin": {
            "include": ["modules/*/composer.json"]
        }
    },
    "config": {
        "allow-plugins": {
            "wikimedia/composer-merge-plugin": true
        }
    }
}
```

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=module-config
```

**📖 For detailed installation instructions, troubleshooting, and setup guide, see:**  
**[Installation Guide](docs/INSTALLATION.md)**

## 🚀 Quick Start

### Create Your First Module

```bash
php artisan module:make Blog
composer dump-autoload
```

This creates a complete module structure:

```
modules/Blog/
├── app/
│   ├── Console/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   ├── Providers/
│   │   └── BlogServiceProvider.php
│   └── Services/
├── config/
│   └── config.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/
│   └── lang/
├── routes/
│   ├── web.php
│   └── api.php
└── composer.json
```

### List All Modules

```bash
php artisan module:list
```

### Remove a Module

```bash
php artisan module:remove Blog
```

## 📖 Usage

### Creating Controllers

```bash
php artisan module:controller Blog PostController
```

### Creating Models

Create models directly in your module:

```php
<?php

namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'content', 'slug'];
}
```

### Adding Routes

In `modules/Blog/routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\PostController;

Route::prefix('blog')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('blog.index');
    Route::resource('posts', PostController::class);
});
```

### Using Views

Create views in `modules/Blog/resources/views/` and load them:

```php
return view('blog::posts.index');
```

### Migrations

Create migrations in your module:

```bash
php artisan module:migration Blog create_posts_table
```

Run migrations:

```bash
php artisan migrate
```

## 🎯 Advanced Features

### Module Configuration

Each module can have its own configuration file in `config/config.php`:

```php
return [
    'name' => 'Blog',
    'enabled' => true,
    'version' => '1.0.0',
    // Your custom config...
];
```

Access module config:

```php
config('blog.version');
```

### Disabling Modules

In `config/module.php`:

```php
'disabled' => [
    'OldModule',
],
```

### Performance Optimization

The package includes built-in caching:

```bash
# Cache module discovery
php artisan module:cache
```

### Debug Mode

When `APP_DEBUG=true`, the package logs:
- Module discovery events
- Cache operations  
- Module creation/deletion
- Performance metrics

When `APP_DEBUG=false` (production), only errors are logged.

## 🛠️ Available Commands

| Command | Description |
|---------|-------------|
| `module:make` | Create a new module |
| `module:list` | List all modules |
| `module:remove` | Remove a module |
| `module:controller` | Create a controller in a module |
| `module:request` | Create a form request in a module |
| `module:resource` | Create an API resource in a module |
| `module:migration` | Create a migration in a module |
| `module:factory` | Create a model factory in a module |
| `module:seeder` | Create a database seeder in a module |
| `module:test` | Run tests for a specific module |
| `module:cache` | Cache module configuration |

## ⚙️ Configuration

The `config/module.php` file provides extensive configuration options:

```php
return [
    // Module storage path
    'path' => base_path('modules'),
    
    // Disabled modules
    'disabled' => [],
    
    // Caching
    'cache' => [
        'enabled' => env('MODULE_CACHE_ENABLED', true),
        'key' => 'laravel_modular_cache',
        'lifetime' => 86400, // 24 hours
    ],
    
    // Auto-register routes
    'auto_register_routes' => true,
    
    // Custom stubs path
    'stubs_path' => null,
];
```

## 📚 Documentation

- **[Installation Guide](docs/INSTALLATION.md)** - Complete installation and setup
- **[Debug Logging](docs/DEBUG_LOGGING.md)** - Debug mode and logging configuration
- **[Performance Optimization](docs/OPTIMIZATION_SUMMARY.md)** - Performance tips and caching
- **[Enhancement Summary](docs/ENHANCEMENT_SUMMARY.md)** - Feature enhancements and improvements

## 🧪 Testing

```bash
composer test
```

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details on how to contribute to this project.

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 🔒 Security

If you discover any security-related issues, please email saeed.es91@gmail.com instead of using the issue tracker.

## 👥 Credits

- [Your Name](https://github.com/saeedvir)
- [All Contributors](../../contributors)

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## 💡 Examples

### Blog Module Example

```php
// Controller
namespace Modules\Blog\Http\Controllers;

use Modules\Blog\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);
        return view('blog::posts.index', compact('posts'));
    }
}

// Model
namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'content', 'slug', 'published_at'];
    protected $casts = ['published_at' => 'datetime'];
}

// Route
Route::prefix('blog')->group(function () {
    Route::resource('posts', PostController::class);
});
```

## 🌟 Why Laravel Modular?

- **Scalability**: Organize large applications into manageable modules
- **Reusability**: Share modules across projects
- **Maintainability**: Clear separation of concerns
- **Team Collaboration**: Teams can work on different modules independently
- **Performance**: Optimized for production with caching and lazy loading
- **Modern**: Built for Laravel 11 & 12 with PHP 8.2+

## 📊 Performance

- **Module Discovery**: ~15-20ms (cached)
- **Autoloading**: Native composer PSR-4 (optimal performance)
- **Memory Usage**: Minimal overhead with lazy loading
- **Production Ready**: Designed for high-traffic applications

---

**Built with ❤️ for the Laravel community**

If you find this package helpful, please consider giving it a ⭐ on GitHub!
