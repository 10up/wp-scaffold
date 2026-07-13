<?php
/**
 * Gutenberg Blocks setup
 *
 * @package TenUpTheme
 */

namespace TenUpTheme;

use TenupFramework\Assets\GetAssetInfo;
use TenupFramework\Module;
use TenupFramework\ModuleInterface;

/**
 * Blocks module.
 *
 * @package TenUpTheme
 */
class Blocks implements ModuleInterface {

	use Module;
	use GetAssetInfo;

	/**
	 * Can this module be registered?
	 *
	 * @return bool
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Register any hooks and filters.
	 *
	 * @return void
	 */
	public function register() {
		$this->setup_asset_vars(
			dist_path: TENUP_THEME_DIST_PATH,
			fallback_version: TENUP_THEME_VERSION
		);
		add_action( 'enqueue_block_editor_assets', [ $this, 'blocks_editor_styles' ] );
		add_action( 'init', [ $this, 'enqueue_block_specific_styles' ] );
		add_action( 'init', [ $this, 'register_theme_blocks' ] );
		add_action( 'init', [ $this, 'register_block_pattern_categories' ] );
		add_filter( 'should_load_separate_core_block_assets', '__return_true' );

		// Prevents third-party blocks from being suggested in the block inserter.
		remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );
	}

	/**
	 * Automatically registers all blocks from the generated Vite blocks manifest.
	 *
	 * Built by `wpBlocks` (@10up/wp-vite-plugins) into `dist/blocks-manifest.php`,
	 * consumed by `wp_register_block_types_from_metadata_collection()` (WP 6.7+)
	 * in one `include` instead of a per-block glob + JSON parse — the whole
	 * point of generating the manifest, which the previous glob-based version
	 * of this method left unused.
	 *
	 * Note the behavior change for blocks with a `markup.php` render callback:
	 * `wp_register_block_types_from_metadata_collection()` has no equivalent of
	 * the old manual `render_callback` closure below. Instead, declare
	 * `"render": "file:./markup.php"` directly in the block's `block.json` —
	 * `wpBlocks` copies that PHP next to the built block, and WordPress core
	 * wires it up natively via the metadata collection.
	 *
	 * @return void
	 */
	public function register_theme_blocks() {
		$manifest_path = TENUP_THEME_DIST_PATH . 'blocks-manifest.php';

		if ( ! file_exists( $manifest_path ) ) {
			return;
		}

		wp_register_block_types_from_metadata_collection(
			TENUP_THEME_DIST_PATH . 'blocks',
			$manifest_path
		);
	}

	/**
	 * Enqueue editor-only JavaScript/CSS for blocks.
	 *
	 * @return void
	 */
	public function blocks_editor_styles() {
		wp_enqueue_style(
			'editor-style-overrides',
			TENUP_THEME_TEMPLATE_URL . '/dist/css/editor-style-overrides.css',
			[],
			$this->get_asset_info( 'editor-style-overrides', 'version' )
		);
	}


	/**
	 * Enqueue block specific styles.
	 *
	 * This function is used to enqueue styles that are specific to a block. It
	 * first gets all the CSS files in the 'autoenqueue' directory. Then
	 * for each stylesheet, it determines the block type by removing the directory
	 * path and '.css' from the stylesheet path. It then tries to get the asset
	 * file for the block type. If the asset file doesn't exist, it creates a new
	 * one with the version set to the file modification time of the stylesheet
	 * and no dependencies. Finally, it enqueues the block style using the block
	 * type, the URL to the stylesheet, the path to the stylesheet, the version
	 * from the asset file, and the dependencies from the asset file.
	 *
	 * @return void
	 */
	public function enqueue_block_specific_styles() {
		$stylesheets = glob( TENUP_THEME_DIST_PATH . 'autoenqueue/**/*.css' );

		if ( empty( $stylesheets ) ) {
			return;
		}

		foreach ( $stylesheets as $stylesheet_path ) {
			$block_type = str_replace( TENUP_THEME_DIST_PATH . 'autoenqueue/', '', $stylesheet_path );
			$block_type = str_replace( '.css', '', $block_type );
			$asset_file = TENUP_THEME_DIST_PATH . 'autoenqueue/' . $block_type . '.asset.php';

			if ( file_exists( $asset_file ) ) {
				$asset_file = require $asset_file;
			} else {
				$asset_file = [
					'version'      => filemtime( $stylesheet_path ),
					'dependencies' => [],
				];
			}

			[$block_namespace, $block_name] = explode( '/', $block_type );

			wp_register_style(
				"tenup-theme-{$block_namespace}-{$block_name}",
				TENUP_THEME_DIST_URL . 'autoenqueue/' . $block_type . '.css',
				$asset_file['dependencies'],
				$asset_file['version']
			);

			wp_enqueue_block_style(
				$block_type,
				[
					'handle' => "tenup-theme-{$block_namespace}-{$block_name}",
					'path'   => $stylesheet_path,
				]
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
	public function register_block_pattern_categories() {
		// Register a block pattern category
		register_block_pattern_category(
			'10up-theme',
			[ 'label' => __( '10up Theme', 'tenup-theme' ) ]
		);
	}
}
