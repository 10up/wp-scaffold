import { useHasSelectedInnerBlock } from '@10up/block-components';
import {
	useBlockProps,
	useInnerBlocksProps,
	store as blockEditorStore,
	InnerBlocks,
} from '@wordpress/block-editor';
import { useSelect, useDispatch } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { TabHeader } from '../tab-item/edit';

export const TAB_ITEM_BLOCK = 'tenup/tab-item';

export const BlockEdit = (props) => {
	const { clientId, isSelected } = props;
	const maxTabs = 3;

	const innerBlocks = useSelect(
		(select) => select(blockEditorStore).getBlock(clientId).innerBlocks,
	);

	const isChildSelected = useHasSelectedInnerBlock(clientId);

	const { insertBlock, selectBlock } = useDispatch(blockEditorStore);

	const appendTabItem = () => {
		const block = createBlock(TAB_ITEM_BLOCK, {}, [createBlock('core/paragraph')]);
		insertBlock(block, innerBlocks.length, clientId, false);
		selectBlock(block.clientId);
	};

	const hasSelection = isSelected || isChildSelected;
	const canAddMoreTabs = maxTabs > innerBlocks.length;
	const showInserter = hasSelection && canAddMoreTabs;

	const blockProps = useBlockProps({
		className: `tabs tabs--horizontal ${showInserter ? 'has-inserter' : ''}`,
	});
	const innerBlockProps = useInnerBlocksProps(blockProps, {
		orientation: 'horizontal',
		allowedBlocks: [TAB_ITEM_BLOCK],
		template: [[TAB_ITEM_BLOCK, {}, [['core/paragraph']]]],
		renderAppender: () => false,
	});

	return (
		<>
			<TabHeader tabsClientId={clientId}>
				{showInserter && (
					<li
						className="tab-item tab-item--inserter"
						role="presentation"
						style={{ order: innerBlocks.length + 1 }}
					>
						<InnerBlocks.ButtonBlockAppender
							className="tab-item__action"
							role="tab"
							icon="plus"
							title={__('Add new Tab', 'tenup')}
							onClick={appendTabItem}
						/>
					</li>
				)}
			</TabHeader>
			<div {...innerBlockProps} />
		</>
	);
};
