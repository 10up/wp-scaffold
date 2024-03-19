<?php
/**
 * Plugin Name:       10up Plugin Scaffold
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
 * Update URI:        https://github.com/10up/wp-scaffold
 *
 * @package           TenUpPlugin
 */

// Useful global constants.
define( 'TENUP_PLUGIN_VERSION', '0.1.0' );
define( 'TENUP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TENUP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'TENUP_PLUGIN_INC', TENUP_PLUGIN_PATH . 'includes/' );
define( 'TENUP_PLUGIN_DIST_URL', TENUP_PLUGIN_URL . 'dist/' );
define( 'TENUP_PLUGIN_DIST_PATH', TENUP_PLUGIN_PATH . 'dist/' );

$is_local_env = in_array( wp_get_environment_type(), [ 'local', 'development' ], true );
$is_local_url = strpos( home_url(), '.test' ) || strpos( home_url(), '.local' );
$is_local     = $is_local_env || $is_local_url;

if ( $is_local && file_exists( __DIR__ . '/dist/fast-refresh.php' ) ) {
	require_once __DIR__ . '/dist/fast-refresh.php';
	TenUpToolkit\set_dist_url_path( basename( __DIR__ ), TENUP_PLUGIN_DIST_URL, TENUP_PLUGIN_DIST_PATH );
}

// Require Composer autoloader if it exists.
if ( file_exists( TENUP_PLUGIN_PATH . 'vendor/autoload.php' ) ) {
	require_once TENUP_PLUGIN_PATH . 'vendor/autoload.php';
}

// Include files.
require_once TENUP_PLUGIN_INC . '/utility.php';
require_once TENUP_PLUGIN_INC . '/core.php';

// Activation/Deactivation.
register_activation_hook( __FILE__, '\TenUpPlugin\Core\activate' );
register_deactivation_hook( __FILE__, '\TenUpPlugin\Core\deactivate' );

// Bootstrap.
TenUpPlugin\Core\setup();
