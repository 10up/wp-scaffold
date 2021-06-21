<?php
/**
 * Site Navigation
 *
 * @package TenUpTheme
 */

?>

<?php if ( has_nav_menu( 'primary' ) ) : ?>
	<nav class="site-navigation" role="navigation" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement">

		<a href="#primary-nav" aria-controls="primary-nav" class="site-menu-toggle">
			<span class="screen-reader-text"><?php echo esc_html_e( 'Primary Navigation', 'tenup-theme' ); ?></span>
			<span aria-hidden="true">☰</span>
		</a>

		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'menu_class'     => 'primary-menu',
				'menu_id'        => 'primary-nav',
			)
		);
		?>
	</nav>
<?php endif; ?>
