<?php
/**
 * tenup markup
 *
 * @package tenup\Blocks\tenup
 *
 * @var array    $attributes         Block attributes.
 * @var string   $content            Block content.
 * @var WP_Block $block              Block instance.
 */

$title      = $attributes['title'];
$cta_text   = $attributes['ctaText'];
$cta_link   = $attributes['ctaLink'];
$cta_target = $attributes['ctaTarget'];
$image_id   = $attributes['imageId'];

$additional_classes = [];

if ( ! empty( $image_id ) ) {
	$additional_classes[] = 'has-image';
}

$additional_classes = array_filter( $additional_classes );
$additional_classes = array_map( 'sanitize_html_class', $additional_classes );
$additional_classes = implode( ' ', $additional_classes );

?>

<section <?php echo get_block_wrapper_attributes( [ 'class' => $additional_classes ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<h2 class="wp-block-tenup-call-to-action__heading">
		<?php echo wp_kses_post( $title ); ?>
	</h2>

	<a class="wp-block-tenup-call-to-action__link" href="<?php echo esc_url( $cta_link ); ?>" target="<?php echo esc_attr( $cta_target ); ?>">
		<?php echo wp_kses_post( $cta_text ); ?>
	</a>

	<?php if ( ! empty( $image_id ) ) : ?>
		<figure class="wp-block-tenup-call-to-action__media">
			<?php echo wp_get_attachment_image( $image_id, 'full', false, [ 'class' => 'wp-block-tenup-call-to-action__image' ] ); ?>
		</figure>
	<?php endif; ?>
</section>
