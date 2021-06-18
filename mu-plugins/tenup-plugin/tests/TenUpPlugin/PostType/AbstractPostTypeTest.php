<?php

namespace TenUpPlugin\PostType;

class AbstractPostTypeTest extends \WP_UnitTestCase {

	public $post_type;

	function setUp() {
		parent::setUp();

		$this->post_type = new ThingPostType();
	}

	function test_it_has_a_name() {
		$actual = $this->post_type->get_name();
		$this->assertEquals( 'thing', $actual );
	}

	function test_it_has_a_singular_label() {
		$actual = $this->post_type->get_singular_label();
		$this->assertEquals( 'Thing', $actual );
	}

	function test_it_has_a_plural_label() {
		$actual = $this->post_type->get_plural_label();
		$this->assertEquals( 'Things', $actual );
	}

	function test_it_has_post_type_options() {
		$actual = $this->post_type->get_options();
		$this->assertTrue( $actual['public'] );
		$this->assertNotEmpty( $actual['labels'] );
		$this->assertNotEmpty( $actual['supports'] );
	}

	function test_it_has_default_post_type_supports() {
		$actual = $this->post_type->get_editor_supports();
		$this->assertContains( 'title', $actual );
		$this->assertContains( 'editor', $actual );
	}

	function test_it_can_register_post_type() {
		$this->post_type->register_post_type();
		$this->assertTrue( post_type_exists( 'thing' ) );
	}

	function test_it_can_register_taxonomies() {
		$this->post_type->register_post_type();
		$this->post_type->register_taxonomies();

		$actual = is_object_in_taxonomy( 'thing', 'category' );
		$this->assertTrue( $actual );
	}

	function test_it_will_not_register_without_theme_support() {
		$name = 't' . uniqid();

		$this->post_type->name = $name;
		$this->post_type->has_theme_support = false;
		$actual = $this->post_type->register();

		$this->assertFalse( $actual );
		$this->assertFalse( post_type_exists( $name ) );
	}

	function test_it_will_register_post_type_with_theme_support() {
		$name = 't' . uniqid();

		$this->post_type->name = $name;
		$this->post_type->has_theme_support = true;
		$actual = $this->post_type->register();

		$this->assertTrue( $actual );
		$this->assertTrue( post_type_exists( $name ) );
	}

	// Hooks
	function test_it_can_filter_editor_supports() {
		$name = 't' . uniqid();
		$this->post_type->has_theme_support = true;
		$this->post_type->name = $name;

		$self         = $this;
		$new_supports = [
			'foo',
		];

		add_filter( 'tenup_plugin_post_type_supports', function( $supports, $post_type ) use ( $self, $new_supports, $name ) {
			$self->assertEquals( $name, $post_type );
			$self->assertContains( 'title', $supports );

			return $new_supports;
		}, 10, 2 );

		$this->post_type->register();
		$this->assertTrue( post_type_supports( $name, 'foo' ) );
	}

	function test_it_can_filter_has_theme_support() {
		$name = 't' . uniqid();
		$this->post_type->name = $name;

		$self         = $this;
		$new_supports = 'foo';

		add_filter( 'tenup_plugin_post_type_has_theme_support', function( $supports, $post_type ) use ( $self, $new_supports, $name ) {
			$self->assertEquals( $name, $post_type );
			$self->assertFalse( $supports );

			return $new_supports;
		}, 10, 2 );

		$this->post_type->register();
		$this->assertTrue( post_type_exists( $name ) );
	}

	function test_it_can_filter_post_type_options() {
		$name = 't' . uniqid();
		$this->post_type->has_theme_support = true;
		$this->post_type->name = $name;

		$self        = $this;
		$new_options = [
			'public' => false,
		];

		add_filter( 'tenup_plugin_post_type_options', function( $options, $post_type ) use ( $self, $new_options, $name ) {
			$self->assertEquals( $name, $post_type );
			$self->assertTrue( $options['public'] );

			return $new_options;
		}, 10, 2 );

		$actual = $this->post_type->register();
		$this->assertFalse( get_post_type_object( $name )->public );
	}

	function test_it_can_filter_post_type_taxonomies() {
		$name = 't' . uniqid();
		$this->post_type->has_theme_support = true;
		$this->post_type->name = $name;

		$self        = $this;
		$new_taxonomies = [
			'post_tag',
		];

		add_filter( 'tenup_plugin_post_type_taxonomies', function( $taxonomies, $post_type ) use ( $self, $new_taxonomies, $name ) {
			$self->assertEquals( $name, $post_type );
			$self->assertNotEmpty( $taxonomies );

			return $new_taxonomies;
		}, 10, 2 );

		$this->post_type->register();
		$this->assertTrue( is_object_in_taxonomy( $name, 'post_tag' ) );
		$this->assertFalse( is_object_in_taxonomy( $name, 'category' ) );
	}
}

class ThingPostType extends AbstractPostType {

	public $has_theme_support;
	public $name = 'thing';

	public function get_name() {
		return $this->name;
	}

	public function get_singular_label() {
		return 'Thing';
	}

	public function get_plural_label() {
		return 'Things';
	}

	public function get_supported_taxonomies() {
		return [
			'category',
			'post_tag',
		];
	}

	public function has_theme_support() {
		if ( isset( $this->has_theme_support ) ) {
			return $this->has_theme_support;
		} else {
			return parent::has_theme_support();
		}
	}

}
