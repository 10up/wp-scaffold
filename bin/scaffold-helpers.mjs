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

export function walkFiles(dir, results = []) {
	for (const entry of readdirSync(dir, { withFileTypes: true })) {
		const fullPath = join(dir, entry.name);
		if (entry.isDirectory()) {
			if (SKIP_DIRS.has(entry.name)) continue;
			walkFiles(fullPath, results);
		} else if (entry.isFile()) {
			if (BINARY_EXTENSIONS.has(extname(entry.name).toLowerCase())) continue;
			if (entry.name === 'package-lock.json') continue;
			results.push(fullPath);
		}
	}
	return results;
}
