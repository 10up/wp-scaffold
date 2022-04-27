<?php
/**
 * Template Name: Buttons Kitchen Sink
 *
 * @package TenUpTheme
 */

use function WPScaffoldUI\Helpers\get_component;

// Defining button variations
if ( function_exists( 'WPScaffoldUI\Helpers\get_component' ) ) :

	$default_button = get_component(
		'button'
	);


	$normal_button = get_component(
		'button',
		$args = [
			'label' => 'Learn More',
		]
	);

	$hover_button = get_component(
		'button',
		$args = [
			'class' => 'tui-button--hover',
		]
	);

	$active_button = get_component(
		'button',
		$args = [
			'class' => 'tui-button--active',
		]
	);

	$disabled_button = get_component(
		'button',
		$args = [
			'disabled' => true,
		]
	);

	$focus_button = get_component(
		'button',
		$args = [
			'class' => 'tui-button--focus',
		]
	);

	$link_button = get_component(
		'button',
		$args = [
			'url' => 'https://10up.com/',
		]
	);

	$link_blank_button = get_component(
		'button',
		$args = [
			'url' => 'https://10up.com/',
			'target' => '_blank',
		]
	);

	$primary_submit_button = get_component(
		'button',
		$args = [
			'el' => 'submit',
		]
	);

	$primary_reset_button = get_component(
		'button',
		$args = [
			'label' => 'Learn More',
		]
	);

	$submit_button = get_component(
		'button',
		$args = [
			'el' => 'submit',
			'label' => 'Submit',
		]
	);

	$reset_button = get_component(
		'button',
		$args = [
			'el' => 'submit',
			'label' => 'Reset',
		]
	);

	$primary_button = get_component(
		'button',
		$args = [
			'url' => 'https://10up.com',
			'style' => 'primary',
		]
	);

	$secondary_button = get_component(
		'button',
		$args = [
			'url' => 'https://10up.com',
			'style' => 'secondary',
			'rel' => 'nofollow',
		]
	);

endif;

get_header();

?>

<h1 class="uikit__heading">
	<?php echo esc_html( get_the_title() ); ?> <?php esc_html_e( 'Sink Page', 'tenup-theme' ); ?>
</h1>

<div class="uikit__container">

	<div class="uikit__content">

		<h2 class="uikit__heading">
			<div class="uikit__block">
				<?php echo esc_html( get_the_title() ); ?> - <?php esc_html_e( 'Partials', 'tenup-theme' ); ?>
			</div>
		</h2>

		<section class="uikit__section" id="buttons">

			<div class="content">

				<h3 class="heading">Default Button</h3>

				<?php
					echo wp_kses_post( $default_button );
				?>

				<h3 class="heading">Normal Button</h3>

				<?php
					echo wp_kses_post( $normal_button );
				?>

				<h3 class="heading">Hover Button</h3>

				<?php
					echo wp_kses_post( $hover_button );
				?>

				<h3 class="heading">Active Button</h3>

				<?php
					echo wp_kses_post( $active_button );
				?>

				<h3 class="heading">Disabled Button</h3>

				<?php
					echo wp_kses_post( $disabled_button );
				?>

				<h3 class="heading">Focus Button</h3>

				<?php
					echo wp_kses_post( $focus_button );
				?>

				<h3 class="heading">Link Button</h3>

				<?php
					echo wp_kses_post( $link_button );
				?>

				<h3 class="heading">Link Blank Target Button</h3>

				<?php
					echo wp_kses_post( $link_blank_button );
				?>

				<h3 class="heading">Submit Button</h3>

				<?php
					echo wp_kses_post( $submit_button );
				?>

				<h3 class="heading">Reset Button</h3>

				<?php
					echo wp_kses_post( $reset_button );
				?>

				<h3 class="heading">Disabled Button</h3>

				<?php
					echo wp_kses_post( $disabled_button );
				?>

				<p>end this grouping</p>

				<hr>

				<p>Want to have the following sections/additions:</p>
				<ul>
					<li>Color variations</li>
					<li>Size variations</li>
					<li>Icon variations</li>
					<li>Toggle Dark Mode</li>
					<li>Dark background section</li>
				</ul>

				<h3 class="heading">Primary Button</h3>

				<?php
					echo wp_kses_post( $primary_button );
				?>

				<h3 class="heading">Secondary Button</h3>

				<?php
					echo wp_kses_post( $secondary_button );
				?>
			</div><!--/.content-->

		</section><!--/.uikit__section-->


	</div><!--/.uikit__content-->

</div><!--/.uikit__container-->

<?php
	// TODO: add output of custom button block (bring over from block libray) - with guidance on when / how use (i.e. either/or with core button block). Make sure it uses the php partial above
?>

<?php // Block editor output ?>
<?php if ( have_posts() ) : ?>
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

<?php get_footer(); ?>
