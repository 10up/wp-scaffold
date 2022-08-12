<?php
/**
 * Auto-initialize all Module based clases in the plugin.
 *
 * @package TenUpPlugin
 */

namespace TenUpPlugin;

use HaydenPierce\ClassFinder\ClassFinder;
use ReflectionClass;

/**
 * ModuleInitialization class.
 *
 * @package TenUpPlugin
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
	 * @var array
	 */
	protected $classes = [];

	/**
	 * Get all the TenUpPlugin plugin classes.
	 *
	 * @return array
	 */
	protected function get_classes() {
		return ClassFinder::getClassesInNamespace( 'TenUpPlugin', ClassFinder::RECURSIVE_MODE );
	}

	/**
	 * Initialize all the TenUpPlugin plugin classes.
	 *
	 * @return void
	 */
	public function init_classes() {
		foreach ( $this->get_classes() as $class ) {
			// Create a slug for the class name.
			$slug = $this->slugify_class_name( $class );

			// If the class has already been initialized, skip it.
			if ( isset( $this->classes[ $slug ] ) ) {
				continue;
			}

			// Create a new reflection of the class.
			$reflection_class = new ReflectionClass( $class );

			// Using reflection, check if the class can be initialized.
			// If not, skip.
			if ( ! $reflection_class->isInstantiable() ) {
				continue;
			}

			// Make sure the class is a subclass of Module, so we can initialize it.
			if ( ! $reflection_class->isSubclassOf( '\TenUpPlugin\Module' ) ) {
				continue;
			}

			// Initialize the class.
			$instantiated_class = new $class();
			// If the class can be registered, register it.
			if ( $instantiated_class->can_register() ) {
				// Call it's register method.
				$instantiated_class->register();
				// Store the class in the list of initialized classes.
				$this->classes[ $slug ] = $instantiated_class;
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
	 * @return false|\TenUpPlugin\Module
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
	 * @return array
	 */
	public function get_all_classes() {
		return $this->classes;
	}

}
