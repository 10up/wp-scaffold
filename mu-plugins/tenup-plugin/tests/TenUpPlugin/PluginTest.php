<?php

namespace TenUpPlugin;

class PluginTest extends \WP_UnitTestCase {

	public $plugin;

	function setUp() {
		$this->plugin = new Plugin();
	}

	function test_it_is_a_singleton() {
		$a = Plugin::get_instance();
		$b = Plugin::get_instance();

		$this->assertSame( $a, $b );
	}

	function test_it_knows_if_plugin_module_is_absent() {
		$actual = $this->plugin->get_plugin_support( 'no_such_module' );
		$this->assertFalse( $actual );
	}

	function test_it_knows_if_plugin_module_is_present() {
		$module = new \stdClass();
		$this->plugin->plugin_support['foo'] = $module;

		$actual = $this->plugin->get_plugin_support( 'foo' );
		$this->assertSame( $module, $actual );
	}

	function test_it_will_not_register_module_when_not_in_context() {
		$module = new CantRegisterModule();
		$actual = $this->plugin->register( $module );

		$this->assertFalse( $actual );
	}

	function test_it_will_register_module_when_in_context() {
		$module = new CanRegisterModule();
		$actual = $this->plugin->register( $module );

		$this->assertTrue( $actual );
	}

	function test_it_can_set_container_on_module() {
		$module = new CanRegisterModule();
		$this->plugin->register( $module );

		$actual = $module->container;

		$this->assertSame( $this->plugin, $actual );
	}

	function test_it_can_register_multiple_modules() {
		$modules = [
			'a' => new CanRegisterModule(),
			'b' => new CanRegisterModule(),
			'c' => new CantRegisterModule(),
		];

		$actual = $this->plugin->register_objects( $modules );

		foreach ( $modules as $name => $module ) {
			if ( 'c' === $name ) {
				$this->assertFalse( property_exists( $module, 'container' ) );
			} else {
				$this->assertSame( $this->plugin, $module->container );
			}
		}
	}
}

class CantRegisterModule {

	function can_register() {
		return false;
	}

}

class CanRegisterModule {

	public $container;

	function can_register() {
		return true;
	}

	function register() {
		return true;
	}
}
