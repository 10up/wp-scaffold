<?php
/**
 * PostTypeFactory
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin\PostType;

/**
 * PostTypeFactory builds the TenUpPlugin post type class instances. Instances
 * are stored locally and returned from cache on subsequent build calls.
 *
 * All post types supported by TenUpPlugin are also declared here.
 *
 * Usage:
 *
 * ```php
 *
 * $factory = new PostTypeFactory();
 * $factory->build_all();
 *
 * $factory->build_if( FOO_POST_TYPE );
 *
 * ```
 */
class PostTypeFactory extends \TenUpPlugin\Module {

	/**
	 * Previously created post type instances.
	 *
	 * @var array
	 */
	public $post_types = [];

	/**
	 * Post Type to Class mapping.
	 *
	 * @var array
	 */
	public $post_type_mapping = [
		POST_POST_TYPE => 'Post',
		PAGE_POST_TYPE => 'Page',
	];

	/**
	 * Registers the post type.
	 *
	 * @return void
	 */
	public function register() {
		$this->build_all();
	}

	/**
	 * Checks if its possible to register a post type.
	 *
	 * @return bool
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Builds all supported post types. This is bound to the 'init' hook
	 * to allow both frontend and backend to get these post types.
	 */
	public function build_all() {
		foreach ( $this->get_supported_post_types() as $post_type ) {
			$this->build_if( $post_type );
		}
	}

	/**
	 * Conditionally builds a post type or returns the stored instance.
	 *
	 * @param  string $post_type The post type name.
	 * @return BasePostType A base post type subclass instance
	 */
	public function build_if( $post_type ) {
		if ( ! $this->exists( $post_type ) ) {
			$this->post_types[ $post_type ] = $this->build( $post_type );
			$instance                       = $this->post_types[ $post_type ];
			$instance->register();
		}

		return $this->post_types[ $post_type ];
	}

	/**
	 * Instantiates and returns a instance for the specified post type.
	 * An exception is thrown if an invalid post type name was specified.
	 *
	 * @param  string $post_type The post type name.
	 * @return BasePostType A base post type subclass instance.
	 * @throws \Exception An exception is thrown if an invalid post type name was specified.
	 */
	public function build( $post_type ) {
		if ( ! empty( $this->post_type_mapping[ $post_type ] ) ) {
			$class = $this->post_type_mapping[ $post_type ];

			/* If mapping is not fully qualified, qualify it now */
			if ( strpos( $class, 'TenUpPlugin' ) !== 0 ) {
				$class = 'TenUpPlugin\PostType\\' . $class;
			}

			$instance = new $class();

			return $instance;
		} else {
			throw new \Exception( "Mapping not found for Post Type: '$post_type' " );
		}
	}

	/**
	 * Checks if the post type specified was previously built.
	 *
	 * @param  string $post_type The post type name.
	 * @return bool True if the post type exists else false
	 */
	public function exists( $post_type ) {
		return ! empty( $this->post_types[ $post_type ] );
	}

	/**
	 * The list of supported post type instances for TenUpPlugin.
	 *
	 * @return array List of post type names
	 */
	public function get_supported_post_types() {
		return array_keys( $this->post_type_mapping );
	}
}
