<?php
/**
 * Gutenberg Blocks setup
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
	$n = function ( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'enqueue_block_editor_assets', $n( 'blocks_editor_styles' ) );

	add_action( 'init', $n( 'register_theme_blocks' ) );

	add_action( 'init', $n( 'register_block_pattern_categories' ) );

	/*
	If you are using the block library, remove the blocks you don't need.

	add_filter( 'tenup_available_blocks', function ( $blocks ) {
		if ( ! empty( $blocks['integrated-hero'] ) ) {
			unset( $blocks['integrated-hero'] );
		}

		return $blocks;
	} );
	*/
}

/**
 * Automatically registers all blocks that are located within the includes/blocks directory
 *
 * @return void
 */
function register_theme_blocks() {
	global $wp_version;

	$is_pre_wp_6 = version_compare( $wp_version, '6.0', '<' );

	if ( $is_pre_wp_6 ) {
		// Filter the plugins URL to allow us to have blocks in themes with linked assets. i.e editorScripts
		add_filter( 'plugins_url', __NAMESPACE__ . '\filter_plugins_url', 10, 2 );
	}

	// Register all the blocks in the theme
	if ( file_exists( TENUP_THEME_BLOCK_DIST_DIR ) ) {
		$block_json_files = glob( TENUP_THEME_BLOCK_DIST_DIR . '*/block.json' );

		// auto register all blocks that were found.
		foreach ( $block_json_files as $filename ) {

			$block_folder = dirname( $filename );

			$block_options = [];

			$markup_file_path = $block_folder . '/markup.php';
			if ( file_exists( $markup_file_path ) ) {

				// only add the render callback if the block has a file called markup.php in it's directory
				$block_options['render_callback'] = function ( $attributes, $content, $block ) use ( $block_folder ) {

					// create helpful variables that will be accessible in markup.php file
					$context = $block->context;

					// get the actual markup from the markup.php file
					ob_start();
					include $block_folder . '/markup.php';
					return ob_get_clean();
				};
			}

			register_block_type_from_metadata( $block_folder, $block_options );
		}
	}

	if ( $is_pre_wp_6 ) {
		// Remove the filter after we register the blocks
		remove_filter( 'plugins_url', __NAMESPACE__ . '\filter_plugins_url', 10, 2 );
	}
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
 * Enqueue editor-only JavaScript/CSS for blocks.
 *
 * @return void
 */
function blocks_editor_styles() {
	wp_enqueue_style(
		'editor-style-overrides',
		TENUP_THEME_TEMPLATE_URL . '/dist/css/editor-style-overrides.css',
		[],
		Utility\get_asset_info( 'editor-style-overrides', 'version' )
	);

	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		wp_enqueue_script(
			'editor-style-overrides',
			TENUP_THEME_TEMPLATE_URL . '/dist/js/editor-style-overrides.js',
			Utility\get_asset_info( 'editor-style-overrides', 'dependencies' ),
			Utility\get_asset_info( 'editor-style-overrides', 'version' ),
			true
		);
	}
}

/**
 * Register block pattern categories
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-patterns/
 *
 * @return void
 */
function register_block_pattern_categories() {

	// Register a block pattern category
	register_block_pattern_category(
		'10up-theme',
		[ 'label' => __( '10up Theme', 'tenup-theme' ) ]
	);
}
