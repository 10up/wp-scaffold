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
		folderName: './src/blocks/example-block',
		render: 'file:./markup.php',
		editorStyle: false,
		style: 'file:./style.css',
	},
	variants: {
		default: {},
		innerBlocks: {},
		withViewScript: {
			viewScript: 'file:./view.js',
		},
	},
	blockTemplatesPath: join(__dirname, 'block-templates'),
};
