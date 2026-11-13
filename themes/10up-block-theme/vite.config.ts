/**
 * Vite+ config for the 10up scaffold block theme.
 *
 * `npm run build` runs `wp-vite build`, which drives two passes over this
 * config into one dist/:
 *
 *   1. script pass (`vp build`)                 — classic scripts, all styles,
 *                                                 block.json + render.php
 *                                                 copying, blocks-manifest.php,
 *                                                 static assets.
 *   2. module pass (`vp build --mode modules`)  — Script Module entries only
 *                                                 (`scriptModule` /
 *                                                 `viewScriptModule`), additive.
 *
 * `npm run watch` rebuilds both passes on save. `npm run dev` starts one Vite
 * dev server with HMR; `dist/dev-server.php` (required from functions.php)
 * points WordPress at it while it runs.
 */
import { defineConfig } from 'vite-plus';
import { wp } from '@10up/wp-vite-plugins';

export default defineConfig(({ mode }) => {
	const isModulePass = mode === 'modules';

	return {
		plugins: [
			wp({
				buildType: isModulePass ? 'module' : 'script',
				blocksDir: './blocks',
				blocksStylesDir: './assets/css/blocks',
				copyAssetsDir: './assets',
			}),
		],
		build: {
			outDir: 'dist',
			// The script pass cleans dist/, the module pass adds to it.
			emptyOutDir: !isModulePass,
			rollupOptions: {
				// Theme-level entries (was the `10up-toolkit.entry` config).
				// Block entries are discovered from block.json by wpBlocks.
				// CSS files are first-class entries (instead of JS-side
				// imports) — wpCleanCssChunks removes the empty JS shims.
				input: isModulePass
					? ({} as Record<string, string>)
					: {
							'js/frontend': './assets/js/frontend.ts',
							'js/block-extensions': './assets/js/block-extensions.ts',
							'css/frontend-style': './assets/css/frontend.css',
							'css/editor-frame-style-overrides':
								'./assets/css/editor-frame-style-overrides.css',
							'css/editor-canvas-style-overrides':
								'./assets/css/editor-canvas-style-overrides.css',
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
			ignorePatterns: [
				'**/*.css',
				'**/*.html',
				'**/*.json',
				'**/*.md',
				'**/*.yml',
				'**/*.yaml',
			],
		},
		lint: {
			ignorePatterns: ['dist/**', 'vendor/**'],
			options: {
				typeAware: true,
				typeCheck: true,
			},
		},
	};
});
