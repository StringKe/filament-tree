# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Purpose

This is a Filament PHP package focused on implementing deep tree nesting functionality and TreeSelect support for tables. This is a pure functionality extension package - it does NOT require database migrations, commands, or configuration files.

## Key Architecture

### Package Structure
- **PHP Package**: Laravel/Filament package using PSR-4 autoloading
- **Namespace**: `StringKe\FilamentTree`
- **Service Provider**: `FilamentTreeServiceProvider` handles package registration
- **Frontend Build**: Uses esbuild for JavaScript bundling

### Core Dependencies
- PHP ^8.2
- Filament ^4.0
- Laravel Package Tools

## Development Commands

### JavaScript/Asset Building
```bash
npm run dev    # Watch mode with source maps
npm run build  # Production build, minified
```

### PHP Development
```bash
composer lint         # Run Laravel Pint for code formatting
composer test:lint    # Check code formatting without changes
composer analyse      # Run PHPStan static analysis
composer test         # Run Pest tests
composer refactor     # Run Rector for code refactoring
composer test:refactor # Check refactoring without changes
```

## Important Notes

- **No Database Migrations**: This package is purely for UI/functionality extension
- **No Configuration Files**: Avoid creating config files unless absolutely necessary
- **No Commands**: Package should not include artisan commands
- **Focus**: All development should center around tree structures, deep nesting, and TreeSelect components
- **Assets**: JavaScript entry point is `resources/js/index.js`, compiled to `resources/dist/filament-tree.js`
- **Testing**: Uses Pest PHP testing framework

## File Locations

- **PHP Source**: `src/`
- **JavaScript**: `resources/js/`
- **CSS**: `resources/css/`
- **Translations**: `resources/lang/`
- **Tests**: `tests/`
- **Build Scripts**: `bin/build.js`