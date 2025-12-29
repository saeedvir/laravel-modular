# Changelog

All notable changes to `laravel-modular` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.0] - 2025-12-29

### Added
- **Automatic Persistence**: Module enable/disable states are now automatically persisted in `modules/modules.json`, removing the need for manual configuration edits.
- **Enhanced Module Manager**: Improved `ModuleManager` with direct `enable()` and `disable()` methods that handle state persistence and cache clearing.

### Changed
- **Tech Debt Cleanup**: Conducted a codebase-wide refactoring to qualify all global helper functions and native PHP functions (e.g., `\count()`, `\config()`, `\is_array()`) with leading backslashes for improved performance and static analysis compatibility.
- **Import Optimization**: Standardized and optimized facade imports across all console commands and services.

### Fixed
- Improved logic in `ModuleTestCommand` for handling failed modules.

## [1.1.0] - 2025-12-29

### Added
- New artisan commands for enhanced module management:
  - `php artisan module:optimize` - Pre-calculate and cache module discovery for production
  - `php artisan module:status` - Show a comprehensive table of all modules and their status
  - `php artisan module:enable` / `module:disable` - CLI helpers for managing module states
  - `php artisan module:make-model` - Generate models with optional migrations, factories, and seeders
  - `php artisan module:make-test` - Generate Feature or Unit tests within modules
- New `test.stub` for standardized module testing infrastructure

### Changed
- **Performance**: Moved module service provider registration from `boot()` to `register()` phase to ensure services are available earlier in the application lifecycle
- **Refactoring**: Consolidated all module file generation within `ModuleManager` to exclusively use the `ModuleStubService`, improving consistency and maintainability
- **Stability**: Removed redundant and risky automatic modification of root `composer.json` from `module:make` and `module:remove` commands, favoring the `composer-merge-plugin` workflow
- **Validation**: Enhanced module name validation to prevent use of reserved words and invalid characters

### Fixed
- Improved error handling during module creation with better logging and cleanup of partial files

## [1.0.0] - 2025-11-05

### Added
- Initial release of Laravel Modular Package
- Module auto-discovery with composer merge plugin
- Comprehensive artisan commands for module management
  - `php artisan module:make` - Create new modules
  - `php artisan module:list` - List all modules
  - `php artisan module:remove` - Remove modules
  - `php artisan module:controller` - Create module controllers
  - `php artisan module:request` - Create module requests
  - `php artisan module:resource` - Create module resources
  - `php artisan module:migration` - Create module migrations
  - `php artisan module:factory` - Create module factories
  - `php artisan module:seeder` - Create module seeders
  - `php artisan module:test` - Run module tests
  - `php artisan module:cache` - Cache module configuration
- ModuleManager for programmatic module operations
- Service provider auto-registration via Laravel package discovery
- Complete module structure generator with stubs
- Module caching for improved performance
- Performance monitoring service
- Debug-aware logging (respects `APP_DEBUG` configuration)
- Configurable module system via `config/module.php`
- Support for disabled modules
- PSR-4 autoloading for all modules
- Module-specific routes, views, migrations, and translations
- Publishable assets (config, views, migrations, lang)

### Features
- 🚀 **Auto-Discovery** - Modules automatically discovered and registered
- 📦 **Composer Integration** - Uses composer-merge-plugin for seamless autoloading
- ⚡ **Performance Optimized** - Caching and lazy loading for large-scale projects
- 🎨 **Customizable Stubs** - Override default templates for generated files
- 🔧 **Flexible Configuration** - Extensive configuration options
- 📊 **Performance Metrics** - Built-in performance monitoring
- 🐛 **Debug Logging** - Environment-aware logging system
- 🧪 **Testing Support** - Built-in test infrastructure
- 📚 **Comprehensive Documentation** - Extensive guides and examples

### Technical Details
- PHP 8.2+ required
- Laravel 11.0+ and 12.0+ supported
- Uses wikimedia/composer-merge-plugin for autoloading
- Optimized for production use with minimal overhead
- Follows PSR-12 coding standards
- Implements Laravel best practices

[Unreleased]: https://github.com/your-username/laravel-modular/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/your-username/laravel-modular/releases/tag/v1.0.0
