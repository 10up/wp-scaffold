<?php
/**
 * TaxonomyFactory
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin\Taxonomy;

/**
 * TaxonomyFactory builds the Taxonomy taxonomy class instances. Instances
 * are stored locally and returned from cache on subsequent build calls.
 *
 * All taxonomies supported by TenUpPlugin are also declared here.
 *
 * Usage:
 *
 * ```php
 *
 * $factory = new TaxonomyFactory();
 * $factory->build_all();
 *
 * ```
 */
class TaxonomyFactory extends \TenUpPlugin\Module {

	/**
	 * Taxonomy to Class mapping.
	 *
	 * @var array
	 */
	public $mapping = [
		CATEGORY_TAXONOMY       => 'Category',
		POST_TAG_TAXONOMY       => 'PostTag',
	];

	/**
	 * Previously created taxonomies instances.
	 *
	 * @var array
	 */
	public $taxonomies = [];

	/**
	 * Registers the taxonomy.
	 *
	 * @return void
	 */
	public function register() {
		$this->build_all();
	}

	/**
	 * Checks if its possible to register a taxonomy.
	 *
	 * @return bool
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Builds all supported taxonomies. This is bound to the 'init' hook
	 * to allow both frontend and backend to get these taxonomies.
	 */
	public function build_all() {
		foreach ( $this->get_supported_taxonomies() as $taxonomy ) {
			$this->build_if( $taxonomy );
		}
	}

	/**
	 * Conditionally builds a taxonomy or returns the stored instance.
	 *
	 * @param string $taxonomy The taxonomy name.
	 * @return BaseTaxonomy A base taxonomy subclass instance
	 */
	public function build_if( $taxonomy ) {
		if ( ! $this->exists( $taxonomy ) ) {
			$this->taxonomies[ $taxonomy ] = $this->build( $taxonomy );
			$instance                      = $this->taxonomies[ $taxonomy ];
			$instance->register();
		}

		return $this->taxonomies[ $taxonomy ];
	}

	/**
	 * Instantiates and returns a instance for the specified taxonomy.
	 * An exception is thrown if an invalid taxonomy name was specified.
	 *
	 * @param string $taxonomy The taxonomy name.
	 * @return \Taxonomy\Taxonomy\BaseTaxonomy A base taxonomy subclass instance
	 * @throws \Exception Invalid taxonomy name was specified.
	 */
	public function build( $taxonomy ) {
		if ( ! empty( $this->mapping[ $taxonomy ] ) ) {
			$class = $this->mapping[ $taxonomy ];

			/* If mapping is not fully qualified, qualify it now */
			if ( strpos( $class, 'Taxonomy' ) !== 0 ) {
				$class = 'TenUpPlugin\Taxonomy\\' . $class;
			}

			$instance = new $class();

			return $instance;
		} else {
			throw new \Exception( "Mapping not found for Taxonomy: $taxonomy " );
		}
	}

	/**
	 * Checks if the taxonomy specified was previously built.
	 *
	 * @param string $taxonomy The taxonomy name.
	 * @return bool True if the taxonomy exists else false.
	 */
	public function exists( $taxonomy ) {
		return ! empty( $this->taxonomies[ $taxonomy ] );
	}

	/**
	 * The list of supported taxonomy instances for Taxonomy.
	 *
	 * @return array List of taxonomy names.
	 */
	public function get_supported_taxonomies() {
		return array_keys( $this->mapping );
	}
}
