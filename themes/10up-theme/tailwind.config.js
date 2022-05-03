const typographyPlugin = require('@tailwindcss/typography');
const formsPlugin = require('@tailwindcss/forms');
const aspectRatioPlugin = require('@tailwindcss/aspect-ratio');
const lineClampPlugin = require('@tailwindcss/line-clamp');

module.exports = {
	/**
	 * Tailwind Config File
	 * Every section of the config file is optional,
	 * so you only have to specify what you’d like to change.
	 * Any missing sections will fall back to Tailwind’s default configuration.
	 * https://github.com/tailwindlabs/tailwindcss/blob/master/stubs/defaultConfig.stub.js
	 */
	content: [
		/* Ensure changes to all PHP, JS, and JSON files rebuild your CSS */
		'404.php',
		'archive.php',
		'archive-*.php',
		'attachment.php',
		'author.php',
		'category.php',
		'category-*.php',
		'date.php',
		'footer.php',
		'front-page.php',
		'functions.php',
		'header.php',
		'home.php',
		'index.php',
		'page.php',
		'search.php',
		'searchform.php',
		'single-*.php',
		'single.php',
		'singular.php',
		'tag.php',
		'taxonomy.php',
		'taxonomy-*.php',

		// Directories
		'assets/**/*.js',
		'includes/**/*.{js, php}',
		'partials/**/*.php',
		'templates/**/*.php',
	],
	safelist: [
		/* Prevent editor-specific styles from being purged */
		'editor-post-title__block',
		'editor-post-title__input',
		'entry-content',
		'entry-title',
		'block-editor-block-list__layout',
	],
	theme: {
		/* Override the default theme */

		/* Define brand colors using --wp custom properties from the theme.json */
		colors: {
			transparent: 'transparent',
			current: 'currentColor',
			white: 'var(--wp--preset--color--white)',
			black: 'var(--wp--preset--color--black)',
		},
		extend: {
			/* Extend the default theme */

			maxWidth: {
				/* Custom width properties width from the `theme.json` file */
				content: 'var(--wp--custom--width--content)',
				wide: 'var(--wp--custom--width--wide)',
			},
		},
	},
	plugins: [
		/*
			Extend with 3rd party tailwind plugins or locally import your own
			All plugins are included by default since they are not output if unused.
		*/
		typographyPlugin,
		formsPlugin,
		aspectRatioPlugin,
		lineClampPlugin,
	],
};
