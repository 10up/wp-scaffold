<?php
/**
 * Example block markup
 *
 * @package TenUpScaffold\Blocks\Example
 *
 * @var array $args {
 *     $args is provided by get_template_part.
 *
 *     @type array $attributes Block attributes.
 *     @type array $content    Block content.
 *     @type array $block      Block instance.
 * }
 */

// Set defaults.
$args = wp_parse_args(
	$args,
	[
		'attributes' => [
			'customTitle' => __( 'Custom title default', 'tenup' ),
		],
		'class_name' => 'wp-block-tenup-example',
	]
);

?>
<div class="<?php echo esc_attr( $args['class_name'] ); ?>">
	<h2 class="wp-block-example-block__title">
		<?php echo wp_kses_post( $args['attributes']['customTitle'] ); ?>
	</h2>
</div>
