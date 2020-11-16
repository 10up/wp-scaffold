<?php
/**
 * The template for displaying search results pages.
 *
 * @package TenUpTheme
 */

get_header(); ?>

	<section itemscope itemtype="https://schema.org/SearchResultsPage">
		<?php if ( have_posts() ) : ?>
			<h1>
				<?php
				/* translators: the search query */
				printf( esc_html__( 'Search Results for: %s', 'tenup-theme' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
				?>
			</h1>

			<ul>
			<?php
			while ( have_posts() ) :
				the_post();
				?>

				<li itemscope itemtype="https://schema.org/Thing">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail();
					}

					the_title( '<span itemprop="name"><a href="' . esc_url( get_permalink() ) . '" itemprop="url">', '</a></span>' );
					?>
					<div itemprop="description">
						<?php the_excerpt(); ?>
					</div>
				</li>

			<?php endwhile; ?>
			</ul>

			<?php the_posts_navigation(); ?>
		<?php endif; ?>
	</section>

<?php
get_footer();
