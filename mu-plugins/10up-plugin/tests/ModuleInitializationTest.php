<?php
namespace TenUpPlugin;

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

		$class = new \ReflectionClass( $this->class );
		$method = $class->getMethod( 'get_classes' );

		$classes = $method->invoke( $this->class );
		$this->assertCount( 9, $classes );
	}

	/**
	 * Ensure we can find the right classes.
	 *
	 * @return void
	 */
	public function test_it_can_find_classes_to_register() {
		$this->class->init_classes();
		$classes =$this->class->get_all_classes();

		// We should only be finding post and page.
		$this->assertCount( 2, $classes );
	}

}
