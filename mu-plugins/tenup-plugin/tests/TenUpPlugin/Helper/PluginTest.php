<?php

namespace TenUpPlugin;

class PluginHelperTest extends \WP_UnitTestCase {

	function setUp() {
		parent::setUp();
	}

	function test_it_can_find_plugin_singleton() {
		$a = get_plugin();
		$b = get_plugin();

		$this->assertSame( $a, $b );
	}

	function test_it_can_find_plugin_support() {
		$actual = get_plugin_support( 'assets' );
		$this->assertNotEmpty( $actual );
	}

}
