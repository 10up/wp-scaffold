<?php
/**
 * Example block markup
 *
 * @package TenUpScaffold\Blocks\TuiButton
 *
 * @var array    $attributes         Block attributes.
 * @var string   $content            Block content.
 * @var WP_Block $block              Block instance.
 * @var array    $context            BLock context.
 * @var string   $class_name         Generated class name for concatenation.
 * @var string   $wrapper_attributes Block Wrapper Attributes. To be applied to the outermost element.
 */

use function UIKit\Helpers\get_component;
$button = get_component( 'button', $attributes );
?>
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore ?>">
	<?php echo wp_kses_post( $button ); ?>
</div>
