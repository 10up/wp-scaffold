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

	add_filter( 'block_categories_all', $n( 'blocks_categories' ), 10, 2 );

	add_action( 'init', $n( 'register_theme_blocks' ) );
	add_action( 'init', $n( 'register_theme_block_patterns' ) );
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
 * Add in blocks that are registered in this theme
 *
 * @return void
 */
function register_theme_blocks() {
	// Filter the plugins URL to allow us to have blocks in themes with linked assets. i.e editorScripts
	add_filter( 'plugins_url', __NAMESPACE__ . '\filter_plugins_url', 10, 2 );

	// Require custom blocks.
	require_once TENUP_THEME_BLOCK_DIR . '/example-block/register.php';

	// Call block register functions for each block.
	Example\register();

	// Remove the filter after we register the blocks
	remove_filter( 'plugins_url', __NAMESPACE__ . '\filter_plugins_url', 10, 2 );
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
		'editor-style',
		TENUP_THEME_TEMPLATE_URL . '/dist/css/editor-style.css',
		[],
		TENUP_THEME_VERSION
	);

}

/**
 * Filters the registered block categories.
 *
 * @param array $categories Registered categories.
 *
 * @return array Filtered categories.
 */
function blocks_categories( $categories ) {
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

/**
 * Manage block pattern categories
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-patterns/
 *
 * @return void
 */
function register_block_pattern_categories() {

	// Register a block pattern category
	register_block_pattern_category(
		'10up-theme',
		array( 'label' => __( '10up Theme', 'tenup' ) )
	);
}

/**
 * Register theme block patterns
 */
function register_theme_block_patterns() {
	$default_headers = array(
		'title'         => 'Pattern Name',
		'description'   => 'Description',
		'viewportWidth' => 'Viewport Width',
		'categories'    => 'Categories',
		'keywords'      => 'Keywords',
		'blockTypes'    => 'Block Types',
	);

	// Register patterns for the active theme, for both parent and child theme,
	// if applicable.
	foreach ( wp_get_active_and_valid_themes() as $theme ) {
		$pattern_directory_path = $theme . '/patterns/';
		if ( file_exists( $pattern_directory_path ) ) {
			$files = glob( $pattern_directory_path . '*.html' );
			if ( $files ) {
				foreach ( $files as $file ) {
					// Parse pattern slug from file name.
					if ( ! preg_match( '#/(?P<slug>[A-z0-9_-]+)\.html$#', $file, $matches ) ) {
						continue;
					}
					// Example name: 10up-theme/example-pattern.
					$pattern_name = get_stylesheet() . '/' . $matches['slug'];

					$pattern_options = get_file_data( $file, $default_headers );

					// Title is a required property.
					if ( ! $pattern_options['title'] ) {
						continue;
					}

					// For properties of type array, parse data as comma-separated.
					foreach ( array( 'categories', 'keywords', 'blockTypes' ) as $property ) {
						$pattern_options[ $property ] = array_filter(
							preg_split(
								'/[\s,]+/',
								(string) $pattern_options[ $property ]
							)
						);
					}

					// Parse properties of type int.
					foreach ( array( 'viewportWidth' ) as $property ) {
						$pattern_options[ $property ] = (int) $pattern_options[ $property ];
					}

					// Remove up empty values, so as not to override defaults.
					foreach ( array_keys( $default_headers ) as $property ) {
						if ( empty( $pattern_options[ $property ] ) ) {
							unset( $pattern_options[ $property ] );
						}
					}

					// The actual pattern is everything following the leading comment.
					$raw_content                = file_get_contents( $file );
					$token                      = '-->';
					$pattern_options['content'] = substr(
						$raw_content,
						strpos( $raw_content, $token ) + strlen( $token )
					);
					if ( ! $pattern_options['content'] ) {
						continue;
					}

					register_block_pattern( $pattern_name, $pattern_options );
				}
			}
		}
		if ( file_exists( $pattern_directory_path ) ) {
			$files = glob( $pattern_directory_path . '*.php' );
			if ( $files ) {
				foreach ( $files as $file ) {

					// Parse pattern slug from file name.
					if ( ! preg_match( '#/(?P<slug>[A-z0-9_-]+)\.php$#', $file, $matches ) ) {
						continue;
					}
					// Example name: 10up-theme/example-pattern.
					$pattern_name = get_stylesheet() . '/' . $matches['slug'];

					$pattern_options = get_file_data( $file, $default_headers );

					// Title is a required property.
					if ( ! $pattern_options['title'] ) {
						continue;
					}

					// For properties of type array, parse data as comma-separated.
					foreach ( array( 'categories', 'keywords', 'blockTypes' ) as $property ) {
						$pattern_options[ $property ] = array_filter(
							preg_split(
								'/[\s,]+/',
								(string) $pattern_options[ $property ]
							)
						);
					}

					// Parse properties of type int.
					foreach ( array( 'viewportWidth' ) as $property ) {
						$pattern_options[ $property ] = (int) $pattern_options[ $property ];
					}

					// Remove up empty values, so as not to override defaults.
					foreach ( array_keys( $default_headers ) as $property ) {
						if ( empty( $pattern_options[ $property ] ) ) {
							unset( $pattern_options[ $property ] );
						}
					}

					// The actual pattern content is the output of the file.
					ob_start();
					include $file;
					$pattern_options['content'] = ob_get_clean();
					if ( ! $pattern_options['content'] ) {
						continue;
					}

					register_block_pattern( $pattern_name, $pattern_options );
				}
			}
		}
	}
}
