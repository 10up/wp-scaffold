/**
 * Example-block
 * Custom title block -- feel free to delete
 */

/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import block from './block.json';

/**
 * Register block
 */
registerBlockType(block, {
	edit,
	save,
});
