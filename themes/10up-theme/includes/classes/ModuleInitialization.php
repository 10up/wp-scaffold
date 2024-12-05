<?php
/**
 * Auto-initialize all Module based classes in the theme.
 *
 * @package TenUpTheme
 */

namespace TenUpTheme;

use ReflectionClass;
use Spatie\StructureDiscoverer\Cache\FileDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\Discover;

/**
 * ModuleInitialization class.
 *
 * @package TenUpTheme
 */
class ModuleInitialization {

	/**
	 * The class instance.
	 *
	 * @var null|ModuleInitialization
	 */
	private static $instance = null;

	/**
	 * Get the instance of the class.
	 *
	 * @return ModuleInitialization
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Override the constructor, we don't want to init it that way.
	 */
	private function __construct() {
		// no-op. This class is a singleton.
	}

	/**
	 * The list of initialized classes.
	 *
	 * @var array<\TenUpTheme\Module>
	 */
	protected $classes = [];

	/**
	 * Get all the TenUpTheme plugin classes.
	 *
	 * @return array<string>
	 */
	protected function get_classes() {
		// Get all classes from this directory and its subdirectories.
		$class_finder = Discover::in( __DIR__ );
		// Only fetch classes.
		$class_finder->classes();
		// Only fetch classes in the current namespace.
		$class_finder->custom(
			fn( DiscoveredStructure $structure ) => str_starts_with( $structure->namespace, __NAMESPACE__ )
		);

		// If we are in production or staging, cache the class loader to improve performance.
		if ( in_array( wp_get_environment_type(), [ 'production', 'staging' ], true ) ) {
			$class_finder->withCache(
				__NAMESPACE__,
				new FileDiscoverCacheDriver( __DIR__ . '/class-loader-cache' )
			);
		}

		$classes = array_filter( $class_finder->get(), fn( $cl ) => is_string( $cl ) );

		// Return the classes
		return $classes;
	}

	/**
	 * Initialize all the TenUpTheme plugin classes.
	 *
	 * @return void
	 */
	public function init_classes() {
		$load_class_order = [];

		foreach ( $this->get_classes() as $class ) {
			// Create a slug for the class name.
			$slug = $this->slugify_class_name( $class );

			// If the class has already been initialized, skip it.
			if ( isset( $this->classes[ $slug ] ) ) {
				continue;
			}

			// Create a new reflection of the class.
			// @phpstan-ignore argument.type
			$reflection_class = new ReflectionClass( $class );

			// Using reflection, check if the class can be initialized.
			// If not, skip.
			if ( ! $reflection_class->isInstantiable() ) {
				continue;
			}

			// Make sure the class is a subclass of Module, so we can initialize it.
			if ( ! $reflection_class->isSubclassOf( sprintf( '\%s\Module', __NAMESPACE__ ) ) ) {
				continue;
			}

			// Initialize the class.
			$instantiated_class = new $class();

			if ( ! $instantiated_class instanceof Module ) {
				continue;
			}

			// Assign the classes into the order they should be initialized.
			$load_class_order[ intval( $instantiated_class->load_order ) ][] = [
				'slug'  => $slug,
				'class' => $instantiated_class,
			];
		}

		// Sort the initialized classes by load order.
		ksort( $load_class_order );

		// Loop through the classes and initialize them.
		foreach ( $load_class_order as $class_objects ) {
			foreach ( $class_objects as $class_object ) {
				$class = $class_object['class'];
				$slug  = $class_object['slug'];

				// If the class can be registered, register it.
				if ( $class->can_register() ) {
					// Call its register method.
					$class->register();
					// Store the class in the list of initialized classes.
					$this->classes[ $slug ] = $class;
				}
			}
		}
	}

	/**
	 * Slugify a class name.
	 *
	 * @param string $class_name The class name.
	 *
	 * @return string
	 */
	protected function slugify_class_name( $class_name ) {
		return sanitize_title( str_replace( '\\', '-', $class_name ) );
	}

	/**
	 * Get a class by its full class name, including namespace.
	 *
	 * @param string $class_name The class name & namespace.
	 *
	 * @return false|\TenUpTheme\Module
	 */
	public function get_class( $class_name ) {
		$class_name = $this->slugify_class_name( $class_name );

		if ( isset( $this->classes[ $class_name ] ) ) {
			return $this->classes[ $class_name ];
		}

		return false;
	}

	/**
	 * Get all the initialized classes.
	 *
	 * @return array<\TenUpTheme\Module>
	 */
	public function get_all_classes() {
		return $this->classes;
	}
}
