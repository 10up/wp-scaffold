const colors = require('tailwindcss/colors');

// Tailwind Plugins
const typographyPlugin = require('@tailwindcss/typography');
const { getThemeColors } = require('./includes/tailwind-helpers');
// const formsPlugin = require('@tailwindcss/forms');
// const aspectRatioPlugin = require('@tailwindcss/aspect-ratio');
// const lineClampPlugin = require('@tailwindcss/line-clamp');

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
		'attachment.php',
		'author.php',
		'category.php',
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
		'single.php',
		'singular.php',
		'tag.php',
		'taxonomy.php',

		// Directories
		'assets/**/*.js',
		'includes/**/*.{js, php}',
		'partials/**/*.php',
		'templates/**/*.php',
	],
	safelist: [
		{
			pattern: /(bg|text)-(primary|secondary|tertiary)/,
		},
	],
	theme: {
		/* Override the default theme */
		container: {
			center: true,
			padding: {
				default: '1.25rem',
				md: '2rem',
				lg: '2.5rem',
			},
		},
		/* Define brand colors using --wp custom properties from the theme.json */
		colors: {
			transparent: 'transparent',
			current: 'currentColor',
			...getThemeColors(),
			/* Include additional palette colors as appropriate to fill in gaps for brand colors */
			gray: colors.gray,
		},
		extend: {
			/* Extend the default theme */
			screens: {
				'wp-md': '600px' /* WP Specific Breakpoint used in Gutenberg */,
			},
			maxWidth: {
				/* Custom width properties width from the `theme.json` file */
				content: 'var(--wp--custom--width--content)',
				wide: 'var(--wp--custom--width--wide)',
			},
		},
	},
	/*
		Extend with 3rd party tailwind plugins or locally import your own
		Uncomment the plugins below and at the top of the file to easily add them to the config.
	*/
	plugins: [
		/*
			Add prose classes to provide sensible typography styles to longform content blocks
			See: https://tailwindcss.com/docs/plugins#typography
		*/
		typographyPlugin,
		/* Default form input styling
			See: https://tailwindcss.com/docs/plugins#forms
		*/
		// formsPlugin,
		/*
			Adds the ability to use classes like aspect-w-16 aspect-h-9 including arbitrary values
			See: https://tailwindcss.com/docs/plugins#aspect-ratio
		*/
		// aspectRatioPlugin,
		/*
			Adds line-clamp utilities, very handy for content heavy sites
			See: https://tailwindcss.com/docs/plugins#line-clamp
		*/
		// lineClampPlugin,
	],
};