<?php
/**
 * The main template file
 *
 * @package TenUpTheme
 */

get_header(); ?>

	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<h2><?php the_title(); ?></h2>
			<?php the_content(); ?>
		<?php endwhile; ?>
	<?php endif; ?>

<?php

echo render_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	[
		'blockName' => 'tenup/call-to-action',
		'attrs'     => [
			'title'     => 'Call to Action',
			'ctaText'   => 'Click Me',
			'ctaLink'   => 'https://10up.com',
			'ctaTarget' => '_blank',
			'imageId'   => 0,
		],
	]
);

get_footer();
