# MU-Plugin Architecture

This document outlines the architecture, organization, and best practices for working with Must-Use (MU) plugins in a WP Scaffold project.

## Table of Contents
- [Introduction](#introduction)
- [Directory Structure](#directory-structure)
- [Core Framework](#core-framework)
- [Auto-Loading System](#auto-loading-system)
- [Service Container](#service-container)
- [Plugin Organization](#plugin-organization)
- [Dependency Management](#dependency-management)
- [Configuration](#configuration)
- [Best Practices](#best-practices)
- [Common Patterns](#common-patterns)
- [Troubleshooting](#troubleshooting)

## Introduction

Must-Use (MU) plugins are a special type of WordPress plugin that are automatically activated and cannot be deactivated through the WordPress admin interface. The WP Scaffold project uses MU plugins as the foundation for its core functionality, ensuring that critical features are always available and cannot be accidentally disabled.

### Why MU Plugins?

MU plugins offer several advantages for enterprise WordPress development:

1. **Reliability**: Critical functionality is always active and cannot be disabled by users
2. **Performance**: MU plugins load earlier in the WordPress bootstrap process
3. **Organization**: Allows for modular architecture while keeping related code together
4. **Dependency Management**: Provides a structured way to manage dependencies between components
5. **Separation of Concerns**: Keeps business logic separate from presentation logic

## Directory Structure

The MU plugins in the WP Scaffold follow a structured organization:

```
wp-content/
└── mu-plugins/
    ├── 10up-plugin-scaffold/
    │   ├── src/
    │   │   ├── Core/
    │   │   ├── PostTypes/
    │   │   ├── Taxonomies/
    │   │   ├── Settings/
    │   │   ├── Assets.php
    │   │   ├── PluginCore.php
    │   ├── assets/
    │   │   ├── css/
    │   │   ├── js/
    │   │   └── images/
    │   │   └── svg/
    │   ├── dist/
    │   │   ├── css/
    │   │   ├── js/
    │   │   └── images/
    │   ├── languages/
    │   ├── vendor/
    │   ├── node_modules/
    │   ├── plugin.php
    │   ├── composer.json
    │   └── README.md
    ├── other-mu-plugin/
    │   └── ...
    └── 10up-plugin-loader.php
```

### Key Components

- **10up-plugin-loader.php**: The main loader file that includes all MU plugins
- **10up-plugin-scaffold/**: The core framework MU plugin
- **includes/src/**: PHP classes organized by functionality
- **assets/**: Frontend and admin assets (CSS, JS, images)
- **vendor/**: Composer dependencies

## Core Framework

The WP Scaffold is built on the 10up Plugin Scaffold, which provides a solid foundation for enterprise WordPress development. The core framework includes:

## Auto-Loading System

The WP Scaffold uses Composer's PSR-4 autoloading to automatically load PHP classes. This eliminates the need for manual `require` or `include` statements.

### Autoloader Configuration

The autoloader is configured in `composer.json`:

```json
{
  "autoload": {
    "psr-4": {
      "TenUpScaffold\\": "includes/classes/"
    },
    "files": [
      "includes/functions/core.php"
    ]
  }
}
```

### Class Naming Conventions

Classes follow PSR-4 naming conventions, where the namespace and directory structure match:

```php
// File: includes/classes/PostTypes/Event.php
namespace TenUpScaffold\PostTypes;

class Event {
    // Class implementation
}
```

## Plugin Organization

Each MU plugin in the WP Scaffold follows a modular organization pattern:

### Core Components

- **Post Types**: Custom post type definitions and functionality
- **Taxonomies**: Custom taxonomy definitions and functionality
- **Settings**: Admin settings pages
- **Core**: Common functionality
  - **Emoji**: Disables WordPress core emoji functionality for improved performance
- **Assets**: Asset management for scripts and styles

## Dependency Management

The WP Scaffold uses Composer for PHP dependency management:

### External Dependencies

External dependencies are managed through Composer and installed in the `vendor` directory:

```json
{
  "require": {
    "php": ">=8.2",
    "10up/wp-framework": "~1.2.0"
  },
  "require-dev": {
    "phpunit/phpunit": "^9.0",
    "10up/wp_mock": "^0.4.2"
  }
}
```

## Best Practices

When working with MU plugins in the WP Scaffold, follow these best practices:

### Code Organization

1. **Single Responsibility Principle**: Each class should have a single responsibility
2. **Namespacing**: Use namespaces to organize code and avoid conflicts
5. **Hooks and Filters**: Use hooks and filters for extensibility

### Performance

1. **Asset Management**: Enqueue assets only on pages where they're needed
2. **Caching**: Use object caching for expensive operations
3. **Database Queries**: Optimize database queries and use proper indexing

### Security

1. **Input Validation**: Validate and sanitize all user input
2. **Output Escaping**: Escape output to prevent XSS attacks
3. **Capability Checks**: Check user capabilities before performing actions
4. **Nonce Verification**: Use nonces to prevent CSRF attacks

## Common Patterns

The WP Scaffold uses several common patterns in its MU plugins:

### Registration Pattern

Components register themselves with WordPress at the appropriate time:

```php
// Registration pattern example
namespace TenUpScaffold\Blocks;

class TeamMember {
    /**
     * Register the block
     */
    public function register() {
        register_block_type( TENUP_SCAFFOLD_PATH . 'includes/blocks/team-member', [
            'render_callback' => [ $this, 'render' ],
        ]);
    }

    /**
     * Render the block
     */
    public function render( $attributes, $content ) {
        // Render logic
    }
}
```

### Factory Pattern

Factories create instances of complex objects:

```php
// Factory pattern example
namespace TenUpScaffold\Core;

class BlockFactory {
    /**
     * Create a block instance
     */
    public function create( $block_name ) {
        $class_name = 'TenUpScaffold\\Blocks\\' . str_replace( ' ', '', ucwords( str_replace( '-', ' ', $block_name ) ) );

        if ( class_exists( $class_name ) ) {
            return new $class_name();
        }

        return null;
    }
}
```

## Troubleshooting

Common issues when working with MU plugins in the WP Scaffold:

### Plugin Not Loading

If an MU plugin isn't loading:

1. Check that the plugin's main file is directly in the `mu-plugins` directory or included by `10up-plugin-loader.php`
2. Verify that the plugin doesn't have syntax errors
3. Check for dependency conflicts

### Autoloader Issues

If classes aren't being autoloaded:

1. Verify that the namespace matches the directory structure
2. Check that Composer's autoloader is being loaded
3. Run `composer dump-autoload` to regenerate the autoloader

### Dependency Conflicts

If there are conflicts between dependencies:

1. Check for version conflicts in `composer.json`
2. Use namespaced dependencies to avoid conflicts
3. Consider using dependency injection to resolve conflicts
