<?php

// Useful global constants.
define( 'TENUP_PLUGIN_VERSION', '0.1.0' );
define( 'TENUP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TENUP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'TENUP_PLUGIN_INC', TENUP_PLUGIN_PATH . 'includes/' );

// Require Composer autoloader if it exists.
if ( file_exists( TENUP_PLUGIN_PATH . 'vendor/autoload.php' ) ) {
	require_once TENUP_PLUGIN_PATH . 'vendor/autoload.php';
}

// Include files.
require_once TENUP_PLUGIN_INC . '/core.php';

// Activation/Deactivation.
register_activation_hook( __FILE__, '\TenUpPlugin\Core\activate' );
register_deactivation_hook( __FILE__, '\TenUpPlugin\Core\deactivate' );

// Bootstrap.
TenUpPlugin\Core\setup();
