<?php
/**
 * Template Name: Buttons Kitchen Sink
 *
 * @package TenUpTheme
 */

use function UIKit\Helpers\get_component;

// Defining button variations
if ( function_exists( 'UIKit\Helpers\get_component' ) ) :

	$default_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Read more',
				],
			],
		]
	);

	$hover_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Hover',
					'nested_class' => 'tui-button__link--hover',
				],
			],
		]
	);

	$active_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Active',
					'nested_class' => 'tui-button__link--active',
				],
			],
		]
	);

	$focus_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Focus',
					'nested_class' => 'tui-button__link--focus',
				],
			],
		]
	);

	$icon_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'class' => 'tui-button--has-icon',
					'label' => 'Icon button',
					'icon' => '<span class="dashicons dashicons-admin-home"></span>',
				],
			],
		]
	);

	$icon_button_left = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'class' => 'tui-button--has-icon tui-has-row-reverse',
					'label' => 'Icon button left',
					'icon' => '<span class="dashicons dashicons-admin-home"></span>',
				],
			],
		]
	);

	$icon_button_add_page = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'class' => 'tui-button--has-icon',
					'label' => 'Add page',
					'icon' => '<span class="dashicons dashicons-welcome-add-page"></span>',
				],
			],
		]
	);

	$icon_button_add_page_left = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'class' => 'tui-button--has-icon tui-has-row-reverse',
					'label' => 'Add page',
					'icon' => '<span class="dashicons dashicons-welcome-add-page"></span>',
				],
			],
		]
	);

	$disabled_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Disabled',
					'disabled' => true,
					'nested_class' => 'tui-button__link--disabled',
				],
			],
		]
	);

	$download_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Download',
					'url' => 'https://10up.com/uploads/2022/02/10up-eleventh-anniversary.png',
					'download' => true,
					'type' => 'image/png',
				],
			],
		]
	);

	$button_group = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Login',
				],
				[
					'label' => 'Register',
					'class' => 'is-style-outline',
				],
			],
		]
	);

	$button_group_center = get_component(
		'buttons',
		$args = [
			'class' => 'tui-has-justify-center',
			'buttons' => [
				[
					'label' => 'Login',
				],
				[
					'label' => 'Register',
					'class' => 'is-style-outline',
				],
			],
		]
	);

	$button_group_right = get_component(
		'buttons',
		$args = [
			'class' => 'tui-has-justify-end',
			'buttons' => [
				[
					'label' => 'Login',
				],
				[
					'label' => 'Register',
					'class' => 'is-style-outline',
				],
			],
		]
	);

	$button_group_space_between = get_component(
		'buttons',
		$args = [
			'class' => 'tui-has-justify-space-between',
			'buttons' => [
				[
					'label' => 'Login',
				],
				[
					'label' => 'Register',
					'class' => 'is-style-outline',
				],
			],
		]
	);

	$button_group_column = get_component(
		'buttons',
		$args = [
			'class' => 'tui-has-column tui-has-align-left',
			'buttons' => [
				[
					'label' => 'Login',
				],
				[
					'label' => 'Register',
					'class' => 'is-style-outline',
				],
			],
		]
	);

	$button_group_column_center = get_component(
		'buttons',
		$args = [
			'class' => 'tui-has-column tui-has-align-center',
			'buttons' => [
				[
					'label' => 'Login',
				],
				[
					'label' => 'Register',
					'class' => 'is-style-outline',
				],
			],
		]
	);

	$button_group_column_right = get_component(
		'buttons',
		$args = [
			'class' => 'tui-has-column tui-has-align-right',
			'buttons' => [
				[
					'label' => 'Login',
				],
				[
					'label' => 'Register',
					'class' => 'is-style-outline',
				],
			],
		]
	);


	$link_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Link button',
					'url' => 'https://10up.com/',
					'rel' => 'nofollow',
				],
			],
		]
	);

	$link_blank_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Link button target blank',
					'url' => 'https://10up.com/',
					'target' => '_blank',
				],
			],
		]
	);

	$outline_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Outline',
					'class' => 'is-style-outline',
				],
			],
		]
	);

	$outline_button_hover = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Outline hover',
					'class' => 'is-style-outline',
					'nested_class' => 'tui-button__link--hover',
				],
			],
		]
	);

	$outline_button_active = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Outline active',
					'class' => 'is-style-outline',
					'nested_class' => 'tui-button__link--active',
				],
			],
		]
	);

	$outline_button_focus = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Outline focus',
					'class' => 'is-style-outline',
					'nested_class' => 'tui-button__link--focus',
				],
			],
		]
	);

	$outline_button_disabled = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Disabled',
					'class' => 'is-style-outline',
					'disabled' => true,
					'nested_class' => 'tui-button__link--disabled',
				],
			],
		]
	);

	$text_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Text',
					'class' => 'is-style-text',
				],
			],
		]
	);

	$text_button_hover = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Text hover',
					'class' => 'is-style-text',
					'nested_class' => 'tui-button__link--hover',
				],
			],
		]
	);

	$text_button_active = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Text active',
					'class' => 'is-style-text',
					'nested_class' => 'tui-button__link--active',
				],
			],
		]
	);

	$text_button_focus = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Text focus',
					'class' => 'is-style-text',
					'nested_class' => 'tui-button__link--focus',
				],
			],
		]
	);

	$text_button_disabled = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Disabled',
					'class' => 'is-style-text',
					'disabled' => true,
					'nested_class' => 'tui-button__link--disabled',
				],
			],
		]
	);


	$primary_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Primary',
				],
				[
					'label' => 'Primary',
					'class' => 'has-xl-font-size has-custom-font-size',
				],
			],
		]
	);

	$outline_primary_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Primary',
					'class' => 'is-style-outline',
				],
				[
					'label' => 'Primary',
					'class' => 'is-style-outline has-xl-font-size has-custom-font-size',
				],
			],
		]
	);

	$text_primary_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Primary',
					'class' => 'is-style-text',
				],
				[
					'label' => 'Primary',
					'class' => 'is-style-text has-xl-font-size has-custom-font-size',
				],
			],
		]
	);

	$secondary_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Secondary',
					'nested_class' => 'has-light-color has-secondary-background-color has-text-color has-background',
				],
				[
					'label' => 'Secondary',
					'class' => 'has-xl-font-size has-custom-font-size',
					'nested_class' => 'has-light-color has-secondary-background-color has-text-color has-background',
				],
			],
		]
	);

	$outline_secondary_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Secondary',
					'class' => 'is-style-outline',
					'nested_class' => 'has-secondary-color has-secondary-background-color has-text-color has-background',
				],
				[
					'label' => 'Secondary',
					'class' => 'is-style-outline has-xl-font-size has-custom-font-size',
					'nested_class' => 'has-secondary-color has-secondary-background-color has-text-color has-background',
				],
			],
		]
	);

	$text_secondary_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Secondary',
					'class' => 'is-style-text',
					'nested_class' => 'has-secondary-color has-text-color',
				],
				[
					'label' => 'Secondary',
					'class' => 'is-style-text has-xl-font-size has-custom-font-size',
					'nested_class' => 'has-secondary-color has-text-color',
				],
			],
		]
	);

	$tertiary_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Tertiary',
					'nested_class' => 'has-light-color has-tertiary-background-color has-text-color has-background',
				],
				[
					'label' => 'Tertiary',
					'class' => 'has-xl-font-size has-custom-font-size',
					'nested_class' => 'has-light-color has-tertiary-background-color has-text-color has-background',
				],
			],
		]
	);

	$outline_tertiary_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Tertiary',
					'class' => 'is-style-outline',
					'nested_class' => 'has-tertiary-color has-tertiary-background-color has-text-color has-background',
				],
				[
					'label' => 'Tertiary',
					'class' => 'is-style-outline has-xl-font-size has-custom-font-size',
					'nested_class' => 'has-tertiary-color has-tertiary-background-color has-text-color has-background',
				],
			],
		]
	);

	$text_tertiary_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Tertiary',
					'class' => 'is-style-text',
					'nested_class' => 'has-tertiary-color has-text-color',
				],
				[
					'label' => 'Tertiary',
					'class' => 'is-style-text has-xl-font-size has-custom-font-size',
					'nested_class' => 'has-tertiary-color has-text-color',
				],
			],
		]
	);

	$dark_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Dark',
					'nested_class' => 'has-dark-background-color',
				],
				[
					'label' => 'Dark',
					'class' => 'has-xl-font-size has-custom-font-size',
					'nested_class' => 'has-dark-background-color',
				],
			],
		]
	);

	$light_button = get_component(
		'buttons',
		$args = [
			'buttons' => [
				[
					'label' => 'Light',
					'nested_class' => 'has-light-background-color',
				],
				[
					'label' => 'Light',
					'class' => 'has-xl-font-size has-custom-font-size',
					'nested_class' => 'has-light-background-color',
				],
			],
		]
	);


