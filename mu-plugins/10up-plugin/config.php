<?php
/**
 * config.php
 *
 * Plugin configuration
 *
 * @package TenUpPlugin
 */

$build_version = '';

if ( file_exists( __DIR__ . '/.commit' ) ) {
	$build_version .= file_get_contents( __DIR__ . '/.commit' ); // @codingStandardsIgnoreLine
}

if ( file_exists( __DIR__ . '/.nocache' ) ) {
	$build_version .= '-' . uniqid();
}

tenup_plugin_define( 'TENUP_PLUGIN', __DIR__ . '/plugin.php' );
tenup_plugin_define( 'TENUP_PLUGIN_VERSION', '0.1.0' );
tenup_plugin_define( 'TENUP_PLUGIN_BUILD_VERSION', $build_version );
tenup_plugin_define( 'TENUP_PLUGIN_DIR', __DIR__ );
tenup_plugin_define( 'TENUP_PLUGIN_URL', plugin_dir_url( TENUP_PLUGIN ) );
