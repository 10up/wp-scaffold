/**
 * PostCSS pipeline — replaces the 10up-toolkit defaults.
 *
 * `postcss-global-data` injects the shared `@custom-media` definitions,
 * `postcss-mixins` loads the global `@define-mixin` files, and
 * `postcss-preset-env` resolves custom media plus nesting for the
 * browserslist targets.
 */
const { join } = require("node:path");

module.exports = {
	plugins: {
		"postcss-mixins": {
			mixinsFiles: join(__dirname, "assets/css/mixins", "*.css"),
		},
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
