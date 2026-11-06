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

		add_filter( 'block_editor_settings_all', [ $this, 'curate_editor_settings' ] );
		add_action( 'admin_menu', [ $this, 'remove_font_library_screen' ] );
	}

	/**
	 * Turns off editor features that pull in content from outside the design system.
	 *
	 * @param array<string, mixed> $settings Block editor settings.
	 * @return array<string, mixed>
	 */
	public function curate_editor_settings( $settings ) {
		// Openverse images are third-party media with their own licensing; editors use the media library.
		$settings['enableOpenverseMediaCategory'] = false;

		// Typography comes from theme.json; editors shouldn't install fonts through the Font Library.
		// It stays on locally, where developers try out fonts.
		if ( 'local' !== wp_get_environment_type() ) {
			$settings['fontLibraryEnabled'] = false;
		}

		return $settings;
	}

	/**
	 * Removes the Appearance > Fonts screen (WP 7.0+), the Font Library outside the Site Editor.
	 *
	 * @return void
	 */
	public function remove_font_library_screen() {
		// It stays on locally, where developers try out fonts.
		if ( 'local' === wp_get_environment_type() ) {
			return;
		}

		remove_submenu_page( 'themes.php', 'font-library.php' );
	}


	/**
	 * Registers every block the build put in dist/blocks, and allows them in the inserter.
	 *
	 * The build writes `dist/blocks-manifest.php` with every block's metadata,
	 * so WordPress 6.7+ registers them all from one include instead of reading
	 * and parsing each block.json on every request.
	 *
	 * @return void
	 */
	public function register_theme_blocks() {
		$manifest    = TENUP_BLOCK_THEME_DIST_PATH . 'blocks-manifest.php';
		$block_names = [];

		if ( file_exists( $manifest ) && function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
			wp_register_block_types_from_metadata_collection( TENUP_BLOCK_THEME_BLOCK_DIST_DIR, $manifest );

			$metadata    = require $manifest;
			$block_names = is_array( $metadata ) ? array_values( array_filter( array_column( $metadata, 'name' ) ) ) : [];
		} else {
			// Before WordPress 6.7: register each block from its block.json.
			$block_json_files = glob( TENUP_BLOCK_THEME_BLOCK_DIST_DIR . '*/block.json' );
			$block_json_files = false === $block_json_files ? [] : $block_json_files;

			foreach ( $block_json_files as $filename ) {
				$block = register_block_type_from_metadata( dirname( $filename ) );

				if ( $block ) {
					$block_names[] = $block->name;
				}
			}
		}

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
			$asset      = file_exists( $asset_file )
				? require $asset_file
				: [
					'version'      => filemtime( $stylesheet_path ),
					'dependencies' => [],
				];

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
		}
	}
}
