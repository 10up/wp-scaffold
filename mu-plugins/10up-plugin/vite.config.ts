/**
 * Vite+ config for the 10up scaffold mu-plugin.
 *
 * Single script-pass build (`wp-vite build`): one admin script, one admin
 * stylesheet, plus static assets. `wpExternals` (included by `wp()`) handles
 * any `@wordpress/*` imports and emits `.asset.php` sidecars. `npm run dev`
 * serves the same entries from a Vite dev server through
 * `dist/dev-server.php`.
 */
import { defineConfig } from 'vite-plus';
import { wp } from '@10up/wp-vite-plugins';

export default defineConfig({
	plugins: [
		wp({
			copyAssetsDir: './assets',
		}),
	],
	build: {
		outDir: 'dist',
		rollupOptions: {
			input: {
				'js/admin': './assets/js/admin/admin.js',
				'css/admin-style': './assets/css/admin/admin-style.css',
			},
			output: {
				entryFileNames: '[name].js',
				assetFileNames: '[name][extname]',
			},
		},
	},
	fmt: {
		// 10up house style — the same options @10up/eslint-config passed to Prettier.
		useTabs: true,
		tabWidth: 4,
		printWidth: 100,
		singleQuote: true,
		trailingComma: 'all',
		bracketSpacing: true,
		semi: true,
		arrowParens: 'always',
		// CSS is owned by stylelint (10up config); block-theme HTML/JSON must
		// match the Gutenberg serializer, so oxfmt only handles JS and TS.
		ignorePatterns: ['**/*.css', '**/*.html', '**/*.json', '**/*.md', '**/*.yml', '**/*.yaml'],
	},
	lint: {
		ignorePatterns: ['dist/**', 'vendor/**'],
	},
});
