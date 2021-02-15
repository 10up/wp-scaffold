<?php
/**
 * PostPostType
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin\PostType;

/**
 * Post PostType is used to override the core post type's features.
 */
class Post extends AbstractPostType {

	/**
	 * Get the post type name.
	 *
	 * @return string
	 */
	public function get_name() {
		return POST_POST_TYPE;
	}

	/**
	 * Get the singular post type label.
	 *
	 * @return string
	 */
	public function get_singular_label() {
		return esc_html__( 'Post', 'tenup-plugin' );
	}

	/**
	 * Get the plural post type label.
	 *
	 * @return string
	 */
	public function get_plural_label() {
		return esc_html__( 'Posts', 'tenup-plugin' );
	}

	/**
	 * Get the supported taxonomies.
	 *
	 * @return array
	 */
	public function get_supported_taxonomies() {
		return [
			CATEGORY_TAXONOMY,
			SERVICE_TYPE_TAXONOMY,
		];
	}

	/**
	 * Customizes the Post PostType.
	 */
	public function register_post_type() {
		// no-op -- override this
	}

}
