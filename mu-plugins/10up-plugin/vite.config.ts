/**
 * Vite+ config for the 10up scaffold mu-plugin.
 *
 * `npm run build` (`vp build`) builds one admin script, one admin stylesheet
 * and the static assets into dist/. `wp()` externalizes `@wordpress/*`
 * imports and writes `.asset.php` files. `npm run dev` (`vp dev`) serves the
 * same entries from a dev server through `dist/dev-server.php`.
 */
import tenup, { i18nRules } from '@10up/oxlint-config';
import { wp } from '@10up/wp-vite-plugins';
import { defineConfig } from 'vite-plus';

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
		// Oxfmt formats JS, TS and CSS (stylelint only lints CSS). Block-theme
		// HTML and JSON must match the Gutenberg serializer, so it leaves them alone.
		ignorePatterns: ['**/*.html', '**/*.json', '**/*.md', '**/*.yml', '**/*.yaml'],
	},
	lint: {
		// @10up/eslint-config/wordpress, for Oxlint.
		extends: [tenup],
		rules: {
			...i18nRules('tenup-plugin'),
		},
		ignorePatterns: ['dist/**', 'vendor/**'],
	},
});
