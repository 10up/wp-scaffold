import { useHasSelectedInnerBlock } from '@10up/block-components';

import { useSelect, useDispatch } from '@wordpress/data';
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
import classnames from 'classnames';

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

	const { __unstableMarkNextChangeAsNotPersistent: markNextChangeAsNotPersistent = () => {} } =
		useDispatch(blockEditorStore);

	useEffect(() => {
		if (!identifier) {
			markNextChangeAsNotPersistent();
			setAttributes({ identifier: `tab-${instanceId}` });
		}
	}, [identifier, instanceId, setAttributes, markNextChangeAsNotPersistent]);

	const parentBlocks = useSelect((select) => select(blockEditorStore).getBlockParents(clientId));
	const parentBlockClientId = parentBlocks[parentBlocks.length - 1];
	const parentBlock = useSelect((select) =>
		select(blockEditorStore).getBlock(parentBlockClientId),
	);
	const selectedClientId = useSelect((select) =>
		select(blockEditorStore).getSelectedBlockClientId(),
	);
	const hasSelectionWithinParent = useSelect((select) =>
		select(blockEditorStore).hasSelectedInnerBlock(parentBlockClientId, true),
	);

	const hasChildBlockSelected = useHasSelectedInnerBlock({ clientId });
	const hasSelection = isSelected || hasChildBlockSelected;

	const shouldShowInserter = selectedClientId === parentBlockClientId || hasSelectionWithinParent;

	function maybeShowBlockAppender() {
		if (shouldShowInserter) {
			return <InnerBlocks.ButtonBlockAppender />;
		}

		return null;
	}

	const innerBlocksProps = useInnerBlocksProps(
		{
			className: classnames('tabs__tab-item', 'tabs-content', `tab-${clientId}`),
		},
		{
			template: [['core/paragraph']],
			orientation: 'vertical',
			renderAppender: maybeShowBlockAppender,
		},
	);

	const blockIndex = parentBlock.innerBlocks.findIndex((item) => item.clientId === clientId);
	const isFirstTab = blockIndex === 0;
	const maybeShouldShowBlockEvenIfNotSelected = isFirstTab && !hasSelectionWithinParent;
	const isSelectedTab = hasSelection ? true : maybeShouldShowBlockEvenIfNotSelected;

	const blockProps = useBlockProps({ className: classnames({ 'is-active': isSelected }) });

	return (
		<>
			<TabHeader tabsClientId={parentBlockClientId}>
				<li
					className={classnames('tab-item', { 'is-active': isSelectedTab })}
					role="presentation"
					style={{ order: blockIndex + 1 }}
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
						<TabHeaderSlot tabsClientId={parentBlockClientId} />
					</div>
					<div className="tab-group">
						<div {...innerBlocksProps} />
					</div>
				</div>
			)}
		</>
	);
};
