<?php

namespace TenUpPlugin\PostType;

class PostTypeFactoryTest extends \WP_UnitTestCase {

	public $factory;

	function setUp() {
		parent::setUp();

		$this->factory = new PostTypeFactory();
	}

	function test_it_can_be_created() {
		$this->assertInstanceOf(
			'TenUpPlugin\PostType\PostTypeFactory',
			$this->factory
		);
	}

	function test_it_knows_if_post_type_has_not_been_built() {
		$actual = $this->factory->exists( 'foo' );
		$this->assertFalse( $actual );
	}

	function test_it_can_build_a_post_type() {
		$actual = $this->factory->build( 'post' );

		$this->assertInstanceOf(
			'TenUpPlugin\PostType\Post',
			$actual
		);
	}

	function test_it_returns_cached_post_type_if_already_built() {
		$instance = new \stdClass();
		$this->factory->post_types['foo'] = $instance;

		$actual = $this->factory->build_if( 'foo' );
		$this->assertSame( $instance, $actual );
	}

	function test_it_will_not_register_post_type_if_no_theme_support() {
		$foo1 = new Foo1();
		$foo1->register();

		$actual = post_type_exists( 'foo1' );
		$this->assertFalse( $actual );
	}

	function test_it_will_register_post_type_if_theme_support() {
		add_theme_support( 'post_type_foo1' );

		$foo1 = new Foo1();
		$foo1->register();

		$actual = post_type_exists( 'foo1' );
		$this->assertTrue( $actual );
	}

	// DEPRECATED
	function _test_it_can_build_subset_of_post_types() {
		foreach ( $this->factory->get_supported_post_types() as $post_type ) {
			unregister_post_type( $post_type );
		}

		// unregister_post_type() does not work for the core built- in post types
		// and since we can't unregister them,
		// we should not test whether they aren't registered.
		//
		// This excludes the post types using the strings that match their name
		// in WP Core, not the constants PAGE_POST_TYPE and POST_POST_TYPE.
		// The reason is that if our code's post types change away from the
		// Core post types, so that POST_POST_TYPE is not 'post' or
		// PAGE_POST_TYPE is not 'page', we'll want to run this test against
		// the new POST_POST_TYPE and PAGE_POST_TYPE.
		//
		// This section isn't supposed to exclude two of the plugin's post types;
		// it's supposed to exclude two of WordPress Core's post types
		// which may happen to be present in our plugin's list of post types.
		$post_types_to_test = array_diff(
			$this->factory->get_supported_post_types(),
			array(
				'post',
				'page',
			)
		);

		// Test whether there's anything messing with the registration
		// of each post type which we would like to make modular.
		foreach ( $post_types_to_test as $post_type ) {
			// Make a list of all post types that we're not registering within this loop.
			$excluded = array_diff(
				$post_types_to_test,
				array(
					$post_type,
				)
			);

			// This theme supports this post type.
			$support = 'post_type_' . $post_type;
			add_theme_support( $support );

			// run.
			$this->factory->build_all();

			/*
			 * Positive tests: does this work as intended?
			 */
			// Test this test is set up correctly
			$this->assertTrue(
				get_theme_support( $support ),
				sprintf(
					'%1$s failed to register support for post type `%2$s` with theme support string `%3$s`',
					__FUNCTION__,
					$post_type,
					$support
				)
			);
			// Test that the tested code works.
			$this->assertTrue(
				post_type_exists( $post_type ),
				sprintf(
					'%1$s failed to find that post type `%2$s` was registered when it should have been registered.',
					__FUNCTION__,
					$post_type,
					$support
				)
			);

			/*
			 * Negative tests: does this not work as unintended?
			 */
			foreach ( $excluded as $excluded_post_type ) {
				$excluded_support = 'post_type_' . $excluded_post_type;
				// Test that this test is set up correctly.
				$this->assertFalse(
					get_theme_support( $excluded_support ),
					sprintf(
						'%1$s erroneously registered support for post type `%2$s` with theme support string `%3$s`',
						__FUNCTION__,
						$excluded_post_type,
						$excluded_support
					)
				);
				// Test that this code did not misregister any posts.
				$this->assertFalse(
					post_type_exists( $excluded_post_type ),
					sprintf(
						'%1$s found that post type `%2$s` was misregistered when it should not be registered.',
						__FUNCTION__,
						$excluded_post_type
					)
				);
			} // foreach excluded post type.

			// Clean up the registered post type from this loop.
			remove_theme_support( $support );
			unregister_post_type( $post_type );
		} // foreach post type to test.

		// tear down: add all these post types back
		foreach ( $this->factory->get_supported_post_types() as $post_type ) {
			add_theme_support( 'post_type_' . $taxonomy );
		}
		$this->factory->build_all();
	}
}

class Foo1 extends AbstractPostType {

	public function get_name() {
		return 'foo1';
	}

	public function get_singular_label() {
		return 'foo';
	}

	public function get_plural_label() {
		return 'foos';
	}

}
