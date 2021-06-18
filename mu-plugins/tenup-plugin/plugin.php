<?php
/**
 * Plugin Name:       TenUp Plugin Scaffold
 * Plugin URI:        https://github.com/TenUp/plugin-scaffold
 * Description:       A brief description of the plugin.
 * Version:           0.1.0
 * Requires at least: 4.9
 * Requires PHP:      7.2
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tenup-plugin
 * Domain Path:       /languages
 *
 * @package           TenUpPlugin
 */

// Include files.
//require_once TENUP_PLUGIN_INC . 'functions/core.php';

// Activation/Deactivation.
//register_activation_hook( __FILE__, '\TenUpPlugin\Core\activate' );
//register_deactivation_hook( __FILE__, '\TenUpPlugin\Core\deactivate' );

// Bootstrap.
//TenUpPlugin\Core\setup();

// Require Composer autoloader if it exists.
//if ( file_exists( TENUP_PLUGIN_PATH . 'vendor/autoload.php' ) ) {
	//require_once TENUP_PLUGIN_PATH . 'vendor/autoload.php';
//}


/**
 * Small wrapper around PHP's define function. The defined constant is
 * ignored if it has already been defined. This allows the
 * wp-config.php and/or config.local.php to override any constant in the plugin's config.php.
 *
 * @param string $name The constant name
 * @param mixed  $value The constant value
 * @return void
 */
function tenup_plugin_define( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}

/**
 * Optional config for PHPUnit Tests.
 */
if ( file_exists( __DIR__ . '/config.test.php' ) && defined( 'PHPUNIT_RUNNER' ) ) {
	require_once __DIR__ . '/config.test.php';
}

/**
 * Optional config specific to the current environment.
 */
if ( file_exists( __DIR__ . '/config.local.php' ) ) {
	require_once __DIR__ . '/config.local.php';
}

/**
 * Load main config for the plugin
 */
require_once __DIR__ . '/config.php';

/**
 * Initiatializes the main plugin after loading Composer. If Composer
 * autoloader is absent exits early with an error message.
 */
function tenup_plugin_autorun() {
	if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		error_log( 'Fatal Error: Composer not setup in ' . TENUP_PLUGIN_PLUGIN_DIR ); // @codingStandardsIgnoreLine
		add_action( 'admin_notices', 'tenup_plugin_autoload_notice' );
		return;
	}

	require_once __DIR__ . '/vendor/autoload.php';

	$plugin = \TenUpPlugin\Plugin::get_instance();
	$plugin->enable();
}

/**
 * Displays an error notice if the plugin's composer was not setup.
 */
function tenup_plugin_autoload_notice() {
	$class   = 'notice notice-error';
	$message = 'Error: Please run $ composer install in this directory: ' . TENUP_PLUGIN_DIR;

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_attr( $message ) );
	error_log( $message ); // @codingStandardsIgnoreLine
}

tenup_plugin_autorun();

