<?php
/**
 * Server-side rendering of the Chart Tabs Gutenberg Block
 *
 * @package tenup-theme
 */

$label      = $attributes['label'];
$identifier = $attributes['identifier'];

if ( empty( $label ) ) {
	return '';
}

$tab_id = sanitize_title( $label ) . $identifier;

$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => 'tabs__tab-item tabs-content' ] );

?>

<div <?php echo $wrapper_attributes; //phpcs:disable ?> id="<?php echo esc_attr( $tab_id ); ?>" role="tabpanel" aria-labelledby="tab-<?php echo esc_attr( $tab_id ); ?>" aria-hidden="true">
	<?php
		/*
		* the content is the html generated from innerBlocks
		* it is being created from the save method in JS or the render_callback
		* in php and is sanitized.
		*
		* Re sanitizing it through `wp_kses_post` causes
		* embed blocks to break and other core filters don't apply.
		* therefore no additional sanitization is done and it is being output as is
		*/
		echo $content; //phpcs:disable
	?>
</div>
