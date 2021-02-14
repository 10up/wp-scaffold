<?php
namespace TenUpPlugin\Core;

/**
 * This is a very basic test case to get things started. You should probably rename this and make
 * it work for your project. You can use all the tools provided by WP Mock and Mockery to create
 * your tests. Coverage is calculated against your includes/ folder, so try to keep all of your
 * functional code self contained in there.
 *
 * References:
 *   - http://phpunit.de/manual/current/en/index.html
 *   - https://github.com/padraic/mockery
 *   - https://github.com/TenUp/wp_mock
 */

use TenUpPlugin as Base;

class Core_Tests extends Base\TestCase {

	protected $testFiles = [
		'functions/core.php'
	];

	/**
	 * Test load method.
	 */
	public function test_setup() {
		// Setup
		\WP_Mock::expectActionAdded( 'init', 'TenUpPlugin\Core\i18n' );
		\WP_Mock::expectActionAdded( 'init', 'TenUpPlugin\Core\init' );
		\WP_Mock::expectAction( 'TenUp_plugin_loaded' );

		// Act
		setup();

		// Verify
		$this->assertConditionsMet();
	}

	/**
	 * Test internationalization integration.
	 */
	public function test_i18n() {
		// Setup
		\WP_Mock::userFunction( 'get_locale', array(
			'times' => 1,
			'args' => array(),
			'return' => 'en_US',
		) );
		\WP_Mock::onFilter( 'plugin_locale' )->with( 'en_US', 'TenUp-plugin' )->reply( 'en_US' );
		\WP_Mock::userFunction( 'load_textdomain', array(
			'times' => 1,
			'args' => array( 'TenUp-plugin', 'lang_dir/TenUp-plugin/TenUp-plugin-en_US.mo' ),
		) );
		\WP_Mock::userFunction( 'plugin_basename', array(
			'times' => 1,
			'args' => array( 'path' ),
			'return' => 'path',
		) );
		\WP_Mock::userFunction( 'load_plugin_textdomain', array(
			'times' => 1,
			'args' => array( 'TenUp-plugin', false, 'path/languages/' ),
		) );

		// Act
		i18n();

		// Verify
		$this->assertConditionsMet();
	}

	/**
	 * Test initialization method.
	 */
	public function test_init() {
		// Setup
		\WP_Mock::expectAction( 'TenUp_plugin_init' );

		// Act
		init();

		// Verify
		$this->assertConditionsMet();
	}

	/**
	 * Test activation routine.
	 */
	public function test_activate() {
		// Setup
		\WP_Mock::userFunction( 'flush_rewrite_rules', array(
			'times' => 1
		) );

		// Act
		activate();

		// Verify
		$this->assertConditionsMet();
	}

	/**
	 * Test deactivation routine.
	 */
	public function test_deactivate() {
		// Setup

		// Act
		deactivate();

		// Verify
		$this->assertTrue( true ); // Replace with actual assertion
	}
}
