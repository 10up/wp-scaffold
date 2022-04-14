<?php
/**
 * Example block markup
 *
 * @package TenUpTheme\Blocks\Example
 *
 * @var array    $attributes         Block attributes.
 * @var string   $content            Block content.
 * @var WP_Block $block              Block instance.
 * @var array    $context            BLock context.
 * @var string   $class_name         Generated class name for concatenation.
 */

?>
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore ?>">
	<h2 class="<?php echo sanitize_html_class( $class_name ); ?>__title">
		<?php echo wp_kses_post( $attributes['title'] ); ?>
	</h2>
</div>
