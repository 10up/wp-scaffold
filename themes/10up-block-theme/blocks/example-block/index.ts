import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';
import metadata from './block.json';
import { BlockEdit } from './edit';

const blockMeta = metadata as unknown as BlockConfiguration<Record<string, unknown>>;
const blockName = blockMeta.name as string;

registerBlockType(blockName, {
	...blockMeta,
	edit: BlockEdit,
	save: () => null,
});
