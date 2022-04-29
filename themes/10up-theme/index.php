<?php
/**
 * The main template file
 *
 * @package TenUpTheme
 */

get_header(); ?>

	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<div class="entry-content">
				<h2 class="entry-title"><?php the_title(); ?></h2>
				<?php the_content(); ?>
			</div>
		<?php endwhile; ?>
	<?php endif; ?>

<?php
get_footer();
