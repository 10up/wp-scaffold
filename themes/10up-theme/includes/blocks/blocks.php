<?php
/**
 * Blocks setup
 *
 * @package TenUpTheme
 */

namespace TenUpTheme\Blocks;

use TenUpTheme\Utility;

/**
 * Set up blocks
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'init', $n( 'register_blocks' ) );
	add_action( 'enqueue_block_editor_assets', $n( 'block_scripts_styles' ) );
	add_filter( 'block_categories', $n( 'blocks_categories' ), 10, 2 );
}

/**
 * Register blocks via PHP
 *
 * @return void
 */
function register_blocks() {
	require_once __DIR__ . '/example-block/index.php';

	ExampleBlock\register();
}

/**
 * Enqueue block scripts and styles
 *
 * @return void
 */
function block_scripts_styles() {
	wp_enqueue_script(
		'blocks',
		TENUP_THEME_TEMPLATE_URL . '/dist/blocks.js',
		Utility\get_asset_info( 'blocks', 'dependencies' ),
		Utility\get_asset_info( 'blocks', 'version' ),
		true
	);

	/**
	 * Note that CSS for each block is stored in /assets/css/blocks
	 */

	wp_enqueue_style(
		'editor-style',
		TENUP_THEME_TEMPLATE_URL . '/dist/css/editor-style.css',
		[],
		Utility\get_asset_info( 'editor-style', 'version' )
	);
}

/**
 * Filters the registered block categories.
 *
 * @param array  $categories Registered categories.
 * @param object $post       The post object.
 *
 * @return array Filtered categories.
 */
function blocks_categories( $categories, $post ) {
	if ( ! in_array( $post->post_type, array( 'post', 'page' ), true ) ) {
		return $categories;
	}

	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'tenup-theme-blocks',
				'title' => __( 'Custom Blocks', 'tenup-theme' ),
			),
		)
	);
}
