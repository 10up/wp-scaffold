<?php
/**
 * AbstractPostType
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin\PostType;

/**
 * Abstract class for post types.
 */
abstract class AbstractPostType {

	/**
	 * Get the post type name.
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Get the singular post type label.
	 *
	 * @return string
	 */
	abstract public function get_singular_label();

	/**
	 * Get the plural post type label.
	 *
	 * @return string
	 */
	abstract public function get_plural_label();

	/**
	 * Default post type supported feature names.
	 *
	 * @return array
	 */
	public function get_editor_supports() {
		$supports = [
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
		];

		return $supports;
	}

	/**
	 * Returns whether the current theme supports the current post type.
	 *
	 * @return bool
	 */
	public function has_theme_support() {
		$supports = get_theme_support( 'post_type_' . $this->get_name() );

		return $supports;
	}

	/**
	 * Get the options for the post type.
	 *
	 * @return array
	 */
	public function get_options() {
		/**
		 * Allows plugins / themes to override the post type supports of a CPT.
		 *
		 * @param array  $supports Default supports array.
		 * @param string $name Post Type name.
		 */
		$supports = apply_filters( 'tenup_plugin_post_type_supports', $this->get_editor_supports(), $this->get_name() );

		$options = [
			'labels'            => $this->get_labels(),
			'public'            => true,
			'has_archive'       => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => false,
			'supports'          => $supports,
		];

		return $options;
	}

	/**
	 * Get the labels for the post type.
	 *
	 * @return array
	 */
	public function get_labels() {
		$plural_label   = $this->get_plural_label();
		$singular_label = $this->get_singular_label();

		// phpcs:disable -- ignoring template strings without translators placeholder since this is dynamic
		$labels = array(
			'name'                     => $plural_label,
			// Already translated via get_plural_label().
			'singular_name'            => $singular_label,
			// Already translated via get_singular_label().
			'add_new_item'             => sprintf( __( 'Add New %s', 'tenup-plugin' ), $singular_label ),
			'edit_item'                => sprintf( __( 'Edit %s', 'tenup-plugin' ), $singular_label ),
			'new_item'                 => sprintf( __( 'New %s', 'tenup-plugin' ), $singular_label ),
			'view_item'                => sprintf( __( 'View %s', 'tenup-plugin' ), $singular_label ),
			'view_items'               => sprintf( __( 'View %s', 'tenup-plugin' ), $plural_label ),
			'search_items'             => sprintf( __( 'Search %s', 'tenup-plugin' ), $plural_label ),
			'not_found'                => sprintf( __( 'No %s found.', 'tenup-plugin' ), strtolower( $plural_label ) ),
			'not_found_in_trash'       => sprintf( __( 'No %s found in Trash.', 'tenup-plugin' ), strtolower( $plural_label ) ),
			'parent_item_colon'        => sprintf( __( 'Parent %s:', 'tenup-plugin' ), $plural_label ),
			'all_items'                => sprintf( __( 'All %s', 'tenup-plugin' ), $plural_label ),
			'archives'                 => sprintf( __( '%s Archives', 'tenup-plugin' ), $singular_label ),
			'attributes'               => sprintf( __( '%s Attributes', 'tenup-plugin' ), $singular_label ),
			'insert_into_item'         => sprintf( __( 'Insert into %s', 'tenup-plugin' ), strtolower( $singular_label ) ),
			'uploaded_to_this_item'    => sprintf( __( 'Uploaded to this %s', 'tenup-plugin' ), strtolower( $singular_label ) ),
			'filter_items_list'        => sprintf( __( 'Filter %s list', 'tenup-plugin' ), strtolower( $plural_label ) ),
			'items_list_navigation'    => sprintf( __( '%s list navigation', 'tenup-plugin' ), $plural_label ),
			'items_list'               => sprintf( __( '%s list', 'tenup-plugin' ), $plural_label ),
			'item_published'           => sprintf( __( '%s published.', 'tenup-plugin' ), $singular_label ),
			'item_published_privately' => sprintf( __( '%s published privately.', 'tenup-plugin' ), $singular_label ),
			'item_reverted_to_draft'   => sprintf( __( '%s reverted to draft.', 'tenup-plugin' ), $singular_label ),
			'item_scheduled'           => sprintf( __( '%s scheduled.', 'tenup-plugin' ), $singular_label ),
			'item_updated'             => sprintf( __( '%s updated.', 'tenup-plugin' ), $singular_label ),
			'menu_name'                => $plural_label,
			'name_admin_bar'           => $singular_label,
		);
		// phpcs:enable

		return $labels;
	}

	/**
	 * Registers a post type and associates its taxonomies.
	 *
	 * @uses $this->get_name() to get the post's type name.
	 * @return Bool Whether this theme has supports for this post type.
	 */
	public function register() {
		$supports = $this->has_theme_support();

		/**
		 * Filters the theme support of a post type before using it.
		 *
		 * To add support for a post type `example` to a theme,
		 * `add_theme_supports( 'post_type_example' );`
		 *
		 * @param bool $supports Whether the current theme supports this post type.
		 */
		$supports = apply_filters( 'tenup_plugin_post_type_has_theme_support', $supports, $this->get_name() );

		if ( $supports ) {
			$this->register_post_type();
			$this->register_taxonomies();
		}

		return $supports;
	}

	/**
	 * Registers the current post type with WordPress.
	 */
	public function register_post_type() {
		/**
		 * Allow plugins/themes to update options for a post type.
		 *
		 * @param array  $options  Default post type options.
		 * @param string $name Post type name.
		 */
		$options = apply_filters(
			'tenup_plugin_post_type_options',
			$this->get_options(),
			$this->get_name()
		);

		register_post_type(
			$this->get_name(),
			$options
		);
	}

	/**
	 * Registers the taxonomies declared with the current post type.
	 */
	public function register_taxonomies() {
		$taxonomies  = $this->get_supported_taxonomies();

		/**
		 * Filters the post types supported taxonomies before using it.
		 *
		 * @param array  $taxonomies The default supported taxonomies for this post type.
		 * @param string $name The post type name.
		 */
		$taxonomies = apply_filters( 'tenup_plugin_post_type_taxonomies', $taxonomies, $this->get_name() );

		$object_type = $this->get_name();

		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				register_taxonomy_for_object_type(
					$taxonomy,
					$object_type
				);
			}
		}
	}

	/**
	 * Returns the default supported taxonomies. The subclass should declare the
	 * Taxonomies that it supports here if required.
	 *
	 * @return array
	 */
	public function get_supported_taxonomies() {
		return [];
	}

}
