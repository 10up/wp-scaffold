<?php
/**
 * Category
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin\Taxonomy;

/**
 * Category customizes the core WordPress category taxonomy for
 * TenUpPlugin.
 *
 * Usage:
 *
 * ```php
 *
 * $taxonomy = new Category();
 * $taxonomy->register();
 *
 * ```
 */
class Category extends AbstractTaxonomy {

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
		return esc_html__( 'Category', 'groundworks' );
	}

	/**
	 * Get the plural taxonomy label.
	 *
	 * @return string
	 */
	public function get_plural_label() {
		return esc_html__( 'Categories', 'groundworks' );
	}

	/**
	 * Overrides parent register since Category is a Core taxonomy.
	 */
	public function register() {
		// no-op -- override this
	}

}
