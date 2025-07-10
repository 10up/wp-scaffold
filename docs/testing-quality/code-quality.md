# Code Quality

This document outlines the code quality tools and practices used in a WP Scaffold project to ensure consistent, maintainable, and high-quality code.

## Table of Contents
- [PHP Code Quality](#php-code-quality)
- [JavaScript and CSS Code Quality](#javascript-and-css-code-quality)
- [Pre-commit Hooks](#pre-commit-hooks)

## PHP Code Quality

### PHP CodeSniffer (PHPCS)

The WP Scaffold uses PHP CodeSniffer to enforce PHP coding standards. The configuration is defined in `phpcs.xml`:

```xml
<?xml version="1.0"?>
<ruleset name="10up PHPCS">
	<description>10up PHPCS extended.</description>

	<!-- Scan these directories -->
	<file>themes</file>
	<file>mu-plugins</file>

	<!-- Don't scan these directories -->
	<exclude-pattern>node_modules/</exclude-pattern>
	<exclude-pattern>vendor/</exclude-pattern>
	<exclude-pattern>dist/</exclude-pattern>

	<!-- Exclude the specific plugins we don't want to scan -->
	<exclude-pattern>./plugins/debug-bar/</exclude-pattern>
	<exclude-pattern>./plugins/debug-bar-slow-actions/</exclude-pattern>
	<exclude-pattern>./plugins/query-monitor/</exclude-pattern>

	<!-- Use the 10up rulset -->
	<rule ref="10up-Default" />

	<!-- Ignore filecomment for the plugin loader -->
	<rule ref="Squiz.Commenting.FileComment.Missing">
		<exclude-pattern>./mu-plugins/10up-plugin-loader.php</exclude-pattern>
	</rule>

	<arg value="sp"/> <!-- Show sniff and progress -->
	<arg name="colors"/> <!-- Show results with colors. Disable if working on Windows -->
	<arg name="basepath" value="."/> <!-- Strip the file paths down to the relevant bit -->
	<arg name="extensions" value="php"/> <!-- Limit to PHP -->
</ruleset>
```

The configuration uses the "10up-Default" ruleset, which is a custom ruleset based on WordPress coding standards. It scans the themes and mu-plugins directories, excluding node_modules, vendor, and dist directories, as well as specific plugins.

### PHPStan

The WP Scaffold uses PHPStan for static analysis of PHP code. See the [PHPStan documentation](./phpstan.md) for more information.

## JavaScript and CSS Code Quality

### ESLint

The WP Scaffold uses ESLint to enforce JavaScript coding standards. The configuration is defined in `.eslintrc`:

```json
{
    "extends": ["@10up/eslint-config/wordpress"]
}
```

The configuration extends the "@10up/eslint-config/wordpress" configuration, which is a custom ESLint configuration for WordPress projects maintained by 10up.

### Stylelint

The WP Scaffold uses Stylelint to enforce CSS coding standards. The configuration is not explicitly defined in a configuration file, but it's likely using the default configuration provided by the 10up-toolkit.

## Pre-commit Hooks

The WP Scaffold uses lint-staged and Husky to run linters on staged files before they are committed. The configuration is defined in `.lintstagedrc.json`:

```json
{
  "*.css": ["10up-toolkit lint-style"],
  "*.js": ["10up-toolkit lint-js"],
  "*.jsx": ["10up-toolkit lint-js"],
  "*.php": ["./vendor/bin/phpcs"]
}
```

This configuration runs the following linters on staged files:
- For CSS files: 10up-toolkit lint-style
- For JS and JSX files: 10up-toolkit lint-js
- For PHP files: PHP CodeSniffer
