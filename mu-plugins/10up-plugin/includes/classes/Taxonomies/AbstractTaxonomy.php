<?php
/**
 * AbstractTaxonomy
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin\Taxonomies;

use TenUpPlugin\Module;

/**
 * Abstract Base Class for Taxonomies.
 *
 * Usage:
 *
 * class FooTaxonomy extends AbstractTaxonomy {
 *
 *     public function get_name() {
 *         return 'tag';
 *     }
 *
 *     public function get_singular_label() {
 *         return 'Tag'
 *     }
 *
 *     public function get_plural_label() {
 *         return 'Tags';
 *     }
 *
 *     public function can_register() {
 *         return true;
 *     }
 * }
 */
abstract class AbstractTaxonomy extends Module {

	/**
	 * Used to alter the order in which clases are initialized.
	 *
	 * Lower number will be initialized first.
	 *
	 * @var int The priority of the module.
	 */
	public $load_order = 9;

	/**
	 * Get the taxonomy name.
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
	 * Is the taxonomy hierarchical?
	 *
	 * @return bool
	 */
	public function is_hierarchical() {
		return false;
	}

	/**
	 * Register hooks and actions.
	 *
	 * @uses $this->get_name() to get the taxonomy's slug.
	 * @return bool
	 */
	public function register() {
		\register_taxonomy(
			$this->get_name(),
			$this->get_post_types(),
			$this->get_options()
		);

		$this->after_register();

		return true;
	}

	/**
	 * Get the options for the taxonomy.
	 *
	 * @return array{
	 *       labels?: array<string, string>,
	 *       description?: string,
	 *       public?: bool,
	 *       publicly_queryable?: bool,
	 *       hierarchical?: bool,
	 *       show_ui?: bool,
	 *       show_in_menu?: bool,
	 *       show_in_nav_menus?: bool,
	 *       show_tagcloud?: bool,
	 *       show_in_quick_edit?: bool,
	 *       show_admin_column?: bool,
	 *       meta_box_cb?: bool|callable,
	 *       show_in_rest?: bool,
	 *       rest_base?: string,
	 *       rest_namespace?: string,
	 *       rest_controller_class?: string,
	 *       capabilities?: array<string, string>,
	 *       rewrite?: bool|array{
	 *           slug?: string,
	 *           with_front?: bool,
	 *           hierarchical?: bool,
	 *           ep_mask?: int,
	 *       },
	 *       query_var?: string|bool,
	 *       update_count_callback?: callable,
	 *       default_term?: string|array{
	 *           name: string,
	 *           slug?: string,
	 *           description?: string,
	 *       },
	 *       sort?: bool,
	 *       _builtin?: bool,
	 *  }
	 */
	public function get_options() {
		return [
			'labels'            => $this->get_labels(),
			'hierarchical'      => $this->is_hierarchical(),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'public'            => true,
		];
	}

	/**
	 * Get the labels for the taxonomy.
	 *
	 * @return array<string, string>
	 */
	public function get_labels() {
		$plural_label   = $this->get_plural_label();
		$singular_label = $this->get_singular_label();

		// phpcs:disable
		$labels = [
			'name'                       => $plural_label, // Already translated via get_plural_label().
			'singular_name'              => $singular_label, // Already translated via get_singular_label().
			'search_items'               => sprintf( __( 'Search %s', 'tenup-plugin' ), $plural_label ),
			'popular_items'              => sprintf( __( 'Popular %s', 'tenup-plugin' ), $plural_label ),
			'all_items'                  => sprintf( __( 'All %s', 'tenup-plugin' ), $plural_label ),
			'edit_item'                  => sprintf( __( 'Edit %s', 'tenup-plugin' ), $singular_label ),
			'update_item'                => sprintf( __( 'Update %s', 'tenup-plugin' ), $singular_label ),
			'add_new_item'               => sprintf( __( 'Add %s', 'tenup-plugin' ), $singular_label ),
			'new_item_name'              => sprintf( __( 'New %s Name', 'tenup-plugin' ), $singular_label ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'tenup-plugin' ), strtolower( $plural_label ) ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'tenup-plugin' ), strtolower( $plural_label ) ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', 'tenup-plugin' ), strtolower( $plural_label ) ),
			'not_found'                  => sprintf( __( 'No %s found.', 'tenup-plugin' ), strtolower( $plural_label ) ),
			'not_found_in_trash'         => sprintf( __( 'No %s found in Trash.', 'tenup-plugin' ), strtolower( $plural_label ) ),
			'view_item'                  => sprintf( __( 'View %s', 'tenup-plugin' ), $singular_label ),
		];
		// phpcs:enable

		return $labels;
	}

	/**
	 * Setting the post types to null to ensure no post type is registered with
	 * this taxonomy. Post Type classes declare their supported taxonomies.
	 *
	 * @return array<string>
	 */
	public function get_post_types() {
		return [];
	}

	/**
	 * Run any code after the taxonomy has been registered.
	 *
	 * @return void
	 */
	public function after_register() {
		// Do nothing.
	}
}
