<?php
/**
 * Gutenberg Blocks setup
 *
 * @package TenUpTheme\Blocks\Example
 */

namespace TenUpTheme\Blocks\Example;

/**
 * Whether or not this block should be available to select.
 *
 * @param \WP_Post $post The current post object.
 *
 * @return bool
 */
function allow_block( $post ) {
	return true;
}

/**
 * Register the block
 */
function register() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	// Register the block.
	$result = register_block_type_from_metadata(
		TENUP_THEME_BLOCK_DIR . '/example-block', // this is the directory where the block.json is found.
		[
			'render_callback' => $n( 'render_block_callback' ),
		]
	);

	// If we got a successful registration, filter the allowed blocks.
	if ( $result instanceof \WP_Block_type ) {
		add_filter(
			'allowed_block_types',
			function( $allowed_block_types, $post ) use ( $n, $result ) {
				// If the allow_block function returns true, add the block to the allow list.
				if ( $n( 'allow_block' )( $post ) ) {
					$allowed_block_types[] = $result->name;
				}

				return $allowed_block_types;
			},
			20,
			2
		);
	}
}

/**
 * Render callback method for the block
 *
 * @param array  $attributes The blocks attributes
 * @param string $content    Data returned from InnerBlocks.Content
 * @param array  $block      Block information such as context.
 *
 * @return string The rendered block markup.
 */
function render_block_callback( $attributes, $content, $block ) {
	ob_start();
	get_template_part(
		'includes/blocks/example-block/markup',
		null,
		[
			'class_name' => 'wp-block-tenup-example',
			'attributes' => $attributes,
			'content'    => $content,
			'block'      => $block,
		]
	);

	return ob_get_clean();
}
