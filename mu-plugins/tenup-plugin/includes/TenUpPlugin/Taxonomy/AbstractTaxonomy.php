<?php
/**
 * AbstractTaxonomy
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin\Taxonomy;

/**
 * Abstract Base Class for TenUpPlugin Taxonomies. A Taxonomy should
 * declare a constant name in the config.php file.
 *
 * Usage:
 *
 * class FooTaxonomy extends AbstractTaxonomy {
 *
 *     public function get_name() {
 *         return FOO_TAXONOMY;
 *     }
 *
 *     public function get_singular_label() {
 *         return 'Tag'
 *     }
 *
 *     public function get_plural_label() {
 *         return 'Tags';
 *     }
 * }
 *
 * Then add it to the Taxonomy Factory. And add as a supported Taxonomy
 * on the corresponding post types.
 */
abstract class AbstractTaxonomy {

	/**
	 * Get the taxonomy name constant.
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Get the singular taxonomy label.
	 *
	 * @return string
	 */
	abstract public function get_singular_label();

	/**
	 * Get the plural taxonomy label.
	 *
	 * @return string
	 */
	abstract public function get_plural_label();

	/**
	 * Returns whether the current theme supports the current taxonomy.
	 *
	 * @return bool
	 */
	public function has_theme_support() {
		$supports = get_theme_support( 'taxonomy_' . $this->get_name() );

		return $supports;
	}

	/**
	 * Register hooks and actions.
	 *
	 * To add support for a taxonomy `gw_example` to a theme,
	 * `add_theme_supports( 'taxonomy_gw_example' );`
	 *
	 * @uses $this->get_name() to get the taxonomy's slug.
	 * @return bool
	 */
	public function register() {
		$supports = $this->has_theme_support();

		/**
		 * Filters the theme support of a taxonomy before using it.
		 *
		 * To add support for a taxonomy `example` to a theme,
		 * `add_theme_supports( 'taxonomy_example' );`
		 *
		 * @param bool $supports Whether the current theme supports this taxonomy.
		 */
		$supports = apply_filters( 'tenup_plugin_taxonomy_has_theme_support', $supports, $this->get_name() );

		if ( ! $supports ) {
			return false;
		}

		/**
		 * Allow plugins/themes to update options for a taxonomy.
		 *
		 * @param array  $options  Default taxonomy options.
		 * @param string $name Taxonomy name.
		 */
		$options = apply_filters(
			'tenup_plugin_taxonomy_options',
			$this->get_options(),
			$this->get_name()
		);

		\register_taxonomy(
			$this->get_name(),
			$this->get_post_types(),
			$options
		);

		return true;
	}

	/**
	 * Get the options for the taxonomy.
	 *
	 * @return array
	 */
	public function get_options() {
		return array(
			'labels'            => $this->get_labels(),
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'public'            => true,
		);
	}

	/**
	 * Get the labels for the taxonomy.
	 *
	 * @return array
	 */
	public function get_labels() {
		$plural_label   = $this->get_plural_label();
		$singular_label = $this->get_singular_label();

		// phpcs:disable
		$labels = array(
			'name'                       => $plural_label, // Already translated via get_plural_label().
			'singular_name'              => $singular_label, // Already translated via get_singular_label().
			'search_items'               => sprintf( __( 'Search %s', 'groundworks' ), $plural_label ),
			'popular_items'              => sprintf( __( 'Popular %s', 'groundworks' ), $plural_label ),
			'all_items'                  => sprintf( __( 'All %s', 'groundworks' ), $plural_label ),
			'edit_item'                  => sprintf( __( 'Edit %s', 'groundworks' ), $singular_label ),
			'update_item'                => sprintf( __( 'Update %s', 'groundworks' ), $singular_label ),
			'add_new_item'               => sprintf( __( 'Add New %s', 'groundworks' ), $singular_label ),
			'new_item_name'              => sprintf( __( 'New %s Name', 'groundworks' ), $singular_label ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'groundworks' ), strtolower( $plural_label ) ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'groundworks' ), strtolower( $plural_label ) ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', 'groundworks' ), strtolower( $plural_label ) ),
			'not_found'                  => sprintf( __( 'No %s found.', 'groundworks' ), strtolower( $plural_label ) ),
			'not_found_in_trash'         => sprintf( __( 'No %s found in Trash.', 'groundworks' ), strtolower( $plural_label ) ),
			'view_item'                  => sprintf( __( 'View %s', 'groundworks' ), $singular_label ),
		);
		// phpcs:enable

		return $labels;
	}

	/**
	 * Setting the post types to null to ensure no post type is registered with
	 * this taxonomy. Post Type classes declare their supported taxonomies.
	 */
	public function get_post_types() {
		return null;
	}
}
