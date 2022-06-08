<?php
/**
 * WP Theme constants and setup functions
 *
 * @package TenUpTheme
 */

// Useful global constants.
define( 'TENUP_THEME_VERSION', '0.1.0' );
define( 'TENUP_THEME_TEMPLATE_URL', get_template_directory_uri() );
define( 'TENUP_THEME_PATH', get_template_directory() . '/' );
define( 'TENUP_THEME_DIST_PATH', TENUP_THEME_PATH . 'dist/' );
define( 'TENUP_THEME_DIST_URL', TENUP_THEME_TEMPLATE_URL . '/dist/' );
define( 'TENUP_THEME_INC', TENUP_THEME_PATH . 'includes/' );
define( 'TENUP_THEME_BLOCK_DIR', TENUP_THEME_INC . 'blocks/' );

$check_hmr =               in_array( wp_get_environment_type(), [ 'local', 'development' ], true );

if ( $check_hmr ) {
	$hmr_file_path       = __DIR__ . '/dist/fast-refresh.php';
	// Only check for file_exist on development environments
	$has_hmr_file        = file_exists( $hmr_file_path );
	$is_debugging_script = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

	if ( $has_hmr_file ) {
		if ( $is_debugging_script ) {
			require_once $hmr_file_path;
			TenUpToolkit\set_dist_url_path( basename( __DIR__ ), TENUP_THEME_DIST_URL, TENUP_THEME_DIST_PATH );
		} else {
			// This is development environment with a detected fast-refresh-file
			wp_die(
				sprintf(
					"You're using <a href='%s' target='_blank'>10up-toolkit</a>'s
					Hot Module Reloading but don't have <code>SCRIPT_DEBUG</code> enabled.<br/>
					Learn more about <a href='%s' target='_blank'>enabling HMR</a>.",
					"https://github.com/10up/10up-toolkit/tree/develop/packages/toolkit",
					"https://github.com/10up/10up-toolkit/tree/develop/packages/toolkit#hmr-and-fast-refresh"
				)
			);
		}
	}
}

require_once TENUP_THEME_INC . 'core.php';
require_once TENUP_THEME_INC . 'overrides.php';
require_once TENUP_THEME_INC . 'template-tags.php';
require_once TENUP_THEME_INC . 'utility.php';
require_once TENUP_THEME_INC . 'blocks.php';

// Run the setup functions.
TenUpTheme\Core\setup();
TenUpTheme\Blocks\setup();

// Require Composer autoloader if it exists.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

if ( ! function_exists( 'wp_body_open' ) ) {

	/**
	 * Shim for the the new wp_body_open() function that was added in 5.2
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}
