/**
 * Vite+ config for the 10up scaffold mu-plugin.
 *
 * Replaces the `10up-toolkit` (webpack) build with Vite+ and
 * `@10up/wp-vite-plugins`. Single script-pass build: one admin script and
 * one admin stylesheet, plus static assets. `wpExternals` (included by
 * `wp()`) handles any `@wordpress/*` imports and emits `.asset.php`
 * sidecars.
 */
import { defineConfig } from "vite-plus";
import { wp } from "@10up/wp-vite-plugins";

export default defineConfig({
	plugins: [
		wp({
			copyAssetsDir: "./assets",
		}),
	],
	build: {
		outDir: "dist",
		rollupOptions: {
			// 'css/admin-style', not 'css/admin': TenupFramework's
			// get_asset_info('admin') checks dist/js/admin.asset.php before
			// dist/css/admin.asset.php, so a same-named CSS entry silently
			// inherited the JS entry's version/deps instead of its own (the
			// style enqueue never busted cache on CSS-only edits).
			input: {
				"js/admin": "./assets/js/admin/admin.js",
				"css/admin-style": "./assets/css/admin/admin-style.css",
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
	},
});
