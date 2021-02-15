<?php

namespace TenUpPlugin\Taxonomy;

class AbstractTaxonomyTest extends \WP_UnitTestCase {

	public $taxonomy;

	function setUp() {
		parent::setUp();

		$this->taxonomy = new FooTag();
		$this->taxonomy->name = 'tag' . uniqid();
	}

	function test_it_has_a_name() {
		$actual = $this->taxonomy->get_name();
		$this->assertContains( 'tag', $actual );
	}

	function test_it_has_a_singular_name() {
		$actual = $this->taxonomy->get_singular_label();
		$this->assertEquals( 'Tag', $actual );
	}

	function test_it_has_a_plural_name() {
		$actual = $this->taxonomy->get_plural_label();
		$this->assertEquals( 'Tags', $actual );
	}

	function test_it_knows_if_current_theme_does_not_support_taxonomy() {
		$actual = $this->taxonomy->has_theme_support();
		$this->assertFalse( $actual );
	}

	function test_it_knows_if_current_theme_supports_taxonomy() {
		add_theme_support( 'taxonomy_' . $this->taxonomy->get_name() );

		$actual = $this->taxonomy->has_theme_support();
		$this->assertTrue( $actual );
	}

	function test_it_has_taxonomy_options() {
		$actual = $this->taxonomy->get_options();
		$this->assertTrue( $actual['public'] );
	}

	function test_it_will_not_register_taxonomy_without_theme_support() {
		$actual = $this->taxonomy->register();
		$this->assertFalse( $actual );
		$this->assertFalse( taxonomy_exists( $this->taxonomy->get_name() ) ) ;
	}

	function test_it_will_register_taxonomy_with_theme_support() {
		$this->taxonomy->has_theme_support = true;

		$actual = $this->taxonomy->register();
		$this->assertTrue( $actual );
		$this->assertTrue( taxonomy_exists( $this->taxonomy->get_name() ) ) ;
	}

	function test_it_can_override_taxonomy_theme_support() {
		$name = $this->taxonomy->get_name();
		$self = $this;

		add_filter( 'tenup_plugin_taxonomy_has_theme_support', function( $supports, $taxonomy ) use ( $self, $name ) {
			$self->assertEquals( $name, $taxonomy );
			$self->assertFalse( $supports );

			return true;
		}, 10, 2 );

		$this->taxonomy->register();
		$this->assertTrue( taxonomy_exists( $name ) );
	}

	function test_it_can_override_taxonomy_options() {
		$this->taxonomy->has_theme_support = true;

		$name = $this->taxonomy->get_name();
		$self = $this;

		$new_options = [
			'public' => false,
		];

		add_filter( 'tenup_plugin_taxonomy_options', function( $options, $taxonomy ) use ( $self, $new_options, $name ) {
			$self->assertEquals( $name, $taxonomy );
			$self->assertTrue( $options['public'] );

			return $new_options;
		}, 10, 2 );

		$this->taxonomy->register();
		$this->assertFalse( get_taxonomy( $name )->public );
	}
}

class FooTag extends AbstractTaxonomy {

	public $name;
	public $has_theme_support;

	public function get_name() {
		return $this->name;
	}

	public function get_singular_label() {
		return 'Tag';
	}

	public function get_plural_label() {
		return 'Tags';
	}

	public function has_theme_support() {
		if ( isset( $this->has_theme_support ) ) {
			return $this->has_theme_support;
		} else {
			return parent::has_theme_support();
		}
	}

}
