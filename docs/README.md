# Laravel Modular - Documentation

Welcome to the Laravel Modular package documentation!

## 📖 Getting Started

### Installation
- **[Installation Guide](INSTALLATION.md)** - Complete installation instructions, troubleshooting, and setup

### Quick Links
- [Main README](../README.md) - Package overview and quick start
- [Changelog](../CHANGELOG.md) - Version history
- [Contributing](../CONTRIBUTING.md) - How to contribute
- [License](../LICENSE) - MIT License

## 📚 Documentation Pages

### Setup & Configuration

#### [Installation Guide](INSTALLATION.md)
Complete guide covering:
- Requirements and prerequisites
- Installation via Composer
- Configuration options
- First module creation
- Troubleshooting common issues
- Performance optimization
- Installation checklist

#### [Routing Guide](ROUTING.md) ⭐ NEW
Understanding module routing:
- **Automatic route prefixing** - All module routes are prefixed
- Web routes configuration
- API routes configuration
- Custom prefix configuration
- Route naming conventions
- Middleware usage
- Examples and best practices

### Features & Enhancements

#### [Enhancement Summary](ENHANCEMENT_SUMMARY.md)
Overview of package features and capabilities:
- Core functionality
- Module structure
- Available commands
- Service providers
- Auto-discovery system

### Performance & Optimization

#### [Performance Optimization](OPTIMIZATION_SUMMARY.md)
Performance improvements and best practices:
- What was optimized
- Benefits of optimization
- Composer merge plugin integration
- Performance metrics
- Backward compatibility notes

### Development & Debugging

#### [Debug Logging](DEBUG_LOGGING.md)
Debug mode and logging configuration:
- How debug logging works
- Log levels by environment
- Controlled log statements
- Configuration examples
- Performance impact
- Best practices

## 🎯 Quick Reference

### Common Tasks

**Install Package**
```bash
composer require your-vendor/laravel-modular
```

**Create Module**
```bash
php artisan module:make ModuleName
composer dump-autoload
```

**List Modules**
```bash
php artisan module:list
```

**Remove Module**
```bash
php artisan module:remove ModuleName
```

**Clear Caches**
```bash
php artisan optimize:clear
composer dump-autoload
```

### Available Commands

| Command | Description |
|---------|-------------|
| `module:make` | Create a new module |
| `module:list` | List all modules |
| `module:remove` | Remove a module |
| `module:controller` | Create a controller |
| `module:request` | Create a form request |
| `module:resource` | Create an API resource |
| `module:migration` | Create a migration |
| `module:factory` | Create a factory |
| `module:seeder` | Create a seeder |
| `module:test` | Run module tests |
<<<<<<< HEAD
| `module:cache` | Manage module discovery cache |
| `module:optimize` | Optimize module discovery |
| `module:status` | Show status of all modules |
| `module:enable` | Enable a specific module |
| `module:disable` | Disable a specific module |
=======
| `module:cache` | Cache module config |
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c

## 🔧 Configuration

### Basic Config

The main configuration file is `config/module.php`:

```php
return [
    'path' => base_path('modules'),
    'disabled' => [],
    'cache' => [
        'enabled' => env('MODULE_CACHE_ENABLED', true),
        'key' => 'laravel_modular_cache',
        'lifetime' => 86400,
    ],
    'auto_register_routes' => true,
    'stubs_path' => null,
];
```

### Environment Variables

```env
# Enable/disable module caching
MODULE_CACHE_ENABLED=true

# Debug mode affects logging
APP_DEBUG=false
```

## 🏗️ Module Structure

```
modules/YourModule/
├── app/
│   ├── Console/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   ├── Providers/
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

## 🐛 Troubleshooting

### Module Not Found

```bash
composer dump-autoload
php artisan optimize:clear
```

### Class Not Found

1. Check PSR-4 namespace in module's `composer.json`
2. Verify composer merge plugin is configured
3. Run `composer dump-autoload`

### Routes Not Working

```bash
php artisan route:clear
php artisan route:list | grep ModuleName
```

### Permission Errors

- Run terminal as Administrator (Windows)
- Check directory permissions (Linux/Mac)

See [Installation Guide - Troubleshooting](INSTALLATION.md#troubleshooting) for detailed solutions.

## 📊 Performance Tips

1. **Production**: Enable caching
   ```env
   MODULE_CACHE_ENABLED=true
   APP_DEBUG=false
   ```

2. **Development**: Clear caches frequently
   ```bash
   php artisan optimize:clear
   ```

3. **Optimize autoloader**
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

See [Performance Optimization](OPTIMIZATION_SUMMARY.md) for details.

## 🎓 Learning Path

### For Beginners

1. Read [Main README](../README.md)
2. Follow [Installation Guide](INSTALLATION.md)
3. Create your first module (Quick Start in README)
4. Explore module structure
5. Try different commands

### For Advanced Users

1. Review [Enhancement Summary](ENHANCEMENT_SUMMARY.md)
2. Study [Performance Optimization](OPTIMIZATION_SUMMARY.md)
3. Configure [Debug Logging](DEBUG_LOGGING.md)
4. Customize stubs
5. Contribute to the project

## 🔗 External Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Composer Documentation](https://getcomposer.org/doc/)
- [PSR-4 Autoloading](https://www.php-fig.org/psr/psr-4/)
- [Semantic Versioning](https://semver.org/)

## ❓ Getting Help

1. **Check Documentation** - Read relevant guides
2. **Search Issues** - Check existing GitHub issues
3. **Ask Questions** - Create a new issue with details
4. **Contribute** - Submit PRs for improvements

## 📝 Documentation Updates

This documentation is continuously updated. Check the repository for the latest version.

<<<<<<< HEAD
**Last Updated**: December 2025  
**Package Version**: 1.2.0
=======
**Last Updated**: November 2025  
**Package Version**: 1.0.0
>>>>>>> 1e28343963064afec1036f03d9c7bfca61878a0c

---

**Need help?** Create an issue on [GitHub](https://github.com/your-username/laravel-modular/issues)

**Want to contribute?** See [Contributing Guide](../CONTRIBUTING.md)
