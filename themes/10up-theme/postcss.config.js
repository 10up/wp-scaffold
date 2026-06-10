/**
 * PostCSS pipeline — replaces the 10up-toolkit defaults.
 *
 * `postcss-global-data` injects the shared `@custom-media` definitions into
 * every file (toolkit did this via its internal postcss config), and
 * `postcss-preset-env` resolves them plus nesting for the browserslist
 * targets.
 */
const { join } = require("node:path");

module.exports = {
	plugins: {
		"@csstools/postcss-global-data": {
			files: [join(__dirname, "assets/css/globals/media-queries.css")],
		},
		"postcss-preset-env": {
			stage: 2,
			features: {
				"custom-media-queries": true,
				"nesting-rules": true,
			},
		},
	},
};
