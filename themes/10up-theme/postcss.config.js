/* eslint-disable global-require */

module.exports = {
	// Only load postcss-editor-styles plugin when we're processing the editor-style.css file.
	plugins: [
		require('postcss-import'),
		require('tailwindcss/nesting')('postcss-nesting'),
		require('tailwindcss'),
		require('postcss-preset-env')({
			stage: 0,
			features: {
				'custom-properties': false,
				'nesting-rules': false,
				'is-pseudo-class': {
					onComplexSelector: 'ignore',
				},
			},
		}),
	],
};
