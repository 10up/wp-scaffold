<?php
/**
 * PHPUnit bootstrap file
 *
 * @package PublixCollectivePlugin
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

// Added so we can target the WP content directory from our tests
define( 'CONTENT_DIR', dirname( __DIR__, 3 ) );
define( 'PHPUNIT_RUNNER', true );
define( 'SAVEQUERIES', false );
define( 'WP_ENVIRONMENT_TYPE', 'development' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo sprintf( 'Could not find %s/includes/functions.php, have you run bin/install-wp-tests.sh ?', esc_attr( $_tests_dir ) ) . PHP_EOL;
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require __DIR__ . '/mu-plugins/10up-plugin/plugin.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/mu-plugins/10up-plugin/vendor/autoload.php';
require __DIR__ . '/themes/10up-theme/vendor/autoload.php';
