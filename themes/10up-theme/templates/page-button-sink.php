<?php
/**
 * Template Name: Buttons Kitchen Sink
 *
 * @package TenUpTheme
 */

use function WPScaffoldUI\Helpers\get_component;

// Defining button variations
if ( function_exists( 'WPScaffoldUI\Helpers\get_component' ) ) :
	$primary_link = get_component(
		'button',
		$args = [
			'type' => 'button',
			'url' => 'https://10up.com',
		]
	);
	$secondary_link = get_component(
		'button',
		$args = [
			'type' => 'button',
			'url' => 'https://10up.com',
			'style' => 'secondary',
			'rel' => 'nofollow',
		]
	);
	$button = get_component(
		'button',
		$args = [
			'type' => 'button',
		]
	);
	$plain = get_component( 'button' );
endif;

get_header();
?>

<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<h2><?php the_title(); ?></h2>
			<?php the_content(); ?>
		<?php endwhile; ?>
	<?php endif; ?>

<div class="uikit__container">

	<h1 class="uikit__heading">
		<div class="uikit__block">
			<span><?php echo esc_html( get_the_title() ); ?></span>
		</div>
	</h1>

	<div class="uikit__content">


		<section class="uikit__section" id="buttons">
			<h2 class="heading">Primary Button</h2>

			<div class="content">
				<?php
					echo wp_kses( $primary_link );
				?>
			</div><!--/.content-->

			<h2 class="heading">Secondary Button</h2>

			<div class="content">
				<?php
					echo wp_kses( $secondary_link );
				?>
			</div><!--/.content-->

		</section><!--/.uikit__section-->


	</div><!--/.uikit__content-->

</div><!--/.uikit__container-->

<?php get_footer(); ?>
