/**
 * Workspace-root Vite+ config.
 *
 * The root package is not a Vite app — this config only carries the
 * repo-wide Vite+ tooling configuration:
 *
 *   - `staged`: pre-commit checks run by `vp staged` (replaces lint-staged).
 *   - `fmt`:    what oxfmt leaves alone, repo-wide.
 *   - `lint`:   ignore patterns for `vp lint` / `vp check` when run from
 *               the repo root. Per-workspace configs handle the rest.
 *
 * Build configuration lives in each workspace's own vite.config.ts.
 */
import { defineConfig } from 'vite-plus';

export default defineConfig({
	staged: {
		'*.{js,jsx,ts,tsx}': 'vp check --fix',
		'*.css': ['vp fmt', 'stylelint --fix'],
		'*.php': './vendor/bin/phpcs',
		'**/assets/svg/**/*.svg': 'node bin/lint-svg.mjs',
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
		ignorePatterns: [
			'**/dist/**',
			'**/vendor/**',
			'**/node_modules/**',
			'plugins/**',
			'upgrade/**',
			'uploads/**',
		],
	},
});
