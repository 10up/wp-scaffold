/* eslint-disable global-require */

module.exports = () => {
	const config = {
		plugins: [],
	};

	// Only load postcss-editor-styles plugin when we're processing the editor-style.css file.

	config.plugins.push(require('postcss-import'));
	config.plugins.push(require('tailwindcss/nesting')('postcss-nesting'));
	config.plugins.push(require('tailwindcss'));
	config.plugins.push(
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
	);

	return config;
};
