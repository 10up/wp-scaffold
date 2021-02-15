<?php
/**
 * config.php
 *
 * Plugin configuration
 *
 * @package TenUpPlugin
 */

$build_version = '';

/**
 * If the CI environment generated a .commit file use that as the build version.
 */
if ( file_exists( __DIR__ . '/.commit' ) ) {
	$build_version .= file_get_contents( __DIR__ . '/.commit' ); // @codingStandardsIgnoreLine
}

/**
 * If a nocache file is present, use a uniq build version to disable caching.
 */
if ( file_exists( __DIR__ . '/.nocache' ) ) {
	$build_version .= '-' . uniqid();
}

tenup_plugin_define( 'TENUP_PLUGIN', __DIR__ . '/plugin.php' );
tenup_plugin_define( 'TENUP_PLUGIN_VERSION', '0.1.0' . $build_version );
tenup_plugin_define( 'TENUP_PLUGIN_BUILD_VERSION', $build_version );
tenup_plugin_define( 'TENUP_PLUGIN_DIR', __DIR__ );
tenup_plugin_define( 'TENUP_PLUGIN_URL', plugin_dir_url( TENUP_PLUGIN ) );

// Post Type Names
tenup_plugin_define( 'POST_POST_TYPE', 'post' );
tenup_plugin_define( 'PAGE_POST_TYPE', 'page' );
