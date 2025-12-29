# Contributing to Laravel Modular

First off, thank you for considering contributing to Laravel Modular! It's people like you that make this package better for everyone.

## Code of Conduct

This project and everyone participating in it is governed by our commitment to creating a welcoming and inclusive environment. By participating, you are expected to uphold this standard.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the existing issues to avoid duplicates. When you create a bug report, include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples** to demonstrate the steps
- **Describe the behavior you observed** and what you expected to see
- **Include Laravel version, PHP version, and package version**
- **Include error messages and stack traces** if applicable

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, include:

- **Use a clear and descriptive title**
- **Provide a detailed description** of the suggested enhancement
- **Explain why this enhancement would be useful** to most users
- **List any similar features** in other packages if applicable

### Pull Requests

1. **Fork the repository** and create your branch from `main`
2. **Follow PSR-12 coding standards**
3. **Write clear, descriptive commit messages**
4. **Include tests** for new features
5. **Update documentation** as needed
6. **Ensure all tests pass** before submitting

#### Pull Request Process

1. Update the README.md with details of changes if applicable
2. Update the CHANGELOG.md following Keep a Changelog format
3. The PR will be merged once you have the sign-off of at least one maintainer

## Development Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- Laravel 11.0 or 12.0

### Setting Up Development Environment

```bash
# Clone your fork
git clone https://github.com/your-username/laravel-modular.git
cd laravel-modular

# Install dependencies
composer install

# Run tests
composer test
```

### Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run specific test file
./vendor/bin/phpunit tests/Unit/ModuleManagerTest.php
```

### Code Style

This project follows PSR-12 coding standards. You can check your code style with:

```bash
# Check code style
composer check-style

# Fix code style automatically
composer fix-style
```

## Development Guidelines

### File Structure

- Place new commands in `src/Console/Commands/`
- Place new services in `src/Services/`
- Place tests in `tests/Unit/` or `tests/Feature/`
- Update stubs in `stubs/` if adding new generators

### Naming Conventions

- **Classes**: StudlyCase (e.g., `ModuleManager`)
- **Methods**: camelCase (e.g., `createModule()`)
- **Variables**: camelCase (e.g., `$modulePath`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `DEFAULT_PATH`)

### Documentation

- Add PHPDoc blocks to all classes and methods
- Include `@param`, `@return`, and `@throws` tags
- Document complex logic with inline comments
- Update README.md for new features

### Testing

- Write unit tests for new functionality
- Aim for high test coverage
- Test both success and failure scenarios
- Use descriptive test method names

Example test structure:
```php
public function test_it_creates_module_with_valid_name(): void
{
    // Arrange
    $moduleName = 'TestModule';
    
    // Act
    $result = $this->moduleManager->create($moduleName);
    
    // Assert
    $this->assertTrue($result);
}
```

### Commit Messages

Follow conventional commits format:

```
type(scope): subject

body

footer
```

Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

Examples:
```
feat(commands): add module:clone command

fix(manager): resolve caching issue on Windows

docs(readme): update installation instructions
```

### Branch Naming

- Feature branches: `feature/short-description`
- Bug fix branches: `fix/short-description`
- Documentation: `docs/short-description`

## Additional Notes

### Issue and Pull Request Labels

- `bug`: Something isn't working
- `enhancement`: New feature or request
- `documentation`: Improvements to documentation
- `good first issue`: Good for newcomers
- `help wanted`: Extra attention is needed
- `question`: Further information is requested

## Questions?

Feel free to open an issue with the `question` label if you have any questions about contributing!

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

**Thank you for contributing to Laravel Modular!** 🎉
