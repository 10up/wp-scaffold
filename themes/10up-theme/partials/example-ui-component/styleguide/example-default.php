<?php
/**
 * Example ui component Styleguide Example - Default - Partial
 *
 * @package TenUpTheme
 */

// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
$faker = Faker\Factory::create();

/*
 * Use this to setup a specific example of the component
 * for inclusion in the styleguide
 *  - Don't forget to include new variants of this partial in ./examples.php
 *    - using the $name param (currently null) to match the filename suffix
 *
 * Faker is a PHP library that generates fake data for you.
 * https://github.com/fzaninotto/Faker
 *
 * For images a better alternative is usually
 * https://picsum.photos
 *
 * For illustrations you may want to consider
 * https://doodleipsum.com/
 *
 */
?>
<h3 class="sub-heading">Variation: Default</h3>
<?php
	get_template_part(
		'partials/example-ui-component/example-ui-component',
		null,
		[
			'class_names' => '',
		]
	);
	?>
