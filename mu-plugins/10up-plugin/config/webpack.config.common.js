const path = require('path');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const StyleLintPlugin = require('stylelint-webpack-plugin');
const WebpackBar = require('webpackbar');
const ImageminPlugin = require('imagemin-webpack-plugin').default;

const isProduction = process.env.NODE_ENV === 'production';

// Config files.
const settings = require('./webpack.settings.js');

/**
 * Configure entries.
 *
 * @return {Object[]} Array of webpack settings.
 */
const configureEntries = () => {
	const entries = {};

	for (const [key, value] of Object.entries(settings.entries)) {
		entries[key] = path.resolve(process.cwd(), value);
	}

	return entries;
};

module.exports = {
	entry: configureEntries(),
	output: {
		path: path.resolve(process.cwd(), settings.paths.dist.base),
		filename: settings.filename.js,
		/**
		 * If multiple webpack runtimes (from different compilations) are used on the same webpage,
		 * there is a risk of conflicts of on-demand chunks in the global namespace.
		 *
		 * @see (@link https://webpack.js.org/configuration/output/#outputjsonpfunction)
		 */
		jsonpFunction: '__TenUpPlugin_webpackJsonp',
	},

	// Console stats output.
	// @link https://webpack.js.org/configuration/stats/#stats
	stats: settings.stats,

	// External objects.
	externals: {
		jquery: 'jQuery',
		lodash: 'lodash',
	},

	// Performance settings.
	performance: {
		maxAssetSize: settings.performance.maxAssetSize,
	},

	// Build rules to handle asset files.
	module: {
		rules: [
			// Lint JS.
			{
				test: /\.js$/,
				enforce: 'pre',
				loader: 'eslint-loader',
				options: {
					fix: true,
				},
			},

			// Scripts.
			{
				test: /\.js$/,
				exclude: /node_modules(?!\/@TenUp)/,
				use: [
					{
						loader: 'babel-loader',
						options: {
							cacheDirectory: true,
							sourceMap: !isProduction,
						},
					},
				],
			},

			// Styles.
			{
				test: /\.css$/,
				include: path.resolve(process.cwd(), settings.paths.src.css),
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
					},
					{
						loader: 'css-loader',
						options: {
							sourceMap: !isProduction,
							// We copy fonts etc. using CopyWebpackPlugin.
							url: false,
						},
					},
					{
						loader: 'postcss-loader',
						options: {
							sourceMap: !isProduction,
						},
					},
				],
			},
		],
	},

	plugins: [
		// Remove the extra JS files Webpack creates for CSS entries.
		// This should be fixed in Webpack 5.
		new FixStyleOnlyEntriesPlugin({
			silent: true,
		}),

		// Clean the `dist` folder on build.
		new CleanWebpackPlugin({
			cleanStaleWebpackAssets: false,
		}),

		// Extract CSS into individual files.
		new MiniCssExtractPlugin({
			filename: settings.filename.css,
			chunkFilename: '[id].css',
		}),

		// Copy static assets to the `dist` folder.
		new CopyWebpackPlugin([
			{
				from: settings.copyWebpackConfig.from,
				to: settings.copyWebpackConfig.to,
				context: path.resolve(process.cwd(), settings.paths.src.base),
			},
		]),

		// Compress images
		// Must happen after CopyWebpackPlugin
		new ImageminPlugin({
			disable: !isProduction,
			test: settings.ImageminPlugin.test,
		}),

		// Lint CSS.
		new StyleLintPlugin({
			context: path.resolve(process.cwd(), settings.paths.src.css),
			files: '**/*.css',
		}),

		// Fancy WebpackBar.
		new WebpackBar(),
	],
};
