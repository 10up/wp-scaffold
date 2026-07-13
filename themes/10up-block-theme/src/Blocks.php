<?php
/**
 * Gutenberg Blocks setup
 *
 * @package TenupBlockTheme
 */

namespace TenupBlockTheme;

use TenupFramework\Assets\GetAssetInfo;
use TenupFramework\Module;
use TenupFramework\ModuleInterface;

/**
 * Blocks module.
 *
 * @package TenupBlockTheme
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
			dist_path: TENUP_BLOCK_THEME_DIST_PATH,
			fallback_version: TENUP_BLOCK_THEME_VERSION
		);
		add_action( 'init', [ $this, 'register_theme_blocks' ], 10, 0 );
		add_action( 'init', [ $this, 'enqueue_theme_block_styles' ], 10, 0 );

		// Prevents third-party blocks from being suggested in the block inserter.
		remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );
	}


	/**
	 * Automatically registers all blocks from the generated Vite blocks manifest.
	 *
	 * Built by `wpBlocks` (@10up/wp-vite-plugins) into `dist/blocks-manifest.php`,
	 * consumed by `wp_register_block_types_from_metadata_collection()` (WP 6.7+)
	 * in one `include` instead of a per-block glob + JSON parse.
	 *
	 * @return void
	 */
	public function register_theme_blocks() {
		$manifest_path = TENUP_BLOCK_THEME_DIST_PATH . 'blocks-manifest.php';

		if ( ! file_exists( $manifest_path ) ) {
			return;
		}

		wp_register_block_types_from_metadata_collection(
			TENUP_BLOCK_THEME_DIST_PATH . 'blocks',
			$manifest_path
		);

		// The manifest itself (not the registration call) is the source of truth
		// for which block names got registered, so read it back for the filter below.
		$manifest    = require $manifest_path;
		$block_names = array_filter( array_column( $manifest, 'name' ) );

		if ( empty( $block_names ) ) {
			return;
		}

		add_filter(
			'allowed_block_types_all',
			function ( array|bool $allowed_blocks ) use ( $block_names ): array|bool {
				if ( ! is_array( $allowed_blocks ) ) {
					return $allowed_blocks;
				}
				return array_merge( $allowed_blocks, $block_names );
			}
		);
	}

	/**
	 * Enqueue block specific styles.
	 *
	 * Built by `wpBlockStyles` (@10up/wp-vite-plugins) into `dist/autoenqueue/`.
	 * These are CSS-only entries — `get_asset_info()` only probes `js/`, `css/`,
	 * and `blocks/` prefixes, never `autoenqueue/`, so calling it with an
	 * `autoenqueue/…` slug always silently falls through to the fallback
	 * version and never reflects real CSS edits. Read the `.asset.php`
	 * sidecar directly instead (falling back to the file's mtime if a given
	 * stylesheet has no sidecar at all — e.g. no `@wordpress/*` imports).
	 *
	 * @return void
	 */
	public function enqueue_theme_block_styles() {
		$stylesheets = glob( TENUP_BLOCK_THEME_DIST_PATH . 'autoenqueue/**/*.css' );

		if ( empty( $stylesheets ) ) {
			return;
		}

		foreach ( $stylesheets as $stylesheet_path ) {
			$block_type = str_replace( TENUP_BLOCK_THEME_DIST_PATH . 'autoenqueue/', '', $stylesheet_path );
			$block_type = str_replace( '.css', '', $block_type );
			$asset_file = TENUP_BLOCK_THEME_DIST_PATH . 'autoenqueue/' . $block_type . '.asset.php';

			if ( file_exists( $asset_file ) ) {
				$asset = require $asset_file;
			} else {
				$asset = [
					'version'      => (string) filemtime( $stylesheet_path ),
					'dependencies' => [],
				];
			}

			wp_register_style(
				"tenup-block-theme-{$block_type}",
				TENUP_BLOCK_THEME_DIST_URL . 'autoenqueue/' . $block_type . '.css',
				$asset['dependencies'],
				$asset['version'],
			);

			wp_enqueue_block_style(
				$block_type,
				[
					'handle' => "tenup-block-theme-{$block_type}",
					'path'   => $stylesheet_path,
				]
			);

			if ( file_exists( TENUP_BLOCK_THEME_DIST_PATH . 'autoenqueue/' . $block_type . '.js' ) ) {
				wp_enqueue_script(
					$block_type,
					TENUP_BLOCK_THEME_DIST_URL . 'autoenqueue/' . $block_type . '.js',
					$asset['dependencies'],
					$asset['version'],
					true
				);
			}
		}
	}
}
