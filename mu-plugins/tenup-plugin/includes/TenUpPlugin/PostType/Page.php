<?php
/**
 * Page
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin\PostType;

/**
 * Page Post Type customizes the Core Page Post Type.
 */
class Page extends AbstractPostType {

	/**
	 * Get the post type name.
	 *
	 * @return string
	 */
	public function get_name() {
		return PAGE_POST_TYPE;
	}

	/**
	 * Get the singular post type label.
	 *
	 * @return string
	 */
	public function get_singular_label() {
		return esc_html__( 'Page', 'tenup-plugin' );
	}

	/**
	 * Get the plural post type label.
	 *
	 * @return string
	 */
	public function get_plural_label() {
		return esc_html__( 'Pages', 'tenup-plugin' );
	}

	/**
	 * Don't re-register the 'page' post_type
	 */
	public function register_post_type() {
		// no-op -- override this
	}
}
