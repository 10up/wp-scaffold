# Theme Architecture

This document outlines the architecture, organization, and features of the WordPress themes included in the WP Scaffold project.

## Table of Contents
- [Introduction](#introduction)
- [Theme Types](#theme-types)
- [Directory Structure](#directory-structure)
- [Core Components](#core-components)
- [Asset Management](#asset-management)
- [Block Integration](#block-integration)
- [Theme Customization](#theme-customization)
- [Development Workflow](#development-workflow)
- [Best Practices](#best-practices)

## Introduction

The WP Scaffold includes two theme options:

1. **10up-theme**: A traditional WordPress theme with modern development practices
2. **10up-block-theme**: A block-based theme utilizing WordPress Full Site Editing (FSE) features

Both themes follow modern WordPress development practices, including object-oriented programming, modular architecture, and integration with the WP Framework.

## Theme Types

### Traditional Theme (10up-theme)

The traditional theme (`10up-theme`) combines classic WordPress theme structure with modern development practices:

- Uses traditional template files (header.php, footer.php, index.php)
- Implements object-oriented PHP for theme functionality
- Integrates with the block editor while maintaining traditional theme structure
- Uses a modular approach with clear separation of concerns

### Block Theme (10up-block-theme)

The block theme (`10up-block-theme`) is built for WordPress Full Site Editing:

- Uses block templates and template parts instead of traditional PHP templates
- Includes templates directory for block templates
- Includes parts directory for reusable template parts
- Designed for full block-based site editing

## Directory Structure

### Traditional Theme Structure

```
10up-theme/
├── assets/             # Source assets (images, fonts, etc.)
├── blocks/             # Custom block definitions
│   └── example-block/  # Example block implementation
├── dist/               # Compiled assets (CSS, JS)
├── languages/          # Translation files
├── partials/           # Template partials
├── patterns/           # Block patterns
├── src/                # PHP source files
│   ├── Assets.php      # Asset management
│   ├── Blocks.php      # Block registration and handling
│   └── ThemeCore.php   # Core theme functionality
├── vendor/             # Composer dependencies
├── functions.php       # Theme functions and initialization
├── header.php          # Header template
├── footer.php          # Footer template
├── index.php           # Main template file
├── style.css           # Theme metadata
├── template-tags.php   # Template helper functions
└── theme.json          # Block editor theme settings
```

### Block Theme Structure

```
10up-block-theme/
├── assets/             # Source assets (images, fonts, etc.)
├── blocks/             # Custom block definitions
├── dist/               # Compiled assets (CSS, JS)
├── parts/              # Template parts for block theme
├── patterns/           # Block patterns
├── src/                # PHP source files
├── styles/             # Block theme styles
├── templates/          # Block templates
├── vendor/             # Composer dependencies
├── functions.php       # Theme functions and initialization
├── style.css           # Theme metadata
└── theme.json          # Block theme settings
```

## Core Components

### ThemeCore Class

The `ThemeCore` class serves as the main entry point for the theme, handling initialization and setup:

- Registers theme features and support
- Sets up internationalization
- Initializes theme components using the WP Framework's module system
- Provides hooks for extending theme functionality

```php
class ThemeCore {
    public function setup() {
        // Initialize theme
    }

    public function theme_setup() {
        // Register theme features
    }

    public function init() {
        // Initialize modules
    }
}
```

### Assets Class

The `Assets` class manages theme assets (CSS, JavaScript):

- Enqueues frontend and editor scripts and styles
- Uses the `GetAssetInfo` trait from WP Framework for simplified asset handling
- Manages editor-specific styles for both the editor frame and canvas
- Registers and manages SVG icons
- Handles JavaScript detection
- Integrates with the WP Framework's asset management utilities

### Blocks Class

The `Blocks` class handles block editor integration:

- Automatically registers custom blocks from the blocks directory
- Enqueues block-specific styles using `wp_enqueue_block_style`
- Automatically detects and enqueues block-specific CSS files from the `blocks/autoenqueue` directory
- Registers block pattern categories
- Provides server-side rendering for dynamic blocks using markup.php files
- Integrates with the WP Framework's asset management utilities via the `GetAssetInfo` trait

## Asset Management

The theme uses a modern asset management approach:

- Source files are stored in the `src` directory
- Compiled assets are stored in the `dist` directory
- Assets are enqueued with proper dependencies and versioning
- Development mode includes features like hot reloading

## Block Integration

### Custom Blocks

The theme includes a framework for creating custom blocks:

- Blocks are stored in the `blocks` directory
- Each block follows a standard structure:
  - `block.json`: Block metadata and configuration
  - `edit.js`: Block editor component
  - `index.js`: Block registration
  - `markup.php`: Server-side rendering (for dynamic blocks)
  - `save.js`: Block save function

### Block Editor Integration

The theme integrates with the block editor through:

- Theme settings in `theme.json`
- Editor-specific styles
- Custom block patterns
- Block variations and styles

## Theme Customization

### Theme Settings

The theme uses `theme.json` to define theme settings:

- Content width settings
- Color settings
- Typography settings
- Block-specific settings

### Hooks and Filters

The theme provides several hooks for customization:

- `tenup_theme_loaded`: Fired after theme is loaded
- `tenup_theme_init`: Fired after theme initialization
- `tenup_theme_before_init`: Fired before theme initialization
- `tenup_theme_init_priority`: Filter to modify initialization priority

## Development Workflow

The theme supports a modern development workflow:

- Composer for PHP dependencies
- npm for JavaScript dependencies
- Build process for compiling assets
- Local development environment detection
- Fast refresh for local development

## Best Practices

The theme follows several best practices:

- **Separation of Concerns**: Clear separation between different components
- **Object-Oriented Programming**: Modular, maintainable code structure
- **WP Framework Integration**: Leveraging the framework for common functionality
- **Pure Functions**: Template tags are kept as pure functions
- **Proper Escaping**: Security best practices for output escaping
- **Internationalization**: Full support for translations
- **Accessibility**: Following WordPress accessibility guidelines
