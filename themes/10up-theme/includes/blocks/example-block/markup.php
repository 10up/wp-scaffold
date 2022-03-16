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
 * @var string   $wrapper_attributes Block Wrapper Attributes. To be applied to the outermost element.
 */

?>
<div <?php echo wp_kses_post( $wrapper_attributes ); ?>">
	<h2 class="<?php echo sanitize_html_class( $class_name ); ?>__title">
		<?php echo wp_kses_post( $attributes['title'] ); ?>
	</h2>
</div>
