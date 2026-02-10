#!/usr/bin/env node

/**
 * WordPress Plugin Scaffold CLI
 *
 * Downloads the reference 10up-plugin from GitHub and scaffolds it into an
 * existing project with custom naming conventions.
 *
 * Run with: npm run scaffold:plugin
 */

import { input, confirm } from '@inquirer/prompts';
import { createWriteStream, existsSync, readFileSync, writeFileSync, mkdirSync, rmSync } from 'node:fs';
import { join, resolve } from 'node:path';
import { parseArgs } from 'node:util';
import { pipeline } from 'node:stream/promises';
import { createGunzip } from 'node:zlib';
import { extract } from 'tar';
import {
	toKebab,
	toPascal,
	toConstant,
	toSnake,
	toTitle,
	walkFiles,
} from './scaffold-helpers.mjs';

// ---------------------------------------------------------------------------
// Constants
// ---------------------------------------------------------------------------

const ROOT = resolve(import.meta.dirname, '..');
const TEMP_DIR = join(ROOT, '.scaffold-temp');

// ---------------------------------------------------------------------------
// CLI argument parsing
// ---------------------------------------------------------------------------

const CLI_OPTIONS = {
	// Core
	name: { type: 'string', short: 'n' },
	'mu-dir': { type: 'string' },
	ref: { type: 'string', default: 'trunk' },

	// Plugin overrides
	'plugin-slug': { type: 'string' },
	'plugin-namespace': { type: 'string' },
	'plugin-constant': { type: 'string' },
	'plugin-text-domain': { type: 'string' },
	'plugin-hook-prefix': { type: 'string' },
	'plugin-human-name': { type: 'string' },
	'plugin-npm-name': { type: 'string' },

	// Metadata
	'author-name': { type: 'string' },
	'author-email': { type: 'string' },
	'author-uri': { type: 'string' },
	description: { type: 'string' },
	'composer-vendor': { type: 'string' },

	// Flags
	yes: { type: 'boolean', short: 'y', default: false },
	help: { type: 'boolean', default: false },
};

function printHelp() {
	console.log(`
  Usage: npm run scaffold:plugin -- [options]

  Options:
    -n, --name <name>               Plugin name (e.g. "Content Syndication")
    --mu-dir <path>                 MU plugins directory (auto-detected if not provided)
    --ref <branch|tag>              GitHub ref to download from (default: trunk)
    -y, --yes                       Skip confirmation prompt

  Plugin overrides:
    --plugin-slug <slug>            Plugin directory and slug
    --plugin-namespace <ns>         PHP namespace (e.g. ContentSyndicationPlugin)
    --plugin-constant <prefix>      Constant prefix (e.g. CONTENT_SYNDICATION_PLUGIN)
    --plugin-text-domain <domain>   Text domain
    --plugin-hook-prefix <prefix>   Hook prefix (e.g. content_syndication_plugin)
    --plugin-human-name <name>      Human-readable name
    --plugin-npm-name <name>        npm package name

  Metadata:
    --author-name <name>            Author name
    --author-email <email>          Author email
    --author-uri <uri>              Author URI
    --description <text>            Plugin description
    --composer-vendor <vendor>      Composer vendor slug

  Examples:
    npm run scaffold:plugin
    npm run scaffold:plugin -- -n "Content Syndication" -y
    npm run scaffold:plugin -- \\
      --name "Content Syndication" \\
      --plugin-slug content-syndication \\
      --composer-vendor acme \\
      --yes
`);
}

function parseCli() {
	try {
		const { values } = parseArgs({ options: CLI_OPTIONS, strict: true });
		return values;
	} catch (err) {
		console.error(`\n  Error: ${err.message}\n`);
		printHelp();
		process.exit(1);
	}
}

// ---------------------------------------------------------------------------
// Auto-detection helpers
// ---------------------------------------------------------------------------

