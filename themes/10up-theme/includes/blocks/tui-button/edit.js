/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Edit component.
 * See https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#edit
 *
 * @param {object}   props                  The block props.
 * @param {object}   props.attributes       Block attributes.
 * @param {string}   props.attributes.title Custom title to be displayed.
 * @param {string}   props.className        Class name for the block.
 * @param {Function} props.setAttributes    Sets the value for block attributes.
 * @returns {Function} Render the edit screen
 */
const Edit = (props) => {
	const { attributes, setAttributes } = props;
	const { label } = attributes;

	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<RichText
				tagName="h2"
				placeholder={__('Learn more')}
				value={label}
				onChange={(label) => setAttributes({ label })}
			/>
			<ServerSideRender
				block="tenup/tui-button"
				attributes={{
					label,
				}}
			/>
		</div>
	);
};
export default Edit;
