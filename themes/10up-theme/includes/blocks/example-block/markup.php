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
			'title' => __( 'Custom title default', 'tenup' ),
		],
	]
);

$wrapper_attributes = get_block_wrapper_attributes();

?>
<div <?php echo wp_kses_post( $wrapper_attributes ); ?>>
	<h2 class="wp-block-example-block__title">
		<?php echo wp_kses_post( $args['attributes']['title'] ); ?>
	</h2>
</div>
