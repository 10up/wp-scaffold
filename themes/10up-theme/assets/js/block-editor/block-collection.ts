/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockCollection } from '@wordpress/blocks';

/**
 * Register block collection.
 * See https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/#registerblockcollection
 */
registerBlockCollection('tenup', {
	title: __('Custom Blocks', 'tenup-theme'),
});
