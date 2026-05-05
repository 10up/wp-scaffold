/**
 * External dependencies
 */
const { join } = require('path');

module.exports = {
	defaultValues: {
		slug: 'example-block',
		category: 'text',
		title: 'Example Block',
		description: 'Example Block',
		attributes: {},
		supports: {
			html: false,
		},
		customBlockJSON: {
			textdomain: 'tenup',
		},
		namespace: 'tenup',
		wpScripts: false,
		wpEnv: false,
		version: false,
		folderName: './example-block',
		editorScript: 'file:./index.tsx',
		render: 'file:./markup.php',
		editorStyle: false,
		style: 'file:./style.css',
	},
	variants: {
		default: {},
		innerBlocks: {},
		withViewScript: {
			viewScript: 'file:./view.ts',
		},
	},
	blockTemplatesPath: join(__dirname, 'block-templates'),
};