function detectMuPluginsDir() {
	const clientMu = join(ROOT, 'client-mu-plugins');
	const standardMu = join(ROOT, 'mu-plugins');

	if (existsSync(clientMu)) {
		return clientMu;
	}
	if (existsSync(standardMu)) {
		return standardMu;
	}

	throw new Error('Could not find mu-plugins or client-mu-plugins directory');
}

function detectComposerVendor() {
	const composerPath = join(ROOT, 'composer.json');
	if (!existsSync(composerPath)) {
		return '';
	}

	try {
		const composer = JSON.parse(readFileSync(composerPath, 'utf-8'));
		const name = composer.name || '';
		const vendor = name.split('/')[0];
		return vendor || '';
	} catch {
		return '';
	}
}

// ---------------------------------------------------------------------------
// GitHub download
// ---------------------------------------------------------------------------

async function downloadPluginFromGitHub(ref) {
	console.log(`\n  Downloading 10up-plugin from GitHub (ref: ${ref})...\n`);

	const tarballUrl = `https://api.github.com/repos/10up/wp-scaffold/tarball/${ref}`;

	// Create temp directory
	if (existsSync(TEMP_DIR)) {
		rmSync(TEMP_DIR, { recursive: true, force: true });
	}
	mkdirSync(TEMP_DIR, { recursive: true });

	// Download and extract
	const response = await fetch(tarballUrl, {
		headers: {
			'User-Agent': 'wp-scaffold-plugin-cli',
		},
	});

	if (!response.ok) {
		throw new Error(`Failed to download from GitHub: ${response.statusText}`);
	}

	// Extract only the plugin files we need
	await pipeline(
		response.body,
		createGunzip(),
		extract({
			cwd: TEMP_DIR,
			strip: 2, // Strip "10up-wp-scaffold-<hash>/mu-plugins/"
			filter: (path) => {
				// Only extract mu-plugins/10up-plugin/ and mu-plugins/10up-plugin-loader.php
				return path.includes('/mu-plugins/10up-plugin/') || path.endsWith('/mu-plugins/10up-plugin-loader.php');
			},
		}),
	);

	console.log('  Downloaded successfully\n');
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------

async function main() {
	const args = parseCli();

	if (args.help) {
		printHelp();
		process.exit(0);
	}

	console.log('\n  WordPress Plugin Scaffold\n');

	// -----------------------------------------------------------------------
	// 1. Auto-detect settings
	// -----------------------------------------------------------------------

	const muDir = args['mu-dir'] || detectMuPluginsDir();
	const detectedVendor = detectComposerVendor();

	console.log(`  Detected mu-plugins directory: ${muDir.replace(ROOT + '/', '')}`);
	if (detectedVendor) {
		console.log(`  Detected composer vendor: ${detectedVendor}`);
	}
	console.log('');

	// -----------------------------------------------------------------------
	// 2. Prompts (or use CLI args)
	// -----------------------------------------------------------------------

	const pluginName = args.name
		? args.name
		: await input({
				message: 'Plugin name (human-readable, e.g. "Content Syndication"):',
				validate: (v) => (v.trim().length > 0 ? true : 'Plugin name is required.'),
			});

	const slug = toKebab(pluginName.trim());

	// Derive defaults
	const defaults = {
		pluginSlug: args['plugin-slug'] || `${slug}-plugin`,
		pluginNamespace: args['plugin-namespace'] || `${toPascal(slug)}Plugin`,
		pluginConstant: args['plugin-constant'] || `${toConstant(slug)}_PLUGIN`,
		pluginTextDomain: args['plugin-text-domain'] || `${slug}-plugin`,
		pluginHookPrefix: args['plugin-hook-prefix'] || `${toSnake(slug)}_plugin`,
		pluginHumanName: args['plugin-human-name'] || `${toTitle(slug)} Plugin`,
		pluginNpmName: args['plugin-npm-name'] || `${slug}-plugin`,
		authorName: args['author-name'] || '',
		authorEmail: args['author-email'] || '',
		authorUri: args['author-uri'] || '',
		description: args.description || '',
		composerVendor: args['composer-vendor'] || detectedVendor || slug,
	};

	const isNonInteractive = !!args.name;

	// Prompt for metadata if interactive
	if (!isNonInteractive) {
		console.log('\n  Derived values:\n');
		console.log(`  Plugin directory:     ${defaults.pluginSlug}`);
		console.log(`  Plugin namespace:     ${defaults.pluginNamespace}`);
		console.log(`  Plugin constants:     ${defaults.pluginConstant}_*`);
		console.log(`  Plugin text domain:   ${defaults.pluginTextDomain}`);
		console.log(`  Plugin human name:    ${defaults.pluginHumanName}`);
		console.log('');

		defaults.authorName = await input({
			message: 'Author name:',
			default: defaults.authorName,
		});
		defaults.authorEmail = await input({
			message: 'Author email:',
			default: defaults.authorEmail,
		});
		defaults.authorUri = await input({
			message: 'Author URI:',
			default: defaults.authorUri,
		});
		defaults.description = await input({
			message: 'Plugin description:',
			default: defaults.description,
		});
		defaults.composerVendor = await input({
			message: 'Composer vendor slug:',
			default: defaults.composerVendor,
		});
	}

	const values = defaults;

	// -----------------------------------------------------------------------
	// 3. Confirm
	// -----------------------------------------------------------------------

	console.log('\n  Summary:\n');
	console.log(`  Plugin:               ${values.pluginSlug} (${values.pluginNamespace})`);
	if (values.authorName) console.log(`  Author:               ${values.authorName}`);
	if (values.description) console.log(`  Description:          ${values.description}`);
	console.log('');

	if (!args.yes) {
		const ok = await confirm({ message: 'Download and scaffold this plugin?', default: true });
		if (!ok) {
			console.log('\n  Aborted. No changes were made.\n');
			process.exit(0);
		}
	}

	// -----------------------------------------------------------------------
	// 4. Download from GitHub
	// -----------------------------------------------------------------------

	await downloadPluginFromGitHub(args.ref);

	// -----------------------------------------------------------------------
	// 5. String replacements
	// -----------------------------------------------------------------------

	console.log('  Applying string replacements...\n');

	const replacements = [];

	// Plugin replacements
	replacements.push(
		['TenUpPlugin', values.pluginNamespace],
		['TENUP_PLUGIN', values.pluginConstant],
		['tenup-plugin', values.pluginSlug],
		['tenup_plugin', values.pluginHookPrefix],
		['10up-plugin', values.pluginSlug],
		['10up Plugin Scaffold', values.pluginHumanName],
	);

	// Composer package name
	replacements.push(['10up/wp-plugin', `${values.composerVendor}/${values.pluginSlug}`]);

	// Author / metadata
	if (values.authorEmail) {
		replacements.push(['info@10up.com', values.authorEmail]);
	}
	if (values.authorName) {
		replacements.push(['10up', values.authorName]);
	}
	if (values.authorUri) {
		replacements.push(['https://10up.com', values.authorUri]);
	}
	if (values.description) {
		replacements.push(['A brief description of the plugin.', values.description]);
	}

	// Sort by length descending to avoid partial matches
	replacements.sort((a, b) => b[0].length - a[0].length);

	// Walk all files in the temp directory
	const files = walkFiles(TEMP_DIR);
	let filesChanged = 0;

	for (const filePath of files) {
		let content;
		try {
			content = readFileSync(filePath, 'utf-8');
		} catch {
			continue;
		}

		let changed = false;
		for (const [oldStr, newStr] of replacements) {
			if (content.includes(oldStr)) {
				content = content.replaceAll(oldStr, newStr);
				changed = true;
			}
		}

		if (changed) {
			writeFileSync(filePath, content);
			filesChanged++;
		}
	}

	console.log(`  Updated ${filesChanged} file(s)\n`);

	// -----------------------------------------------------------------------
	// 6. Move files to the correct location
	// -----------------------------------------------------------------------

	console.log('  Moving plugin files into place...\n');

	const tempPluginDir = join(TEMP_DIR, '10up-plugin');
	const tempLoaderFile = join(TEMP_DIR, '10up-plugin-loader.php');

	const finalPluginDir = join(muDir, values.pluginSlug);
	const finalLoaderFile = join(muDir, `${values.pluginSlug}-loader.php`);

	// Move plugin directory
	if (existsSync(tempPluginDir)) {
		if (existsSync(finalPluginDir)) {
			throw new Error(`Plugin directory already exists: ${finalPluginDir}`);
		}
		mkdirSync(finalPluginDir, { recursive: true });
		// Copy contents
		const { execSync } = await import('node:child_process');
		execSync(`cp -R "${tempPluginDir}/." "${finalPluginDir}"`);
		console.log(`  Created ${muDir.replace(ROOT + '/', '')}/${values.pluginSlug}/`);
	}

	// Move loader file
	if (existsSync(tempLoaderFile)) {
		if (existsSync(finalLoaderFile)) {
			throw new Error(`Loader file already exists: ${finalLoaderFile}`);
		}
		let loaderContent = readFileSync(tempLoaderFile, 'utf-8');
		// Fix the require path
		loaderContent = loaderContent.replaceAll('10up-plugin', values.pluginSlug);
		writeFileSync(finalLoaderFile, loaderContent);
		console.log(`  Created ${muDir.replace(ROOT + '/', '')}/${values.pluginSlug}-loader.php`);
	}

	// Clean up temp directory
	rmSync(TEMP_DIR, { recursive: true, force: true });

	// -----------------------------------------------------------------------
	// 7. Integrate with project config files
	// -----------------------------------------------------------------------

	console.log('\n  Integrating with project config...\n');

	// Update package.json
	const pkgPath = join(ROOT, 'package.json');
	if (existsSync(pkgPath)) {
		const pkg = JSON.parse(readFileSync(pkgPath, 'utf-8'));

		// Add to workspaces
		const workspacePath = `${muDir.replace(ROOT + '/', '')}/${values.pluginSlug}`;
		if (!pkg.workspaces.includes(workspacePath)) {
			pkg.workspaces.push(workspacePath);
		}

		// Add watch script
		const watchScriptName = `watch:${values.pluginNpmName}`;
		if (!pkg.scripts[watchScriptName]) {
			pkg.scripts[watchScriptName] = `npm run watch -w=${values.pluginNpmName}`;
		}

		// Update main watch script to include new plugin
		if (pkg.scripts.watch && !pkg.scripts.watch.includes(watchScriptName)) {
			pkg.scripts.watch = `${pkg.scripts.watch.replace('run-p', 'run-p')} ${watchScriptName}`;
		}

		writeFileSync(pkgPath, `${JSON.stringify(pkg, null, 2)}\n`);
		console.log('  Updated package.json (workspaces, watch scripts)');
	}

	// Update phpstan.neon
	const phpstanPath = join(ROOT, 'phpstan.neon');
	if (existsSync(phpstanPath)) {
		let phpstanContent = readFileSync(phpstanPath, 'utf-8');
		const pluginPath = `${muDir.replace(ROOT + '/', '')}/${values.pluginSlug}`;
		const loaderPath = `${muDir.replace(ROOT + '/', '')}/${values.pluginSlug}-loader.php`;

		if (!phpstanContent.includes(pluginPath)) {
			// Add before the closing of the paths array
			phpstanContent = phpstanContent.replace(
				/(\s+paths:\s*\n(?:\s+-\s+.+\n)+)/,
				`$1\t\t- ${pluginPath}\n\t\t- ${loaderPath}\n`,
			);
			writeFileSync(phpstanPath, phpstanContent);
			console.log('  Updated phpstan.neon (added plugin paths)');
		}
	}

	// Update phpstan/constants.php
	const constantsPath = join(ROOT, 'phpstan', 'constants.php');
	if (existsSync(constantsPath)) {
		let constantsContent = readFileSync(constantsPath, 'utf-8');

		const newConstants = `
// ${values.pluginHumanName} constants
define( '${values.pluginConstant}_VERSION', '0.1.0' );
define( '${values.pluginConstant}_URL', '' );
define( '${values.pluginConstant}_PATH', '' );
define( '${values.pluginConstant}_INC', ${values.pluginConstant}_PATH . 'includes/' );
`;

		if (!constantsContent.includes(values.pluginConstant)) {
			// Add before the closing PHP tag or at the end
			constantsContent = constantsContent.trimEnd() + '\n' + newConstants;
			writeFileSync(constantsPath, constantsContent);
			console.log('  Updated phpstan/constants.php (added plugin constants)');
		}
	}

	// Update phpcs.xml
	const phpcsPath = join(ROOT, 'phpcs.xml');
	if (existsSync(phpcsPath)) {
		let phpcsContent = readFileSync(phpcsPath, 'utf-8');
		const loaderPath = `./${muDir.replace(ROOT + '/', '')}/${values.pluginSlug}-loader.php`;

		if (!phpcsContent.includes(loaderPath)) {
			// Add exclusion rule for the loader file
			const exclusionRule = `
	<!-- Ignore filecomment for the plugin loader -->
	<rule ref="Squiz.Commenting.FileComment.Missing">
		<exclude-pattern>${loaderPath}</exclude-pattern>
	</rule>
`;

			// Insert before the closing </ruleset> tag
			phpcsContent = phpcsContent.replace('</ruleset>', `${exclusionRule}\n</ruleset>`);
			writeFileSync(phpcsPath, phpcsContent);
			console.log('  Updated phpcs.xml (added loader exclusion)');
		}
	}

	// Update .github/workflows/php.yml
	const phpWorkflowPath = join(ROOT, '.github', 'workflows', 'php.yml');
	if (existsSync(phpWorkflowPath)) {
		let workflowContent = readFileSync(phpWorkflowPath, 'utf-8');
		const pluginPath = `${muDir.replace(ROOT + '/', '')}/${values.pluginSlug}`;

		if (!workflowContent.includes(pluginPath)) {
			// Add composer validate step
			const validateStep = `
      - name: Validate ${values.pluginHumanName} composer.json and composer.lock
        run: composer validate --strict --working-dir=${pluginPath}
`;

			// Add after the last validate step
			workflowContent = workflowContent.replace(
				/(- name: Validate .+ composer\.json and composer\.lock\s+run: composer validate[^\n]+\n)/g,
				(match) => match + validateStep,
			);

			// Add composer install step
			const installStep = `
      - name: Install ${values.pluginHumanName} dependencies
        run: composer install --prefer-dist --no-progress --working-dir=${pluginPath}
`;

			// Add after the last install step
			workflowContent = workflowContent.replace(
				/(- name: Install .+ dependencies\s+run: composer install[^\n]+\n)/g,
				(match) => match + installStep,
			);

			writeFileSync(phpWorkflowPath, workflowContent);
			console.log('  Updated .github/workflows/php.yml (added CI steps)');
		}
	}

	// -----------------------------------------------------------------------
	// Done!
	// -----------------------------------------------------------------------

	console.log('\n  Done! Your plugin has been scaffolded.\n');
	console.log('  Next steps:\n');
	console.log('    1. Run npm install');
	console.log(`    2. Run composer install in ${pluginPath}`);
	console.log('    3. Run npm run build');
	console.log('    4. Start developing!\n');
}

main().catch((err) => {
	if (err.name === 'ExitPromptError') {
		console.log('\n  Aborted.\n');
		process.exit(0);
	}
	console.error('\n  Error:', err.message, '\n');
	process.exit(1);
});
