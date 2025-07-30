<?php
/**
 * Assets module.
 *
 * @package TenUpTheme
 */

namespace TenUpTheme;

use TenupFramework\Assets\GetAssetInfo;
use TenupFramework\Module;
use TenupFramework\ModuleInterface;

/**
 * Assets module.
 *
 * @package TenUpTheme
 */
class Assets implements ModuleInterface {

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
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'styles' ] );
	}


	/**
	 * Enqueue scripts for front-end.
	 *
	 * @return void
	 */
	public function scripts() {
		/**
		 * Enqueuing frontend.js is required to get css hot reloading working in the frontend
		 * If you're not shipping any front-end js wrap this enqueue in a SCRIPT_DEBUG check.
		 */
		wp_enqueue_script(
			'frontend',
			TENUP_THEME_TEMPLATE_URL . '/dist/js/frontend.js',
			$this->get_asset_info( 'frontend', 'dependencies' ),
			$this->get_asset_info( 'frontend', 'version' ),
			true
		);
	}

	/**
	 * Enqueue core block filters, styles and variations.
	 *
	 * @return void
	 */
	public function enqueue_block_editor_scripts() {
		wp_enqueue_script(
			'block-editor-script',
			TENUP_THEME_DIST_URL . 'js/block-editor-script.js',
			$this->get_asset_info( 'block-editor-script', 'dependencies' ),
			$this->get_asset_info( 'block-editor-script', 'version' ),
			true
		);
	}

	/**
	 * Enqueue styles for front-end.
	 *
	 * @return void
	 */
	public function styles() {
		wp_enqueue_style(
			'styles',
			TENUP_THEME_TEMPLATE_URL . '/dist/css/frontend.css',
			[],
			$this->get_asset_info( 'frontend', 'version' )
		);
	}
}
