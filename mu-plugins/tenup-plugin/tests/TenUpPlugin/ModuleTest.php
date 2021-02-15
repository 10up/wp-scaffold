<?php

namespace TenUpPlugin;

class ModuleTest extends \WP_UnitTestCase {

	public $module;

	function setUp() {
		parent::setUp();

		$this->module = new FooModule();
		$this->module->can_register = true;
	}

	function test_it_has_a_container() {
		$this->assertTrue( property_exists( $this->module, 'container' ) );
	}

	function test_it_can_be_registered() {
		$actual = $this->module->can_register();
		$this->assertTrue( $actual );
	}

	function test_it_knows_if_registered() {
		$this->module->register();
		$actual = $this->module->registered;
		$this->assertTrue( $actual );
	}

}

class FooModule extends Module {

	public $registered;
	public $can_register;

	public function register() {
		$this->registered = true;
	}

	public function can_register() {
		return $this->can_register;
	}

}
