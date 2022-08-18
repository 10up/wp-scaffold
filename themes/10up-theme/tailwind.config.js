const colors = require('tailwindcss/colors');
const purgecssWordpress = require('purgecss-with-wordpress');

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
	corePlugins: {
		/**
		 * Preflight injects this file https://github.com/tailwindlabs/tailwindcss/blob/master/src/css/preflight.css which
		 * would normally be fine but Gutenberg has issues with styles to raw buttons and anchors, and they need to be
		 * prefixed with :where(:not(.components-button)) and where(:not(.components-external-link)) respectively so the
		 * styles don't end up leaking out of the editor.
		 *
		 */
		preflight: false,
	},
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
	safelist: [...purgecssWordpress.safelist],
	theme: {
		/* Override the default theme */
		container: {
			center: true,
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
				/* WP Specific Breakpoints used in Gutenberg/Admin-bar */
				'wp-sm': '600px',
				'wp-md': { min: '601px', max: '783px' },
				'wp-lg': '784px',
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
			You may remove this plugin if you're not using the `.prose-*` tailwind classes
			for long-form content.
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
