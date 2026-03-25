import { useBlockProps } from '@wordpress/block-editor';

export const BlockEdit = () => {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<strong>Example Block:</strong> Hello world
		</div>
	);
};
