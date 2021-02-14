<?php
/**
 * Plugin
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin;

/**
 * The main Plugin class. All subclasses/modules
 * are managed from within this class. This class is used as a singleton
 * and should not be instantiated directly.
 *
 * Usage:
 *
 * ```php
 *
 * $plugin = Plugin::get_instance();
 *
 * ```
 */
class Plugin {

	/**
	 * Singleton instance of the Plugin.
	 *
	 * @var \TenUpPlugin\Plugin
	 */
	public static $instance = null;

	/**
	 * Conditionally creates the singleton instance if absent, else
	 * returns the previously saved instance.
	 *
	 * @return Plugin The singleton instance
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Plugin();
		}

		return self::$instance;
	}

	/**
	 * Plugin support objects.
	 *
	 * @var array
	 */
	public $plugin_support = [];

	/**
	 * Starts the plugin by subscribing to the WordPress lifecycle
	 * hooks. Sets up the WP CLI commands if running in CLI mode.
	 *
	 * @return void
	 */
	public function enable() {
		add_action( 'init', [ $this, 'init' ], 11 );
		add_action( 'admin_init', [ $this, 'init_admin' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'init_admin_scripts' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'init_editor_scripts' ] );

		if ( $this->is_wp_cli() ) {
			$this->init_commands();
		}
	}

	/**
	 * Register all of the subclasses/modules related to the admin
	 * and public-facing functionality of the plugin.
	 *
	 * @return void
	 */
	public function init() {
		$this->plugin_support = [
			// todo
		];

		$this->register_objects( $this->plugin_support );
	}

	/**
	 * Register all of the hooks related to the admin functionality
	 * of the plugin.
	 *
	 * @return void
	 */
	public function init_admin() {
	}

	/**
	 * Enqueue scripts for the admin pages.
	 *
	 * @return void
	 */
	public function init_admin_scripts() {
	}

	/**
	 * Enqueue scripts for the Gutenberg editor. This fires on the block
	 * enqueue hence safe to assume that Gutenberg is active.
	 *
	 * @return void
	 */
	public function init_editor_scripts() {
	}

	/**
	 * Sets up the WP CLI commands.
	 *
	 * @return void
	 */
	public function init_commands() {

	}

	/**
	 * Registers the support objects.
	 *
	 * @param array $objects An array of support objects.
	 *
	 * @return void
	 */
	public function register_objects( $objects ) {
		foreach ( $objects as $object ) {
			$this->register( $object );
		}
	}

	/**
	 * Registers a support object.
	 *
	 * @param object $object A support object.
	 *
	 * @return void
	 */
	public function register( $object ) {
		if ( $object->can_register() ) {

			if ( property_exists( $object, 'container' ) ) {
				$object->container = $this;
			}

			$object->register();
		}
	}

	/**
	 * Checks if running in WP CLI mode.
	 *
	 * @return bool True if in CLI mode else false.
	 */
	public function is_wp_cli() {
		return defined( 'WP_CLI' ) && WP_CLI;
	}

	/**
	 * Fetches the plugin support object by name.
	 *
	 * @param string $name The name of the plugin support object.
	 *
	 * @return bool
	 */
	public function get_plugin_support( $name ) {
		if ( array_key_exists( $name, $this->plugin_support ) ) {
			return $this->plugin_support[ $name ];
		}

		return false;
	}

}
