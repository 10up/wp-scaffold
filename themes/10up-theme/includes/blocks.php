<?php
/**
 * Gutenberg Blocks setup
 *
 * @package TenUpScaffold\Core
 */

namespace TenUpTheme\Blocks;

use TenUpTheme\Blocks\Example;


/**
 * Set up blocks
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'enqueue_block_editor_assets', $n( 'blocks_editor_styles' ) );

	add_filter( 'block_categories', $n( 'blocks_categories' ), 10, 2 );

	/*
	// Uncomment to register custom blocks via the Block Library plugin.

	add_filter( 'tenup_available_blocks', function ( $blocks ) {
		$blocks['example-block'] = [
			'dir' => TENUP_THEME_BLOCK_DIR,
		];
		return $blocks;
	} );
	*/


	// Uncomment to register custom blocks via the theme.

	/*
	add_action(
		'init',
		function() {
			// Filter the plugins URL to allow us to have blocks in themes with linked assets. i.e editorScripts
			add_filter( 'plugins_url', __NAMESPACE__ . '\filter_plugins_url', 10, 2 );


			// Require custom blocks.
			require_once TENUP_THEME_BLOCK_DIR . '/example-block/register.php';

			// Call block register functions for each block.
			Example\register();

			// Remove the filter after we register the blocks
			remove_filter( 'plugins_url', __NAMESPACE__ . '\filter_plugins_url', 10, 2 );
		}
	);
	*/

}

/**
 * Filter the plugins_url to allow us to use assets from theme.
 *
 * @param string $url  The plugins url
 * @param string $path The path to the asset.
 *
 * @return string The overridden url to the block asset.
 */
function filter_plugins_url( $url, $path ) {
	$file = preg_replace( '/\.\.\//', '', $path );
	return trailingslashit( get_stylesheet_directory_uri() ) . $file;
}

/**
 * Enqueue shared frontend and editor JavaScript for blocks.
 *
 * @return void
 */
function blocks_scripts() {

	wp_enqueue_script(
		'blocks',
		TENUP_THEME_TEMPLATE_URL . '/dist/blocks.js',
		[],
		TENUP_THEME_VERSION,
		true
	);
}


/**
 * Enqueue editor-only JavaScript/CSS for blocks.
 *
 * @return void
 */
function blocks_editor_styles() {
	wp_enqueue_style(
		'editor-style',
		TENUP_THEME_TEMPLATE_URL . '/dist/editor-style.css',
		[],
		TENUP_THEME_VERSION
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
				'slug'  => 'tenup-scaffold-blocks',
				'title' => __( 'Custom Blocks', 'tenup-theme' ),
			),
		)
	);
}
