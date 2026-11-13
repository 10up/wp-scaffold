/**
 * Vite+ config for the 10up scaffold theme.
 *
 * `npm run build` (`vp build`) builds into dist/: classic scripts and all
 * styles, and the Script Modules blocks declare (`scriptModule` /
 * `viewScriptModule`), along with the block.json and render.php copies,
 * blocks-manifest.php and static assets. `npm run watch` rebuilds on save.
 * `npm run dev` (`vp dev`) starts a dev server with HMR; `dist/dev-server.php`
 * (required from functions.php) points WordPress at it while it runs.
 */
import tenup, { i18nRules } from '@10up/oxlint-config';
import { wp } from '@10up/wp-vite-plugins';
import { defineConfig } from 'vite-plus';

export default defineConfig({
	plugins: [
		wp({
			blocksDir: './blocks',
			blocksStylesDir: './assets/css/blocks',
			copyAssetsDir: './assets',
			// Custom media, custom selectors and mixins every stylesheet can use.
			css: { globals: ['assets/css/globals/*.css', 'assets/css/mixins/*.css'] },
		}),
	],
	build: {
		outDir: 'dist',
		rollupOptions: {
			// Theme-level entries (was the `10up-toolkit.entry` config). Block
			// entries are discovered from block.json. CSS files are entries of
			// their own; wp() removes the empty JS files Vite emits for them.
			input: {
				'js/frontend': './assets/js/frontend/frontend.ts',
				'js/block-editor-script': './assets/js/block-editor/block-editor-script.ts',
				'css/frontend-style': './assets/css/frontend/style.css',
				'css/editor-style-overrides': './assets/css/frontend/editor-style-overrides.css',
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
			...i18nRules('tenup-theme'),
		},
		ignorePatterns: ['dist/**', 'vendor/**'],
		options: {
			typeAware: true,
			typeCheck: true,
		},
	},
});
