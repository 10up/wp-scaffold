/**
 * Shared utilities for WordPress scaffold scripts
 *
 * @package TenUpScaffold
 */

import { execSync } from 'node:child_process';
import { readdirSync } from 'node:fs';
import { join, extname } from 'node:path';

// ---------------------------------------------------------------------------
// Constants
// ---------------------------------------------------------------------------

export const BINARY_EXTENSIONS = new Set([
	'.png',
	'.jpg',
	'.jpeg',
	'.gif',
	'.webp',
	'.ico',
	'.svg',
	'.woff',
	'.woff2',
	'.eot',
	'.ttf',
	'.otf',
	'.zip',
	'.gz',
	'.tar',
	'.bz2',
	'.mp4',
	'.mp3',
	'.mov',
	'.avi',
	'.pdf',
	'.doc',
	'.docx',
	'.lock',
]);

export const SKIP_DIRS = new Set(['node_modules', 'vendor', '.git', 'plugins']);

// ---------------------------------------------------------------------------
// Naming convention helpers
// ---------------------------------------------------------------------------

/** Convert "Acme Corp" to "acme-corp" */
export function toKebab(name) {
	return name
		.replace(/([a-z])([A-Z])/g, '$1-$2')
		.replace(/[\s_]+/g, '-')
		.replace(/[^a-z0-9-]/gi, '')
		.toLowerCase();
}

/** Convert "acme-corp" to "AcmeCorp" */
export function toPascal(slug) {
	return slug
		.split('-')
		.map((w) => w.charAt(0).toUpperCase() + w.slice(1))
		.join('');
}

/** Convert "acme-corp" to "ACME_CORP" */
export function toConstant(slug) {
	return slug.replace(/-/g, '_').toUpperCase();
}

/** Convert "acme-corp" to "acme_corp" */
export function toSnake(slug) {
	return slug.replace(/-/g, '_');
}

/** Convert "acme-corp" to "Acme Corp" */
export function toTitle(slug) {
	return slug
		.split('-')
		.map((w) => w.charAt(0).toUpperCase() + w.slice(1))
		.join(' ');
}

// ---------------------------------------------------------------------------
// Git helpers
// ---------------------------------------------------------------------------

/** Try to read the git remote origin URL */
export function getGitRemoteUrl(cwd) {
	try {
		const url = execSync('git remote get-url origin', { cwd, encoding: 'utf-8' }).trim();
		// Normalize git@github.com:org/repo.git to https://github.com/org/repo
		if (url.startsWith('git@')) {
			return url.replace(/^git@([^:]+):/, 'https://$1/').replace(/\.git$/, '');
		}
		return url.replace(/\.git$/, '');
	} catch {
		return '';
	}
}

/** Extract the org/user from a GitHub URL */
export function getGitOrgFromUrl(url) {
	const match = url.match(/github\.com\/([^/]+)/);
	return match ? match[1].toLowerCase() : '';
}

// ---------------------------------------------------------------------------
// File-walking helpers
// ---------------------------------------------------------------------------

export function walkFiles(dir, results = [], skipDirs = SKIP_DIRS) {
	for (const entry of readdirSync(dir, { withFileTypes: true })) {
		const fullPath = join(dir, entry.name);
		if (entry.isDirectory()) {
			if (skipDirs.has(entry.name)) continue;
			walkFiles(fullPath, results, skipDirs);
		} else if (entry.isFile()) {
			if (BINARY_EXTENSIONS.has(extname(entry.name).toLowerCase())) continue;
			if (entry.name === 'package-lock.json') continue;
			results.push(fullPath);
		}
	}
	return results;
}

// ---------------------------------------------------------------------------
// Replacement map builder (shared between scaffold.mjs and scaffold-vip.mjs)
// ---------------------------------------------------------------------------

/**
 * Build the replacement map for project scaffold.
 *
 * @param {object}  options
 * @param {boolean} options.isBlock - Whether the theme is a block theme
 * @param {boolean} options.isVip   - Whether the hosting is VIP
 * @param {object}  options.values  - The derived values object
 * @param {string}  options.slug    - The project slug (kebab-case)
 * @returns {Array<[string, string]>} Sorted replacement pairs (longest first)
 */
