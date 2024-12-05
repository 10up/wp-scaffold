<?php
/**
 * WP Constants used by PHPStan
 *
 * These should be updated to match constants that are set in any custom plugins or themes that will be anylised.
 *
 * @package TenUpPhpStan
 */

// Change these when you update the constants in the plugin.
define( 'TENUP_PLUGIN_VERSION', '0.1.0' );
define( 'TENUP_PLUGIN_URL', '' );
define( 'TENUP_PLUGIN_PATH', '' );
define( 'TENUP_PLUGIN_INC', TENUP_PLUGIN_PATH . 'includes/' );

// Change these when you update the constants in the theme.
define( 'TENUP_THEME_VERSION', '0.1.0' );
define( 'TENUP_THEME_TEMPLATE_URL', '' );
define( 'TENUP_THEME_PATH', '/' );
define( 'TENUP_THEME_DIST_PATH', TENUP_THEME_PATH . 'dist/' );
define( 'TENUP_THEME_DIST_URL', TENUP_THEME_TEMPLATE_URL . '/dist/' );
define( 'TENUP_THEME_INC', TENUP_THEME_PATH . 'includes/' );
define( 'TENUP_THEME_BLOCK_DIR', TENUP_THEME_INC . 'blocks/' );
