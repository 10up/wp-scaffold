const typographyPlugin = require('@tailwindcss/typography');
const colors = require('tailwindcss/colors');
const themeJSON = require('./theme.json');

function getThemePalette() {
	const { palette } = themeJSON.settings.color;

	return Array.isArray(palette) ? palette : [];
}

function getThemeColors() {
	const palette = getThemePalette();

	if (!palette) return [];

	const colors = {};

	palette.forEach((paletteColor) => {
		const { slug, color } = paletteColor;

		colors[slug] = color;
	});

	return colors;
}

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
	plugins: [
		/*
			Extend with 3rd party tailwind plugins or locally import your own
			All plugins are included by default since they are not output if unused.
		*/
		typographyPlugin,
	],
};
