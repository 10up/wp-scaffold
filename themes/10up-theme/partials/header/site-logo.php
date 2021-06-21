<?php
/**
 * Site logo
 *
 * @package TenUpTheme
 */

?>

<div class="site-logo" itemscope itemtype="https://schema.org/Organization">
	<?php if ( has_custom_logo() ) : ?>
		<a itemprop="url" href="<?php echo esc_url( home_url() ); ?>" rel="home">
			<img itemprop="logo" src="<?php echo esc_url( wp_get_attachment_url( get_theme_mod( 'custom_logo' ) ) ); ?>" alt="" />
			<span class="screen-reader-text"><?php esc_html( get_bloginfo( 'name' ) ); ?></span>
		</a>
	<?php else : ?>
		<h1><?php echo esc_html( $blog_info ); ?></h1>
	<?php endif; ?>
</div>