export function buildReplacementMap({ isBlock, isVip, values, slug }) {
	const replacements = [];

	// Theme replacements (chosen theme)
	if (isBlock) {
		// The block theme uses both "tenup-block-theme" (directory/style.css) AND
		// "tenup-theme" (internal handles, text domain, pattern slugs). We need
		// to replace both. Longer strings first to avoid partial matches.
		replacements.push(
			['TenUpBlockTheme', values.themeNamespace],
			['TenupBlockTheme', values.themeNamespace],
			['TENUP_BLOCK_THEME', values.themeConstant],
			['tenup-block-theme', values.themeSlug],
			['tenup_block_theme', values.themeHookPrefix],
			['10up-block-theme', values.themeSlug],
			['10up Block Theme', values.themeHumanName],
			['TenUpTheme', values.themeNamespace],
			['TENUP_THEME', values.themeConstant],
			['tenup-theme', values.themeNpmName],
			['tenup_theme', values.themeHookPrefix],
			['10up-theme', values.themeSlug],
			['10up Theme', values.themeHumanName],
		);
	} else {
		replacements.push(
			['TenUpTheme', values.themeNamespace],
			['TENUP_THEME', values.themeConstant],
			['tenup-theme', values.themeSlug],
			['tenup_theme', values.themeHookPrefix],
			['10up-theme', values.themeSlug],
			['10up Theme', values.themeHumanName],
		);
	}

	// Plugin replacements
	replacements.push(
		['TenUpPlugin', values.pluginNamespace],
		['TENUP_PLUGIN', values.pluginConstant],
		['tenup-plugin', values.pluginSlug],
		['tenup_plugin', values.pluginHookPrefix],
		['10up-plugin', values.pluginSlug],
		['10up Plugin Scaffold', values.pluginHumanName],
	);

	// VIP: mu-plugins -> client-mu-plugins path replacement.
	// Use targeted patterns to avoid changing generic "mu-plugins" WordPress references.
	if (isVip) {
		replacements.push(
			['mu-plugins/10up-plugin', 'client-mu-plugins/10up-plugin'],
			['<file>mu-plugins</file>', '<file>client-mu-plugins</file>'],
		);
	}

	// Composer package names
	replacements.push(
		['10up/wp-scaffold', `${values.composerVendor}/${slug}`],
		['10up/wp-plugin', `${values.composerVendor}/${values.pluginSlug}`],
		['10up/wp-theme', `${values.composerVendor}/${values.themeSlug}`],
		['10up/tenup-theme', `${values.composerVendor}/${values.themeSlug}`],
	);

	// npm root package name
	replacements.push(['tenup-wp-scaffold', slug]);

	// Author / metadata
	if (values.authorEmail) {
		replacements.push(['info@10up.com', values.authorEmail]);
	}
	if (values.authorUri) {
		replacements.push(['https://10up.com', values.authorUri]);
	}

	// Description strings (longer matches first)
	if (values.description) {
		replacements.push(
			['The starting point for all 10up WordPress projects.', values.description],
			['The starting point for all 10up WordPress themes.', values.description],
			['The starting point for all 10up WordPress plugins.', values.description],
			['A brief description of the plugin.', values.description],
			['Project description.', values.description],
			['Project Description', values.description],
		);
	}

	// URLs
	if (values.repoUrl) {
		replacements.push(
			['https://github.com/10up/wp-scaffold', values.repoUrl],
			['https://project-git-repo.tld', values.repoUrl],
		);
	}
	if (values.homepageUrl) {
		replacements.push(['https://project-domain.tld', values.homepageUrl]);
	}

	// Author name replacement (must be last / most targeted to avoid over-matching).
	// We only replace the exact author patterns to avoid mangling things like
	// "10up-toolkit" or "10up/phpcs-composer".
	if (values.authorName) {
		replacements.push(
			['"name": "10up"', `"name": "${values.authorName}"`],
			['Author:            10up', `Author:            ${values.authorName}`],
			['Author:      10up', `Author:      ${values.authorName}`],
			['Author:        10up', `Author:        ${values.authorName}`],
		);
	}

	// Sort by length of search string descending to prevent partial matches.
	replacements.sort((a, b) => b[0].length - a[0].length);

	return replacements;
}
