<?php
/**
 * Assets
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin;

/**
 * The Assets object is the central place to manage shared Javascript & CSS
 * files. The assets are registered here. Other parts of the plugin can
 * use them by enqueuing or declaring as dependencies.
 */
class Assets {

	/**
	 * Registers the scripts & styles.
	 */
	public function register() {
		$this->register_scripts();
		$this->register_styles();
	}

	/**
	 * Dictates whether this class should be allowed to register or not. Essentially an on/off switch.
	 *
	 * @return bool
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Registers the javascript assets
	 */
	public function register_scripts() {
		$this->script(
			'tenup-plugin-admin',
			'dist/js/admin.js',
			[]
		);

		$this->script(
			'tenup-plugin-editor',
			'dist/js/editor.js',
			[]
		);
	}

	/**
	 * Registers the CSS assets
	 */
	public function register_styles() {
		$this->style(
			'tenup-plugin-admin',
			'dist/css/admin.css',
			[]
		);

		$this->style(
			'tenup-plugin-editor',
			'dist/css/editor.css',
			[]
		);
	}

	/**
	 * Registers a script with defaults to use plugin revision to bust
	 * cache automatically.
	 *
	 * @param string $name The script name.
	 * @param string $path The relative path of the script.
	 * @param array  $deps Optional dependency names.
	 * @param bool   $footer Whether to output the script in the footer.
	 * @param bool   $prefix_path Whethor to prefix the path to the script.
	 *
	 * @return void
	 */
	public function script( $name, $path, array $deps = [], bool $footer = true, bool $prefix_path = true ) {
		wp_register_script(
			$name,
			$this->asset_url( $path ),
			$deps,
			TENUP_PLUGIN_VERSION,
			$footer
		);
	}

	/**
	 * Registers a style with defaults to use plugin revision to bust
	 * cache automatically.
	 *
	 * @param string $name The style name.
	 * @param string $path The relative path of the style.
	 * @param array  $deps Optional dependency names.
	 * @param bool   $media Default 'all'.
	 */
	public function style( $name, $path, $deps = [], $media = 'all' ) {
		wp_register_style(
			$name,
			$this->asset_url( $path ),
			$deps,
			TENUP_PLUGIN_VERSION,
			$media
		);
	}

	/**
	 * Get the full assets url.
	 *
	 * @param string $path Assets url.
	 *
	 * @return string
	 */
	public function asset_url( $path ) {
		return trailingslashit( TENUP_PLUGIN_URL ) . $path;
	}
}
