<?php
/**
 * Post Post type.
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin\PostTypes;

/**
 * Post Post type.
 */
class Post extends \TenUpPlugin\Module {

	/**
	 * Can the class be registered?
	 *
	 * @return bool
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Register hooks and filters.
	 *
	 * @return void
	 */
	public function register() {
		// Register any hooks/filters you need.
	}
}