endif;

get_header();

?>

<div class="tui-content-container" style="max-width: 1280px; margin: 0 auto; padding: 1.5rem">

<h1 class="uikit__heading">
	<?php echo esc_html( get_the_title() ); ?> <?php esc_html_e( 'Sink Page', 'tenup-theme' ); ?>
</h1>

<?php if ( have_posts() ) : ?>
	<hr />
	<?php while ( have_posts() ) : the_post(); ?>
		<h2 class="uikit__heading">
			<div class="uikit__block">
				<?php echo esc_html( get_the_title() ); ?> - <?php esc_html_e( 'Blocks', 'tenup-theme' ); ?>
				<?php
				// TODO: add core block variants
				?>
			</div>
		</h2>
		<?php the_content(); ?>
	<?php endwhile; ?>
<?php endif; ?>

<div class="uikit__container">

	<div class="uikit__content tui-content-container">
		<hr />

		<h2 class="uikit__heading">
			<div class="uikit__block">
				<?php echo esc_html( get_the_title() ); ?> - <?php esc_html_e( 'Partials : Fill Variant', 'tenup-theme' ); ?>
			</div>
		</h2>

		<section class="uikit__section" id="buttons">

			<div class="content">

				<h3 class="heading">Default Button (Primary)</h3>
				<?php
					echo wp_kses_post( $default_button );
				?>


				<h3 class="heading">Hover Button</h3>
				<?php
					echo wp_kses_post( $hover_button );
				?>

				<h3 class="heading">Active Button</h3>
				<?php
					echo wp_kses_post( $active_button );
				?>

				<h3 class="heading">Focus Button</h3>
				<?php
					echo wp_kses_post( $focus_button );
				?>

				<h3 class="heading">Disabled Button</h3>
				<?php
					echo wp_kses_post( $disabled_button );
				?>

				<h3 class="heading">Download Button</h3>
				<?php
					echo wp_kses_post( $download_button );
				?>

				<h3 class="heading">Link Button (rel="nofollow")</h3>
				<?php
					echo wp_kses_post( $link_button );
				?>

				<h3 class="heading">Link Button (target="_blank")</h3>
				<?php
					echo wp_kses_post( $link_blank_button );
				?>

			</div><!--/.content-->

		</section><!--/.uikit__section-->

		<hr>

		<h2 class="uikit__heading">
			<div class="uikit__block">
				<?php echo esc_html( get_the_title() ); ?> - <?php esc_html_e( 'Partials : Outline Variant', 'tenup-theme' ); ?>
			</div>
		</h2>

		<section class="uikit__section" id="buttons-outline">

			<div class="content">
				<h3 class="heading">Outline</h3>
				<?php
					echo wp_kses_post( $outline_button );
				?>

				<h3 class="heading">Outline Hover</h3>
				<?php
					echo wp_kses_post( $outline_button_hover );
				?>


				<h3 class="heading">Outline Active</h3>
				<?php
					echo wp_kses_post( $outline_button_active );
				?>

				<h3 class="heading">Outline Focus</h3>
				<?php
					echo wp_kses_post( $outline_button_focus );
				?>


				<h3 class="heading">Outline Disabled</h3>
				<?php
					echo wp_kses_post( $outline_button_disabled );
				?>

			</div><!--/.content-->

		</section><!--/.uikit__section-->

		<h2 class="uikit__heading">
			<div class="uikit__block">
				<?php echo esc_html( get_the_title() ); ?> - <?php esc_html_e( 'Partials : Text Variant', 'tenup-theme' ); ?>
			</div>
		</h2>

		<section class="uikit__section" id="buttons-text">

			<div class="content">
				<h3 class="heading">Text</h3>
				<?php
					echo wp_kses_post( $text_button );
				?>

				<h3 class="heading">Text Hover</h3>
				<?php
					echo wp_kses_post( $text_button_hover );
				?>


				<h3 class="heading">Text Active</h3>
				<?php
					echo wp_kses_post( $text_button_active );
				?>

				<h3 class="heading">Text Focus</h3>
				<?php
					echo wp_kses_post( $text_button_focus );
				?>


				<h3 class="heading">Text Disabled</h3>
				<?php
					echo wp_kses_post( $text_button_disabled );
				?>

			</div><!--/.content-->

		</section><!--/.uikit__section-->

		<hr />

		<h2 class="uikit__heading">
			<div class="uikit__block">
				<?php echo esc_html( get_the_title() ); ?> - <?php esc_html_e( 'Partials : Form Inputs', 'tenup-theme' ); ?>
			</div>
		</h2>

		<section>
			<div>
				<h3 class="heading">Submit Button</h3>
				<div class="tui-buttons">
					<div class="tui-button">
						<input type="submit" class="tui-button__link" value="Submit">
					</div>
				</div>

				<h3 class="heading">Reset Button</h3>
				<div class="tui-buttons">
					<div class="tui-button is-style-outline">
						<input type="reset" class="tui-button__link" value="Reset">
					</div>
				</div>
			</div><!--/.content-->

		</section><!--/.uikit__section-->

		<hr />
		<h2 class="uikit__heading">
			<div class="uikit__block">
				<?php echo esc_html( get_the_title() ); ?> - <?php esc_html_e( 'Partials : Colors', 'tenup-theme' ); ?>
			</div>
		</h2>

		<section>
			<div>
				<h3 class="heading">Primary Button</h3>
				<?php
					echo wp_kses_post( $primary_button );
				?>

				<h3 class="heading">Primary Outline Button</h3>
				<?php
					echo wp_kses_post( $outline_primary_button );
				?>

				<h3 class="heading">Primary Text Button</h3>
				<?php
					echo wp_kses_post( $text_primary_button );
				?>

				<h3 class="heading">Secondary Button</h3>
				<?php
					echo wp_kses_post( $secondary_button );
				?>

				<h3 class="heading">Secondary Outline Button</h3>
				<?php
					echo wp_kses_post( $outline_secondary_button );
				?>

				<h3 class="heading">Secondary Text Button</h3>
				<?php
					echo wp_kses_post( $text_secondary_button );
				?>

				<h3 class="heading">Tertiary Button</h3>
				<?php
					echo wp_kses_post( $tertiary_button );
				?>

				<h3 class="heading">Tertiary Outline Button</h3>
				<?php
					echo wp_kses_post( $outline_tertiary_button );
				?>

				<h3 class="heading">Tertiary Text Button</h3>
				<?php
					echo wp_kses_post( $text_tertiary_button );
				?>

				<h3 class="heading">Dark Button</h3>
				<?php
					echo wp_kses_post( $dark_button );
				?>

				<h3 class="heading">On light background</h3>
				<div style="background-color: #e7e7e7; padding: 1rem;">
					<?php
						echo wp_kses_post( $dark_button );
					?>
				</div>

				<h3 class="heading">Light Button</h3>
				<?php
					echo wp_kses_post( $light_button );
				?>

				<h3 class="heading">On dark background</h3>
				<div class="has-dark-background-color" style="padding: 1rem;">
					<?php
						echo wp_kses_post( $light_button );
					?>
				</div>
			</div>
		</section>


		<hr />

		<h2 class="uikit__heading">
			<div class="uikit__block">
				<?php echo esc_html( get_the_title() ); ?> - <?php esc_html_e( 'Partials : Icon Buttons', 'tenup-theme' ); ?>
			</div>
		</h2>

		<section>
			<div>
				<h3 class="heading">Icon Button</h3>
				<?php
					echo wp_kses_post( $icon_button );
				?>

				<h3 class="heading">Icon Button left</h3>
				<?php
					echo wp_kses_post( $icon_button_left );
				?>
				<h3 class="heading">Icon Button</h3>
				<?php
					echo wp_kses_post( $icon_button_add_page );
				?>

				<h3 class="heading">Icon Button left</h3>
				<?php
					echo wp_kses_post( $icon_button_add_page_left );
				?>
			</div>
		</section>

		<hr>

		<h2 class="uikit__heading">
			<div class="uikit__block">
				<?php echo esc_html( get_the_title() ); ?> - <?php esc_html_e( 'Partials : Button Group', 'tenup-theme' ); ?>
			</div>
		</h2>

		<section class="uikit__section" id="buttons-outline">
			<h3 class="heading">Button Group</h3>
			<div>
				<?php
					echo wp_kses_post( $button_group );
				?>
			</div>

			<h3 class="heading">Button Group Center</h3>
			<div>
				<?php
					echo wp_kses_post( $button_group_center );
				?>
			</div>

			<h3 class="heading">Button Group Right</h3>
			<div>
				<?php
					echo wp_kses_post( $button_group_right );
				?>
			</div>

			<h3 class="heading">Button Group Space Between</h3>
			<div>
				<?php
					echo wp_kses_post( $button_group_space_between );
				?>
			</div>

			<h3 class="heading">Button Group Column</h3>
			<div>
				<?php
					echo wp_kses_post( $button_group_column );
				?>
			</div>


			<h3 class="heading">Button Group Column Center</h3>
			<div>
				<?php
					echo wp_kses_post( $button_group_column_center );
				?>
			</div>


			<h3 class="heading">Button Group Column Right</h3>
			<div>
				<?php
					echo wp_kses_post( $button_group_column_right );
				?>
			</div>
		</section>

	</div><!--/.uikit__content-->

</div><!--/.uikit__container-->

<?php
	// TODO: add output of custom button block (bring over from block libray) - with guidance on when / how use (i.e. either/or with core button block). Make sure it uses the php partial above
?>

<hr>

<p>Want to have the following sections/additions:</p>
<ul>
	<li>Toggle Dark Mode</li>
	<li>Dark background section</li>
</ul>

<?php get_footer(); ?>
</div>
