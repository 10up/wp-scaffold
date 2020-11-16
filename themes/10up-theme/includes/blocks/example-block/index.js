/**
 * Example block
 */

/* eslint-disable react/prop-types */

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

registerBlockType('tenup/example-block', {
	title: __('Example Block', 'tenup-theme'),
	category: 'tenup-theme-blocks',
	icon: 'smiley',
	edit: ({ className }) => {
		return (
			<div className={className}>
				<h1>Example Block Editor</h1>
			</div>
		);
	},
	save: () => null,
});
