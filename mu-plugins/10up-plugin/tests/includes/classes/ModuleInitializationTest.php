<?php
/**
 * Test Class
 *
 * @package TenUpPlugin
 */

namespace includes\classes;

/**
 * Test Class
 */
class ModuleInitializationTest extends \WP_UnitTestCase {

	/**
	 * Undocumented variable
	 *
	 * @var [type]
	 */
	public $class;

	/**
	 * Set up test fixtures.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$this->class = \TenUpPlugin\ModuleInitialization::instance();
	}

	/**
	 * Ensure we can find the right classes.
	 *
	 * @return void
	 */
	public function test_it_can_find_classes() {
		$this->class->init_classes();

		$class  = new \ReflectionClass( $this->class );
		$method = $class->getMethod( 'get_classes' );

		$classes = $method->invoke( $this->class );

		// Check that we have the concrete classes we expect to see.
		$this->assertContains( 'TenUpPlugin\PostTypes\AbstractPostType', $classes );
		$this->assertContains( 'TenUpPlugin\PostTypes\AbstractCorePostType', $classes );
		$this->assertContains( 'TenUpPlugin\Taxonomies\AbstractTaxonomy', $classes );
		$this->assertContains( 'TenUpPlugin\Module', $classes );
		$this->assertContains( 'TenUpPlugin\ModuleInitialization', $classes );
	}

	/**
	 * Ensure we can find the right classes.
	 *
	 * @return void
	 */
	public function test_it_can_find_classes_to_register() {
		$this->class->init_classes();
		$classes = $this->class->get_all_classes();

		// Check that we have only classes that extend Module and more than 0.
		$this->assertContainsOnlyInstancesOf( 'TenUpPlugin\Module', $classes );
		$this->assertGreaterThan( 0, count( $classes ) );
	}
}
