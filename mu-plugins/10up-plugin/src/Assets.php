<?php
/**
 * Assets module.
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin;

use TenupFramework\Assets\GetAssetInfo;
use TenupFramework\Module;
use TenupFramework\ModuleInterface;

/**
 * Assets module.
 *
 * @package TenUpPlugin
 */
class Assets implements ModuleInterface {

	use Module;
	use GetAssetInfo;

	/**
	 * Can this module be registered?
	 *
	 * @return bool
	 */
	public function can_register(): bool {
		return true;
	}

	/**
	 * Register any hooks and filters.
	 *
	 * @return void
	 */
	public function register(): void {
		$this->setup_asset_vars(
			dist_path: TENUP_PLUGIN_PATH . 'dist/',
			fallback_version: TENUP_PLUGIN_VERSION
		);

		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );
	}

	/**
	 * Enqueue scripts for admin.
	 *
	 * @return void
	 */
	public function admin_scripts(): void {
		wp_enqueue_script(
			'tenup_plugin_admin',
			TENUP_PLUGIN_URL . 'dist/js/admin.js',
			$this->get_asset_info( 'admin', 'dependencies' ),
			$this->get_asset_info( 'admin', 'version' ),
			true
		);
	}

	/**
	 * Enqueue styles for admin.
	 *
	 * @return void
	 */
	public function admin_styles(): void {
		wp_enqueue_style(
			'tenup_plugin_admin',
			TENUP_PLUGIN_URL . 'dist/css/admin.css',
			[],
			$this->get_asset_info( 'admin', 'version' ),
		);
	}
}
