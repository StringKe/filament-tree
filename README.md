# Filament Tree

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stringke/filament-tree.svg?style=flat-square)](https://packagist.org/packages/stringke/filament-tree)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/stringke/filament-tree/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/stringke/filament-tree/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/stringke/filament-tree/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/stringke/filament-tree/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/stringke/filament-tree.svg?style=flat-square)](https://packagist.org/packages/stringke/filament-tree)

A powerful Filament PHP package for implementing deep tree nesting functionality with TreeTable and TreeSelect components. This package provides elegant solutions for handling hierarchical data structures in your Filament applications.

## Features

- 🌳 **Deep Tree Nesting**: Support for unlimited levels of parent-child relationships
- 📊 **Tree Table**: Display hierarchical data in nested table format with expand/collapse functionality
- 🔽 **Tree Select**: Advanced select component with tree structure visualization
- 🎯 **Model Trait**: Simple `HasTree` trait to enable tree functionality on any model
- ⚡ **Performance Optimized**: Efficient queries and lazy loading for large datasets
- 🎨 **Customizable**: Flexible styling and configuration options

## Installation

You can install the package via composer:

```bash
composer require stringke/filament-tree
```

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels, follow the instructions in the [Filament Docs](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) first.

After setting up a custom theme, add the plugin's views to your theme's CSS file:

```css
@source '../../../../vendor/stringke/filament-tree/resources/**/*.blade.php';
```

## Usage

### 1. Add the HasTree Trait to Your Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use StringKe\FilamentTree\Traits\HasTree;

class Category extends Model
{
    use HasTree;
    
    // Optional: customize the parent and children relationship names
    protected string $parentColumn = 'parent_id';
}
```

### 2. Using TreeTable in Filament Resources

```php
use StringKe\FilamentTree\Tables\Columns\TreeColumn;
use StringKe\FilamentTree\Tables\TreeTable;

public static function table(Table $table): Table
{
    return TreeTable::make($table)
        ->columns([
            TreeColumn::make('name')
                ->sortable()
                ->searchable(),
            // Other columns...
        ])
        ->defaultSort('name')
        ->defaultExpanded() // Optional: expand all nodes by default
        ->maxDepth(5); // Optional: limit the display depth
}
```

### 3. Using TreeSelect in Forms

```php
use StringKe\FilamentTree\Forms\Components\TreeSelect;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            TreeSelect::make('parent_id')
                ->relationship('parent', 'name')
                ->label('Parent Category')
                ->placeholder('Select parent category...')
                ->searchable()
                ->nullable()
                ->preload() // Load all options on mount
                ->maxDepth(10), // Optional: limit selection depth
            // Other fields...
        ]);
}
```

### 4. Advanced Model Configuration

The `HasTree` trait provides several useful methods:

```php
// Get all ancestors
$category->ancestors();

// Get all descendants
$category->descendants();

// Get root nodes
Category::roots();

// Get tree depth
$category->getDepth();

// Check if node is root
$category->isRoot();

// Check if node is leaf (no children)
$category->isLeaf();

// Build complete tree
Category::tree();

// Get siblings
$category->siblings();
```

## Model Requirements

Your model's database table should have:
- A `parent_id` column (nullable, foreign key to same table)
- Standard Laravel timestamps (optional but recommended)

Example migration:

```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
    $table->integer('sort')->default(0);
    $table->timestamps();
});
```

## Configuration

While this package works out of the box without configuration files, you can customize behavior through model properties and component options:

### Model Customization

```php
class Category extends Model
{
    use HasTree;
    
    // Customize parent column name
    protected string $parentColumn = 'parent_id';
    
    // Customize children relationship name
    protected string $childrenRelation = 'children';
    
    // Customize parent relationship name  
    protected string $parentRelation = 'parent';
}
```

### Component Customization

Both `TreeTable` and `TreeSelect` components support various customization options through their fluent API.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [StringKE](https://github.com/StringKe)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.