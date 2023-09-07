import { useBlockProps } from '@wordpress/block-editor';

export const BlockEdit = (props) => {
	const { attributes, setAttributes } = props;
	const blockProps = useBlockProps();
	return (
		<div {...blockProps}>
		</div>
	);
};
