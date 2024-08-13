<?php
/**
 * Test Class
 *
 * @package TenUpTheme
 */

namespace TenUpTheme;

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

		$this->class = \TenUpTheme\ModuleInitialization::instance();
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
		$this->assertCount( 2, $classes );
	}
}
