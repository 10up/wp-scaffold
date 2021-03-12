<?php
/**
 * Core setup, site hooks and filters.
 *
 * @package TenUpTheme
 */

namespace TenUpTheme\Core;

use TenUpTheme\Utility;

/**
 * Set up theme defaults and register supported WordPress features.
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'after_setup_theme', $n( 'i18n' ) );
	add_action( 'after_setup_theme', $n( 'theme_setup' ) );
	add_action( 'wp_enqueue_scripts', $n( 'scripts' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_styles' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_scripts' ) );
	add_action( 'enqueue_block_editor_assets', $n( 'core_block_overrides' ) );
	add_action( 'wp_enqueue_scripts', $n( 'styles' ) );
	add_action( 'wp_head', $n( 'js_detection' ), 0 );
	add_action( 'wp_head', $n( 'add_manifest' ), 10 );

	add_filter( 'script_loader_tag', $n( 'script_loader_tag' ), 10, 2 );
}

/**
 * Makes Theme available for translation.
 *
 * Translations can be added to the /languages directory.
 * If you're building a theme based on "tenup-theme", change the
 * filename of '/languages/TenUpTheme.pot' to the name of your project.
 *
 * @return void
 */
function i18n() {
	load_theme_textdomain( 'tenup-theme', TENUP_THEME_PATH . '/languages' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function theme_setup() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'gallery',
		)
	);

	// This theme uses wp_nav_menu() in three locations.
	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Menu', 'tenup-theme' ),
		)
	);
}

/**
 * Enqueue scripts for front-end.
 *
 * @return void
 */
function scripts() {

	wp_enqueue_script(
		'frontend',
		TENUP_THEME_TEMPLATE_URL . '/dist/js/frontend.js',
		Utility\get_asset_info( 'frontend', 'dependencies' ),
		Utility\get_asset_info( 'frontend', 'version' ),
		true
	);

	if ( is_page_template( 'templates/page-styleguide.php' ) ) {
		wp_enqueue_script(
			'styleguide',
			TENUP_THEME_TEMPLATE_URL . '/dist/js/styleguide.js',
			Utility\get_asset_info( 'styleguide', 'dependencies' ),
			Utility\get_asset_info( 'styleguide', 'version' ),
			true
		);
	}

	/*
	wp_enqueue_script(
		'shared',
		TENUP_THEME_TEMPLATE_URL . '/dist/js/shared.js',
		Utility\get_asset_info( 'shared', 'dependencies' ),
		Utility\get_asset_info( 'shared', 'version' ),
		true
	);
	*/
}

/**
 * Enqueue scripts for admin
 *
 * @return void
 */
function admin_scripts() {
	wp_enqueue_script(
		'admin',
		TENUP_THEME_TEMPLATE_URL . '/dist/js/admin.js',
		Utility\get_asset_info( 'admin', 'dependencies' ),
		Utility\get_asset_info( 'admin', 'version' ),
		true
	);

	/*
	wp_enqueue_script(
		'shared',
		TENUP_THEME_TEMPLATE_URL . '/dist/js/shared.js',
		Utility\get_asset_info( 'shared', 'dependencies' ),
		Utility\get_asset_info( 'shared', 'version' ),
		true
	);
	*/
}

/**
 * Enqueue core block filters, styles and variations.
 *
 * @return void
 */
function core_block_overrides() {
	$overrides = TENUP_THEME_DIST_PATH . 'js/core-block-overrides.asset.php';
	if ( file_exists( $overrides ) ) {
		$dep = require_once $overrides;
		wp_enqueue_script(
			'core-block-overrides',
			TENUP_THEME_DIST_URL . 'js/core-block-overrides.js',
			$dep['dependencies'],
			$dep['version'],
			true
		);
	}
}

/**
 * Enqueue styles for admin
 *
 * @return void
 */
function admin_styles() {

	wp_enqueue_style(
		'admin-style',
		TENUP_THEME_TEMPLATE_URL . '/dist/css/admin-style.css',
		[],
		Utility\get_asset_info( 'admin-style', 'version' )
	);

	/*
	wp_enqueue_style(
		'shared-style',
		TENUP_THEME_TEMPLATE_URL . '/dist/css/shared-style.css',
		[],
		Utility\get_asset_info( 'shared-style', 'version' )
	);
	*/
}

/**
 * Enqueue styles for front-end.
 *
 * @return void
 */
function styles() {

	wp_enqueue_style(
		'styles',
		TENUP_THEME_TEMPLATE_URL . '/dist/css/style.css',
		[],
		Utility\get_asset_info( 'style', 'version' )
	);

	if ( is_page_template( 'templates/page-styleguide.php' ) ) {
		wp_enqueue_style(
			'styleguide',
			TENUP_THEME_TEMPLATE_URL . '/dist/css/styleguide-style.css',
			[],
			Utility\get_asset_info( 'styleguide-style', 'version' )
		);
	}
}

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @return void
 */
function js_detection() {

	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}

/**
 * Add async/defer attributes to enqueued scripts that have the specified script_execution flag.
 *
 * @link https://core.trac.wordpress.org/ticket/12009
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string
 */
function script_loader_tag( $tag, $handle ) {
	$script_execution = wp_scripts()->get_data( $handle, 'script_execution' );

	if ( ! $script_execution ) {
		return $tag;
	}

	if ( 'async' !== $script_execution && 'defer' !== $script_execution ) {
		return $tag;
	}

	// Abort adding async/defer for scripts that have this script as a dependency. _doing_it_wrong()?
	foreach ( wp_scripts()->registered as $script ) {
		if ( in_array( $handle, $script->deps, true ) ) {
			return $tag;
		}
	}

	// Add the attribute if it hasn't already been added.
	if ( ! preg_match( ":\s$script_execution(=|>|\s):", $tag ) ) {
		$tag = preg_replace( ':(?=></script>):', " $script_execution", $tag, 1 );
	}

	return $tag;
}

/**
 * Appends a link tag used to add a manifest.json to the head
 *
 * @return void
 */
function add_manifest() {
	echo "<link rel='manifest' href='" . esc_url( TENUP_THEME_TEMPLATE_URL . '/manifest.json' ) . "' />";
}
