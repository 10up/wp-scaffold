<?php
/**
 * PostTag
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin\Taxonomy;

/**
 * PostTag customizes the core WordPress category taxonomy for
 * TenUpPlugin.
 *
 * Usage:
 *
 * ```php
 *
 * $taxonomy = new PostTag();
 * $taxonomy->register();
 *
 * ```
 */
class PostTag extends AbstractTaxonomy {

	/**
	 * Get the taxonomy name constant.
	 *
	 * @return string
	 */
	public function get_name() {
		return CATEGORY_TAXONOMY;
	}

	/**
	 * Get the singular taxonomy label.
	 *
	 * @return string
	 */
	public function get_singular_label() {
		return esc_html__( 'PostTag', 'tenup-plugin' );
	}

	/**
	 * Get the plural taxonomy label.
	 *
	 * @return string
	 */
	public function get_plural_label() {
		return esc_html__( 'Categories', 'tenup-plugin' );
	}

	/**
	 * Overrides parent register since PostTag is a Core taxonomy.
	 */
	public function register() {
		// no-op -- override this
	}

}
