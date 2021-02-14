/**
 * Babel Config.
 *
 * @param {Object} api The bable API
 * @return {{presets: {Object}}} The babel configuration.
 */
module.exports = (api) => {
	/**
	 * @see https://babeljs.io/docs/en/config-files#apicache
	 */
	api.cache.using(() => process.env.NODE_ENV === 'development');

	/**
	 * Presets
	 *
	 * @see https://babeljs.io/docs/en/presets
	 * @type {Array}
	 */
	const presets = [
		[
			'@TenUp/babel-preset-default',
			{
				wordpress: true,
			},
		],
	];

	/**
	 * Plugins
	 *
	 * @see https://babeljs.io/docs/en/plugins
	 * @type {Array}
	 */
	const plugins = [];

	return {
		presets,
		plugins,
	};
};
