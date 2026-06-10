/**
 * Workspace-root Vite+ config.
 *
 * The root package is not a Vite app — this config only carries the
 * repo-wide Vite+ tooling configuration:
 *
 *   - `staged`: pre-commit checks run by `vp staged` (replaces lint-staged).
 *   - `lint`:   ignore patterns for `vp lint` / `vp check` when run from
 *               the repo root. Per-workspace configs handle the rest.
 *
 * Build configuration lives in each workspace's own vite.config.ts.
 */
import { defineConfig } from "vite-plus";

export default defineConfig({
	staged: {
		"*.{js,jsx,ts,tsx}": "vp check --fix",
		"*.css": "stylelint --fix",
		"*.php": "./vendor/bin/phpcs",
	},
	fmt: {
		// CSS is owned by stylelint (10up config); block-theme HTML/JSON must
		// match the Gutenberg serializer, so oxfmt only handles JS/TS/MD.
		ignorePatterns: ["**/*.css", "**/*.html", "**/*.json", "**/LICENSE.md"],
	},
	lint: {
		ignorePatterns: [
			"**/dist/**",
			"**/vendor/**",
			"**/node_modules/**",
			"plugins/**",
			"upgrade/**",
			"uploads/**",
		],
	},
});
