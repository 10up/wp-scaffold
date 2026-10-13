#!/usr/bin/env node

/**
 * SVG Icon Linter
 *
 * Checks that SVG icons survive WordPress core's icon registry. Core runs every
 * registered icon through a fixed wp_kses allowlist (no filter) and silently
 * drops everything else, so an icon can register fine and still render empty.
 *
 * Run with: npm run lint-svg [-- <file|dir|glob> ...]
 *
 * With no arguments it scans themes/<theme>/assets/svg and mu-plugins/<plugin>/assets/svg.
 * Exits 1 when any file uses an element or attribute core would strip.
 *
 * @package TenUpScaffold
 */

import { existsSync, globSync, readFileSync, statSync } from 'node:fs';
import { join, relative, resolve } from 'node:path';

// ---------------------------------------------------------------------------
// Constants
// ---------------------------------------------------------------------------

const ROOT = resolve(import.meta.dirname, '..');

const DEFAULT_GLOBS = ['themes/*/assets/svg/**/*.svg', 'mu-plugins/*/assets/svg/**/*.svg'];

/**
 * Elements and attributes WordPress core keeps in an icon.
 *
 * Source: WP_Icons_Registry::sanitize_icon_content(), WordPress 7.1. Core passes
 * this exact list to wp_kses() and offers no filter to extend it. Keep it in sync
 * as core widens the list (see WordPress/wordpress-develop#12197 and #13539).
 * Names are lowercase because kses compares element and attribute names
 * case-insensitively.
 */
const ALLOWED = {
	svg: ['class', 'xmlns', 'width', 'height', 'viewbox', 'aria-hidden', 'role', 'focusable'],
	path: ['fill', 'fill-rule', 'd', 'transform'],
	polygon: ['fill', 'fill-rule', 'points', 'transform', 'focusable'],
};

const SHAPES = new Set(['line', 'polyline', 'circle', 'rect', 'ellipse']);

const HINTS = {
	stroke:
		'stroke attributes are stripped by core — outline strokes in your design tool (Figma: Outline stroke) before exporting',
	shapes: 'convert shapes to paths (e.g. SVGO convertShapeToPath)',
	group: 'flatten <g> wrappers — core drops them along with every attribute they carry',
};

// ---------------------------------------------------------------------------
// File discovery
// ---------------------------------------------------------------------------

/**
 * Resolve CLI arguments (files, directories or globs) to a sorted list of SVG paths.
 *
 * @param {string[]} args CLI arguments. Empty means the default theme/mu-plugin folders.
 * @returns {string[]} Absolute file paths.
 */
function collectFiles(args) {
	const files = new Set();
	const patterns = args.length ? args : DEFAULT_GLOBS;

	for (const pattern of patterns) {
		const absolute = resolve(pattern);

		if (existsSync(absolute) && statSync(absolute).isDirectory()) {
			globSync('**/*.svg', { cwd: absolute }).forEach((f) => files.add(join(absolute, f)));
		} else if (existsSync(absolute)) {
			files.add(absolute);
		} else {
			// Defaults are relative to the repo root; user globs to the cwd.
			const cwd = args.length ? process.cwd() : ROOT;
			globSync(pattern, { cwd }).forEach((f) => files.add(resolve(cwd, f)));
		}
	}

	return [...files].filter((f) => f.toLowerCase().endsWith('.svg')).sort();
}

// ---------------------------------------------------------------------------
// Tokenizer
// ---------------------------------------------------------------------------

