import { useHasSelectedInnerBlock } from '@10up/block-components';

import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { createSlotFill } from '@wordpress/components';
import {
	InnerBlocks,
	useBlockProps,
	useInnerBlocksProps,
	store as blockEditorStore,
	RichText,
} from '@wordpress/block-editor';
import { useEffect } from '@wordpress/element';
import { useInstanceId } from '@wordpress/compose';

const { Fill, Slot } = createSlotFill('Toolbar');

export const TabHeader = ({ children, tabsClientId }) => (
	<Fill name={`TabHeader-${tabsClientId}`}>{children}</Fill>
);

export const TabHeaderSlot = ({ tabsClientId }) => (
	<Slot name={`TabHeader-${tabsClientId}`} bubblesVirtually as="ul" className="tab-list" />
);

export const BlockEdit = (props) => {
	const { clientId, isSelected, attributes, setAttributes } = props;
	const { label, identifier } = attributes;
	const instanceId = useInstanceId(BlockEdit);

	useEffect(() => {
		if (!identifier) {
			setAttributes({ identifier: `tab-${instanceId}` });
		}
	}, [identifier, instanceId, setAttributes]);

	const parentBlocks = useSelect((select) => select(blockEditorStore).getBlockParents(clientId));
	const parentClientId = parentBlocks[parentBlocks.length - 1];
	const parentBlock = useSelect((select) => select(blockEditorStore).getBlock(parentClientId));
	const index = parentBlock.innerBlocks.findIndex((item) => item.clientId === clientId);
	const selectedClientId = useSelect((select) =>
		select(blockEditorStore).getSelectedBlockClientId(),
	);
	const hasSelectionWithinParent = useSelect((select) =>
		select(blockEditorStore).hasSelectedInnerBlock(parentClientId, true),
	);

	const hasChildBlockSelected = useHasSelectedInnerBlock(clientId);
	const hasSelection = isSelected || hasChildBlockSelected;

	const shouldShowInserter = selectedClientId === parentClientId || hasSelectionWithinParent;

	function maybeShowBlockAppender() {
		if (shouldShowInserter) {
			return <InnerBlocks.ButtonBlockAppender />;
		}

		return null;
	}

	const blockProps = useBlockProps();

	const innerBlocksProps = useInnerBlocksProps(
		{
			className: `tabs__tab-item tabs-content tab-${clientId}`,
		},
		{
			template: [['core/paragraph']],
			orientation: 'vertical',
			renderAppender: maybeShowBlockAppender,
		},
	);

	const isSelectedTab = hasSelection ? true : index === 0 && !hasSelectionWithinParent;

	return (
		<>
			<TabHeader tabsClientId={parentClientId}>
				<li
					className={`tab-item ${isSelectedTab ? 'is-active' : ''}`}
					role="presentation"
					style={{ order: index + 1 }}
				>
					<RichText
						tagName="span"
						withoutInteractiveFormatting
						className="tab-item__action"
						role="tab"
						value={label}
						placeholder={__('Tab Item', 'tenup')}
						onChange={(value) => setAttributes({ label: value })}
						allowedFormats={[]}
						aria-selected={isSelectedTab}
					/>
				</li>
			</TabHeader>
			{isSelectedTab && (
				<div {...blockProps}>
					<div className="tab-control">
						<TabHeaderSlot tabsClientId={parentClientId} />
					</div>
					<div className="tab-group">
						<div {...innerBlocksProps} />
					</div>
				</div>
			)}
		</>
	);
};
