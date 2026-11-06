/**
 * External dependencies
 */
const { join } = require('path');
const { existsSync, readFileSync } = require('fs');

/**
 * Default block namespace: the project slug.
 *
 * `npm run scaffold` renames the root package from `tenup-wp-scaffold` to the
 * project slug, so stripping the scaffold suffix gives `tenup` before the
 * project is scaffolded and e.g. `acme` after. The namespace prompt still
 * lets you override it (or pass `--namespace`).
 */
const namespace = require('../../package.json').name.replace(/-wp-scaffold$/, '');

/**
 * Reads the text domain of the theme the block is created in.
 *
 * `scaffold:block` runs from `themes/<theme>/blocks/`, so the theme's
 * `style.css` header is one level up. Falls back to the block namespace.
 *
 * @param {string} fallback Text domain to use when none is found.
 * @returns {string} Text domain.
 */
const getThemeTextDomain = (fallback) => {
	const stylesheet = join(process.cwd(), '..', 'style.css');
	if (!existsSync(stylesheet)) {
		return fallback;
	}
	const match = readFileSync(stylesheet, 'utf8').match(/^\s*\*?\s*Text Domain:\s*(\S+)/m);
	return match ? match[1] : fallback;
};

module.exports = {
	defaultValues: {
		slug: 'example-block',
		namespace,
		category: 'text',
		title: 'Example Block',
		description: 'Example Block',
		attributes: {
			content: {
				type: 'string',
				role: 'content',
				default: '',
			},
		},
		example: {
			attributes: {
				content: 'Example content.',
			},
		},
		supports: {
			html: false,
			interactivity: {
				clientNavigation: true,
			},
		},
		wpScripts: false,
		wpEnv: false,
		version: false,
		folderName: './example-block',
		editorScript: 'file:./index.ts',
		render: 'file:./render.php',
		editorStyle: false,
		style: 'file:./style.css',
		transformer: (view) => ({
			...view,
			textdomain: getThemeTextDomain(view.namespace),
		}),
	},
	variants: {
		// Server-rendered block with an inline-editable text field.
		default: {},
		// Container block: editors add inner blocks, render.php outputs them.
		innerBlocks: {
			attributes: {},
			example: {
				innerBlocks: [
					{
						name: 'core/paragraph',
						attributes: { content: 'Example content.' },
					},
				],
			},
			// The inline `save: () => <InnerBlocks.Content />` needs JSX.
			editorScript: 'file:./index.tsx',
		},
		// Frontend behavior through an Interactivity API store (view.ts).
		interactive: {
			supports: {
				html: false,
				interactivity: {
					interactive: true,
					clientNavigation: true,
				},
			},
			viewScriptModule: 'file:./view.ts',
		},
	},
	blockTemplatesPath: join(__dirname, 'block-templates'),
};