const TAG = /<([A-Za-z][\w:.-]*)((?:\s+[^\s=/>]+(?:\s*=\s*(?:"[^"]*"|'[^']*'|[^\s"'>]+))?)*)\s*\/?>/g;
const ATTR = /([^\s=/>]+)(?:\s*=\s*("[^"]*"|'[^']*'|[^\s"'>]+))?/g;

/**
 * Remove comments, CDATA sections, processing instructions (<?xml ?>) and
 * doctypes in one forward scan.
 *
 * A single pass with indexOf (rather than chained regex replaces) keeps this
 * linear and means removing one construct can never splice together another.
 * An unterminated construct swallows the rest of the file, as a parser would.
 *
 * @param {string} source SVG markup.
 * @returns {string} Markup with only elements and text left.
 */
function withoutDeclarations(source) {
	let out = '';
	let pos = 0;

	while (pos < source.length) {
		const start = source.indexOf('<', pos);
		if (start === -1) {
			out += source.slice(pos);
			break;
		}
		out += source.slice(pos, start);

		let end;
		if (source.startsWith('<!--', start)) {
			end = source.indexOf('-->', start + 4);
			end = end === -1 ? source.length : end + 3;
		} else if (source.startsWith('<![CDATA[', start)) {
			end = source.indexOf(']]>', start + 9);
			end = end === -1 ? source.length : end + 3;
		} else if (source.startsWith('<?', start)) {
			end = source.indexOf('?>', start + 2);
			end = end === -1 ? source.length : end + 2;
		} else if (source.startsWith('<!', start)) {
			// Doctype: skip an internal [ … ] subset before the closing >.
			const close = source.indexOf('>', start);
			const subset = source.indexOf('[', start);
			if (subset !== -1 && (close === -1 || subset < close)) {
				const subsetEnd = source.indexOf(']', subset);
				end = subsetEnd === -1 ? -1 : source.indexOf('>', subsetEnd);
			} else {
				end = close;
			}
			end = end === -1 ? source.length : end + 1;
		} else {
			out += '<';
			pos = start + 1;
			continue;
		}
		pos = end;
	}

	return out;
}

/**
 * Parse the start and self-closing tags of an SVG document.
 *
 * Comments, CDATA, <?xml ?> declarations and doctypes are removed first: kses
 * strips them harmlessly, so they are never reported. Closing tags and text
 * content are ignored.
 *
 * @param {string} source SVG markup.
 * @returns {{ name: string, attrs: Record<string, string|null> }[]} Tags in document order.
 */
function tokenize(source) {
	return [...withoutDeclarations(source).matchAll(TAG)].map(([, name, rawAttrs]) => {
		const attrs = {};
		for (const [, attr, value] of rawAttrs.matchAll(ATTR)) {
			attrs[attr.toLowerCase()] = value === undefined ? null : value.replace(/^["']|["']$/g, '');
		}
		return { name: name.toLowerCase(), attrs };
	});
}

// ---------------------------------------------------------------------------
// Checks
// ---------------------------------------------------------------------------

/**
 * Find everything core's icon sanitizer would strip from one SVG.
 *
 * @param {string} source SVG markup.
 * @returns {{ elements: Map<string, number>, attributes: Map<string, Set<string>>, hints: string[] }}
 */
function lintSvg(source) {
	const tags = tokenize(source);
	const elements = new Map();
	const attributes = new Map();
	const hints = new Set();

	tags.forEach(({ name, attrs }, index) => {
		const names = Object.keys(attrs);

		if (names.some((attr) => attr.startsWith('stroke'))) {
			hints.add(HINTS.stroke);
		}
		if (index === 0 && name === 'svg' && attrs.fill?.trim().toLowerCase() === 'none') {
			hints.add(HINTS.stroke);
		}
		if (SHAPES.has(name)) {
			hints.add(HINTS.shapes);
		}
		if (name === 'g') {
			hints.add(HINTS.group);
		}

		if (!ALLOWED[name]) {
			elements.set(name, (elements.get(name) ?? 0) + 1);
			return;
		}

		const disallowed = names.filter((attr) => !ALLOWED[name].includes(attr));
		if (disallowed.length) {
			const seen = attributes.get(name) ?? new Set();
			disallowed.forEach((attr) => seen.add(attr));
			attributes.set(name, seen);
		}
	});

	return { elements, attributes, hints: [...hints] };
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------

function main() {
	const files = collectFiles(process.argv.slice(2));
	let failed = 0;

	for (const file of files) {
		const { elements, attributes, hints } = lintSvg(readFileSync(file, 'utf8'));

		if (!elements.size && !attributes.size) {
			continue;
		}

		failed++;
		console.log(`\n${relative(process.cwd(), file) || file}`);
		for (const [name, count] of elements) {
			console.log(`  ✖ element <${name}> is not allowed (${count}×)`);
		}
		for (const [name, attrs] of attributes) {
			console.log(`  ✖ <${name}> attributes not allowed: ${[...attrs].join(', ')}`);
		}
		for (const hint of hints) {
			console.log(`  → ${hint}`);
		}
	}

	const summary = `${files.length} SVG file${files.length === 1 ? '' : 's'} checked, ${failed} with problems.`;

	if (failed) {
		console.log(`\n✖ ${summary} WordPress core's icon registry strips these when the icon is registered.\n`);
		process.exit(1);
	}

	console.log(`✔ ${summary}`);
}

main();
