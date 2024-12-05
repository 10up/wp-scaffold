# Integrating Spatie Ignition for Enhanced Error Handling in WordPress

To improve the error-handling experience during development, you can utilize [Spatie Ignition](https://github.com/spatie/ignition). Ignition provides detailed, user-friendly error pages that make debugging more straightforward and more efficient.

This guide will walk you through the steps to set up and use Ignition.

## Prerequisites

* Composer is installed on your system.
* PHP version compatible with Spatie Ignition (check the [Ignition documentation](https://github.com/spatie/ignition) for requirements).

## Installation

To install it, run the following command in your project’s root directory:

```bash
composer require --dev spatie/ignition
```

This command will install the Ignition package into your project’s `vendor` directory.

## Configuration

To enable Ignition, you’ll need to modify your `wp-config.php` file.

### 1. Include the Composer Autoloader

Add the following line to your  `wp-config.php` file, right below the `/* Add any custom values between this line and the "stop editing" line. */` line.

```php
/* Add any custom values between this line and the "stop editing" line. */

require_once( dirname( __FILE__ ) . '/wp-content/vendor/autoload.php' );
```

This line ensures that all Composer-managed packages, including Ignition, are autoloaded into your project.

### 2. Register Ignition

After including the autoloader, add the following code to register Ignition:

```php
/* Add any custom values between this line and the "stop editing" line. */

require_once( dirname( __FILE__ ) . '/wp-content/vendor/autoload.php' );

\Spatie\Ignition\Ignition::make()
  ->setTheme( 'dark' )
  ->shouldDisplayException( true === WP_DEBUG )
  ->register();
```

This snippet does the following:

* `setTheme('dark')`: Sets the error page theme to dark. You can change this to ``'light'`` if you prefer.
* `shouldDisplayException( true === WP_DEBUG )`: Configures Ignition to display exceptions only when `WP_DEBUG` is set to `true`.
* `register()`: Finalizes the setup and activates Ignition.

### 3. Ensure `WP_DEBUG` is Enabled

Make sure that `WP_DEBUG` is set to `true` in your `wp-config.php` during development:

```php
define( 'WP_DEBUG', true );
```

## **Usage**

With Ignition set up, any errors or exceptions that occur during development will be displayed using Ignition’s detailed error pages. These pages include:

* **Stack Traces**: Shows the sequence of function calls that led to the error.
* **Code Snippets**: Displays the relevant code around where the error occurred.
* **Contextual Data**: Provides information about variables and the environment at the time of the error.

## Customization

You can further customize Ignition to suit your needs:

### Changing the Theme

If you prefer a different theme, modify the `setTheme` method:

```php
->setTheme( 'light' ) *// Options are 'light' or 'dark'*
```

### Adjusting Error Display Conditions

You can control when errors are displayed by modifying the `shouldDisplayException` method:

```php
->shouldDisplayException( true )
```

Setting this to true will always display exceptions, regardless of the `WP_DEBUG` setting.

## Troubleshooting

### Ignition Not Displaying Errors

* Ensure that the autoloader path in `require_once` is correct.
* Verify that composer install has been run successfully.
* Confirm that `WP_DEBUG` is set to true.

### Composer Autoload File Not Found

* Double-check the path in your `require_once` statement. It should point to the `vendor/autoload.php` file within the `wp-content` directory.

## Additional Resources

* [Spatie Ignition Documentation](https://github.com/spatie/ignition)
* [WordPress Debugging Guide](https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/)
