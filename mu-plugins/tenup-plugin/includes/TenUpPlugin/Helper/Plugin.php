<?php
/**
 * Plugin.php
 *
 * Helpers common to the mu-plugin.
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin;

/**
 * Returns the singleton instance of the main Plugin class.
 *
 * @return Plugin
 */
function get_plugin() {
	return Plugin::get_instance();
}

/**
 * Returns the instance of the plugin module if present.
 *
 * @param string $name The name of the module.
 * @return stdObject|false
 */
function get_plugin_support( $name ) {
	return get_plugin()->get_plugin_support( $name );
}
