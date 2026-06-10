/**
 * Vite+ config for the 10up scaffold block theme.
 *
 * Replaces the `10up-toolkit` (webpack) build with Vite+ and
 * `@10up/wp-vite-plugins`. The theme runs a dual-pass build:
 *
 *   1. `vp build`                — script pass: classic scripts, all styles,
 *                                  block.json copying, blocks-manifest.php,
 *                                  static assets.
 *   2. `vp build --mode modules` — module pass: Script Module entries only
 *                                  (`scriptModule` / `viewScriptModule`),
 *                                  additive into the same dist/. Run via
 *                                  `bin/build-script-modules.mjs`, which
 *                                  skips the pass when no block declares a
 *                                  Script Module.
 */
import { defineConfig } from "vite-plus";
import { wp } from "@10up/wp-vite-plugins";

export default defineConfig(({ mode }) => {
	const isModulePass = mode === "modules";

	return {
		plugins: [
			wp({
				buildType: isModulePass ? "module" : "script",
				blocksDir: "./blocks",
				blocksStylesDir: "./assets/css/blocks",
				copyAssetsDir: "./assets",
			}),
		],
		build: {
			outDir: "dist",
			// The script pass cleans dist/, the module pass adds to it.
			emptyOutDir: !isModulePass,
			rollupOptions: {
				// Theme-level entries (was the `10up-toolkit.entry` config).
				// Block entries are discovered from block.json by wpBlocks.
				// CSS files are first-class entries (instead of JS-side
				// imports) — wpCleanCssChunks removes the empty JS shims.
				input: isModulePass
					? {}
					: {
							"js/frontend": "./assets/js/frontend.ts",
							"js/block-extensions": "./assets/js/block-extensions.ts",
							"css/frontend": "./assets/css/frontend.css",
							"css/editor-frame-style-overrides": "./assets/css/editor-frame-style-overrides.css",
							"css/editor-canvas-style-overrides": "./assets/css/editor-canvas-style-overrides.css",
						},
				output: {
					entryFileNames: "[name].js",
					assetFileNames: "[name][extname]",
				},
			},
		},
		fmt: {
			// CSS is owned by stylelint (10up config); block-theme HTML/JSON must
			// match the Gutenberg serializer, so oxfmt only handles JS/TS/MD.
			ignorePatterns: ["**/*.css", "**/*.html", "**/*.json", "**/LICENSE.md"],
		},
		lint: {
			ignorePatterns: ["dist/**", "vendor/**"],
			options: {
				typeAware: true,
				typeCheck: true,
			},
		},
	};
});
