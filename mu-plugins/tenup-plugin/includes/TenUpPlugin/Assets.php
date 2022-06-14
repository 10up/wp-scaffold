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
class Assets extends Module {

	/**
	 * Registers the scripts & styles.
	 */
	public function register() {
		// Hook to allow async or defer on asset loading.
		add_filter( 'script_loader_tag', [ $this, 'script_loader_tag' ], 10, 2 );

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

	/**
	 * Add async/defer attributes to enqueued scripts that have the specified script_execution flag.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12009
	 * @param string $tag    The script tag.
	 * @param string $handle The script handle.
	 * @return string
	 */
	function script_loader_tag( $tag, $handle ) {
		$script_execution = wp_scripts()->get_data( $handle, 'script_execution' );

		if ( ! $script_execution ) {
			return $tag;
		}

		if ( 'async' !== $script_execution && 'defer' !== $script_execution ) {
			return $tag; // _doing_it_wrong()?
		}

		// Abort adding async/defer for scripts that have this script as a dependency. _doing_it_wrong()?
		foreach ( wp_scripts()->registered as $script ) {
			if ( in_array( $handle, $script->deps, true ) ) {
				return $tag;
			}
		}

		// Add the attribute if it hasn't already been added.
		if ( ! preg_match( ":\s$script_execution(=|>|\s):", $tag ) ) {
			$tag = preg_replace( ':(?=></script>):', " $script_execution", $tag, 1 );
		}

		return $tag;
	}

}