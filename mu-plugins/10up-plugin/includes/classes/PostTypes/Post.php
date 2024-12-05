<?php
/**
 * Post Post type.
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin\PostTypes;

/**
 * Post Post type.
 *
 * This class is a placeholder for the core Post post type.
 * It's here to allow engineers to extend the core Post post type in the same way as custom post types.
 */
class Post extends AbstractCorePostType {

	/**
	 * Get the post type name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'post';
	}

	/**
	 * Returns the default supported taxonomies. The subclass should declare the
	 * Taxonomies that it supports here if required.
	 *
	 * Note: This will not remove the default taxonomies that are registered by core.
	 *
	 * @return array<string>
	 */
	public function get_supported_taxonomies() {
		return [];
	}

	/**
	 * Run any code after the post type has been registered.
	 *
	 * @return void
	 */
	public function after_register() {
		// Do nothing.
	}
}
