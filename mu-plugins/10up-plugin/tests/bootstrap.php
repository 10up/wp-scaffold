<?php
/**
 * PHPUnit bootstrap file
 *
 * @package 10upTheme
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

// Added so we can target the WP content directory from our tests
define( 'CONTENT_DIR', dirname( __DIR__, 3 ) );
define( 'TEST_DIR', __DIR__ );
define( 'PHPUNIT_RUNNER', true );
define( 'FIXTURES_DIR', TEST_DIR . '/fixtures/' );
define( 'SAVEQUERIES', false );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo sprintf( 'Could not find %s/includes/functions.php, have you run bin/install-wp-tests.sh ?', esc_attr( $_tests_dir ) ) . PHP_EOL;
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	// Load our plugins.
	require dirname( __DIR__, 1 ) . '/plugin.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
