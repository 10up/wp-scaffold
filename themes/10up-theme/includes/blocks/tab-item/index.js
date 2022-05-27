import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks } from '@wordpress/block-editor';
import metadata from './block.json';

import { BlockEdit } from './edit';

const BlockSave = () => {
	return <InnerBlocks.Content />;
};

registerBlockType(metadata, {
	edit: BlockEdit,
	save: BlockSave,
});
