<?php
/**
 * Example block
 *
 * @package TenUpTheme
 */

namespace TenUpTheme\Blocks\ExampleBlock;

/**
 * Register example block
 *
 * @return void
 */
function register() {
	register_block_type(
		'tenup/example-block',
		[
			'render_callback' => __NAMESPACE__ . '\render',
		]
	);
}

/**
 * Render example block
 *
 * @return string
 */
function render() {
	return '<h1>Example block front end</h1>';
}
