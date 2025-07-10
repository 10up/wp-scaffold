# Troubleshooting Guide

This guide provides solutions to common issues you may encounter when working with a WP Scaffold project, focusing on files and directories within `wp-content`.

## Table of Contents
- [Installation Issues](#installation-issues)
- [Dependency Problems](#dependency-problems)
- [Build Failures](#build-failures)
- [Code Quality Issues](#code-quality-issues)
- [Debugging Tips](#debugging-tips)
- [Common Error Messages](#common-error-messages)
- [Where to Get Help](#where-to-get-help)

## Installation Issues
- Ensure all prerequisites are installed (see [Installation Guide](../installation.md)):
	- Local WP (https://localwp.com/)
	- Git
	- Composer (for PHP dependencies)
	- Node.js >= 20.0.0 (as specified in package.json)
	- npm >= 9.0.0 (as specified in package.json)
	- PHP >= 8.3 (as specified in composer.json)
- Check that your Local WP site is correctly mapped to your project's `wp-content` directory.
- Verify file permissions for the `wp-content` directory and its subfolders.
- Ensure your local environment is properly detected. The plugin checks for:
	- Environment type: 'local' or 'development'
	- URLs containing '.test' or '.local'

## Dependency Problems
- Run `composer install` and `npm install` in the `wp-content` directory to ensure all dependencies are present.
- If issues persist, delete `vendor/` and `node_modules/` from `wp-content` and reinstall.
- If you encounter the error "Vendor autoload file not found", run `composer install` in the `wp-content` directory.
- Remember that the project uses npm workspaces for themes and the mu-plugin, so dependencies should be installed at the root level.
- For theme-specific or plugin-specific dependencies, you can run:
  ```bash
  npm install --workspace=tenup-theme
  npm install --workspace=tenup-plugin
  ```
- Check that all required PHP extensions are enabled in your PHP configuration.

## Build Failures
- Check for errors in the terminal output when running build scripts from `wp-content`.
- Ensure Node.js and npm versions are compatible with the project requirements (Node.js >= 20.0.0, npm >= 9.0.0).
- Run `npm run build` or `npm run dev` as appropriate from `wp-content`.
- For theme-specific or plugin-specific builds, you can use:
  ```bash
  npm run watch:theme  # Watch theme files for changes
  npm run watch:plugin  # Watch plugin files for changes
  ```
- If you encounter JavaScript or CSS linting errors, try running:
  ```bash
  npm run lint-js  # Check JavaScript files
  npm run format-js  # Fix JavaScript formatting issues
  npm run lint-style  # Check CSS files
  ```
- Clear the `dist/` directory if you suspect cached build files are causing issues:
  ```bash
  npm run clean-dist
  ```

## Code Quality Issues
- The project uses several code quality tools that might prevent commits or builds if standards aren't met:
	- PHP CodeSniffer (PHPCS) for PHP coding standards
	- PHPStan for PHP static analysis
	- ESLint for JavaScript linting
	- Stylelint for CSS linting
- To run these tools manually:
  ```bash
  composer lint  # Run PHPCS
  composer lint-fix  # Fix PHPCS issues automatically
  composer static  # Run PHPStan
  npm run lint-js  # Run ESLint
  npm run lint-style  # Run Stylelint
  ```
- Pre-commit hooks will automatically run these tools on staged files before commits.
- If you need to bypass pre-commit hooks temporarily (not recommended), you can use `git commit --no-verify`.

## Debugging Tips
- Enable `WP_DEBUG` in your site's `wp-config.php` for detailed error messages:
  ```php
  define( 'WP_DEBUG', true );
  ```
- Use [Spatie Ignition](../testing-quality/debugging-php.md) for enhanced PHP error pages.
- Check logs in the Local WP log viewer or in the `wp-content` directory if available.
- Use Query Monitor plugin (included as a dev dependency) for detailed debugging information.
- For JavaScript debugging, use browser developer tools and the source maps generated in development mode.
- If Spatie Ignition is not displaying errors:
	- Ensure that the autoloader path in `require_once` is correct.
	- Verify that composer install has been run successfully.
	- Confirm that `WP_DEBUG` is set to true.

## Common Error Messages
- **"Vendor autoload file not found. Please run `composer install`."**: The Composer dependencies haven't been installed. Run `composer install` in the `wp-content` directory.
- **"Node Sass does not yet support your current environment"**: Your Node.js version is incompatible with the installed version of Node Sass. Update Node.js or reinstall dependencies.
- **"Error: Node.js version x.x.x is not supported, please use Node.js >= 20.0.0"**: Update your Node.js version to meet the requirements.
- **"PHP Fatal error: Uncaught Error: Class 'XYZ' not found"**: This could indicate a missing dependency or an autoloading issue. Run `composer dump-autoload` to regenerate the autoloader.
- **"Failed to load external module @10up/babel-preset-default"**: Ensure all npm dependencies are installed correctly with `npm install`.

## Where to Get Help
- Review the documentation in the `docs/` directory.
- Search existing issues or open a new one in the project repository.
- Ask questions in the project's community channels.
- Check the [10up Engineering Best Practices](https://10up.github.io/Engineering-Best-Practices/) for guidance on WordPress development.
