<?php
/**
 * Demo Post Type
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin\PostTypes;

/**
 * Demo post type.
 */
class Demo extends AbstractPostType {

	/**
	 * Get the post type name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'tenup-demo';
	}

	/**
	 * Get the singular post type label.
	 *
	 * @return string
	 */
	public function get_singular_label() {
		return esc_html__( 'Demo', 'tenup-plugin' );
	}

	/**
	 * Get the plural post type label.
	 *
	 * @return string
	 */
	public function get_plural_label() {
		return esc_html__( 'Demos', 'tenup-plugin' );
	}

	/**
	 * Can the class be registered?
	 *
	 * @return bool
	 */
	public function can_register() {
		return false;
	}

	/**
	 * Returns the default supported taxonomies. The subclass should declare the
	 * Taxonomies that it supports here if required.
	 *
	 * @return array
	 */
	public function get_supported_taxonomies() {
		return [
			'tenup-tax-demo',
		];
	}

	/**
	 * Run any code after the post type has been registered.
	 *
	 * @return void
	 */
	public function after_register() {
		// Register any hooks/filters you need.
	}
}
