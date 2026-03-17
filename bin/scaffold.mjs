#!/usr/bin/env node

/**
 * WordPress Project Scaffold CLI
 *
 * Interactive CLI script that renames all placeholder strings, directories,
 * and config references in the 10up wp-scaffold starter to match a new project.
 *
 * Run with: npm run scaffold
 */

import { select, input, confirm } from '@inquirer/prompts';
import {
	existsSync,
	readFileSync,
	writeFileSync,
	readdirSync,
	renameSync,
	rmSync,
	mkdirSync,
} from 'node:fs';
import { join, resolve } from 'node:path';
import { parseArgs } from 'node:util';
import {
	toKebab,
	toPascal,
	toConstant,
	toSnake,
	toTitle,
	getGitRemoteUrl,
	getGitOrgFromUrl,
	walkFiles,
} from './scaffold-helpers.mjs';

// ---------------------------------------------------------------------------
// Constants
// ---------------------------------------------------------------------------

const ROOT = resolve(import.meta.dirname, '..');

// ---------------------------------------------------------------------------
// CLI argument parsing
// ---------------------------------------------------------------------------

const CLI_OPTIONS = {
	// Core
	hosting: { type: 'string', short: 'h' },
	theme: { type: 'string', short: 't' },
	'project-name': { type: 'string', short: 'n' },

	// Plugin
	'plugin-slug': { type: 'string' },
	'plugin-namespace': { type: 'string' },
	'plugin-constant': { type: 'string' },
	'plugin-text-domain': { type: 'string' },
	'plugin-hook-prefix': { type: 'string' },
	'plugin-human-name': { type: 'string' },
	'plugin-npm-name': { type: 'string' },

	// Theme
	'theme-slug': { type: 'string' },
	'theme-namespace': { type: 'string' },
	'theme-constant': { type: 'string' },
	'theme-text-domain': { type: 'string' },
	'theme-hook-prefix': { type: 'string' },
	'theme-human-name': { type: 'string' },
	'theme-npm-name': { type: 'string' },

	// Metadata
	'author-name': { type: 'string' },
	'author-email': { type: 'string' },
	'author-uri': { type: 'string' },
	description: { type: 'string' },
	'composer-vendor': { type: 'string' },
	'homepage-url': { type: 'string' },
	'repo-url': { type: 'string' },

	// Flags
	yes: { type: 'boolean', short: 'y', default: false },
	'self-destruct': { type: 'boolean', default: false },
	help: { type: 'boolean', default: false },
};

function printHelp() {
	console.log(`
  Usage: npm run scaffold -- [options]

  Options:
    -n, --project-name <name>       Project name (e.g. "Acme Corp")
    -h, --hosting <type>            Hosting platform: "standard" or "vip"
    -t, --theme <type>              Theme type: "block" or "classic"
    -y, --yes                       Skip confirmation prompt
    --self-destruct                 Remove the scaffold script and bin/ directory after running

  Plugin overrides:
    --plugin-slug <slug>            Plugin directory and slug
    --plugin-namespace <ns>         PHP namespace (e.g. AcmeCorpPlugin)
    --plugin-constant <prefix>      Constant prefix (e.g. ACME_CORP_PLUGIN)
    --plugin-text-domain <domain>   Text domain
    --plugin-hook-prefix <prefix>   Hook prefix (e.g. acme_corp_plugin)
    --plugin-human-name <name>      Human-readable name
    --plugin-npm-name <name>        npm package name

  Theme overrides:
    --theme-slug <slug>             Theme directory and slug
    --theme-namespace <ns>          PHP namespace (e.g. AcmeCorpTheme)
    --theme-constant <prefix>       Constant prefix (e.g. ACME_CORP_THEME)
    --theme-text-domain <domain>    Text domain
    --theme-hook-prefix <prefix>    Hook prefix (e.g. acme_corp_theme)
    --theme-human-name <name>       Human-readable name
    --theme-npm-name <name>         npm package name

  Metadata:
    --author-name <name>            Author name
    --author-email <email>          Author email
    --author-uri <uri>              Author URI
    --description <text>            Project description
    --composer-vendor <vendor>      Composer vendor slug
    --homepage-url <url>            Project homepage URL
    --repo-url <url>                Repository URL

  Examples:
    npm run scaffold
    npm run scaffold -- -n "Acme Corp" -t block -h standard -y
    npm run scaffold -- --project-name "Acme Corp" --theme block --hosting vip --yes --self-destruct
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

async function main() {
	const args = parseCli();

	if (args.help) {
		printHelp();
		process.exit(0);
	}

	console.log('\n  WordPress Project Scaffold\n');

	// -----------------------------------------------------------------------
	// 1. Prompts (skipped for any value provided via CLI flags)
	// -----------------------------------------------------------------------

	const hosting =
		args.hosting && ['standard', 'vip'].includes(args.hosting)
			? args.hosting
			: await select({
					message: 'What hosting platform will this project use?',
					choices: [
						{ name: 'Standard WordPress', value: 'standard' },
						{ name: 'WordPress VIP', value: 'vip' },
					],
				});

	const themeType =
		args.theme && ['block', 'classic'].includes(args.theme)
			? args.theme
			: await select({
					message: 'Which theme type would you like to use?',
					choices: [
						{ name: 'Block Theme (Recommended)', value: 'block' },
						{ name: 'Classic Theme', value: 'classic' },
					],
				});

	const projectName = args['project-name']
		? args['project-name']
		: await input({
				message: 'Project name (human-readable, e.g. "Acme Corp"):',
				validate: (v) => (v.trim().length > 0 ? true : 'Project name is required.'),
			});

	const isVip = hosting === 'vip';
	const isBlock = themeType === 'block';
	const slug = toKebab(projectName.trim());

	// Auto-detect git remote
	const gitRemoteUrl = getGitRemoteUrl(ROOT);
	const gitOrg = getGitOrgFromUrl(gitRemoteUrl);

	// -----------------------------------------------------------------------
	// 2. Derive values (CLI flags override auto-derived defaults)
	// -----------------------------------------------------------------------

	const defaults = {
		// Plugin
		pluginSlug: args['plugin-slug'] || `${slug}-plugin`,
		pluginNamespace: args['plugin-namespace'] || `${toPascal(slug)}Plugin`,
		pluginConstant: args['plugin-constant'] || `${toConstant(slug)}_PLUGIN`,
		pluginTextDomain: args['plugin-text-domain'] || `${slug}-plugin`,
		pluginHookPrefix: args['plugin-hook-prefix'] || `${toSnake(slug)}_plugin`,
		pluginHumanName: args['plugin-human-name'] || `${toTitle(slug)} Plugin`,
		pluginNpmName: args['plugin-npm-name'] || `${slug}-plugin`,

		// Theme
		themeSlug: args['theme-slug'] || `${slug}-theme`,
		themeNamespace: args['theme-namespace'] || `${toPascal(slug)}Theme`,
		themeConstant: args['theme-constant'] || `${toConstant(slug)}_THEME`,
		themeTextDomain: args['theme-text-domain'] || `${slug}-theme`,
		themeHookPrefix: args['theme-hook-prefix'] || `${toSnake(slug)}_theme`,
		themeHumanName: args['theme-human-name'] || `${toTitle(slug)} Theme`,
		themeNpmName: args['theme-npm-name'] || `${slug}-theme`,

		// Metadata
		authorName: args['author-name'] || '',
		authorEmail: args['author-email'] || '',
		authorUri: args['author-uri'] || '',
		description: args.description || '',
		composerVendor: args['composer-vendor'] || gitOrg || slug,
		homepageUrl: args['homepage-url'] || '',
		repoUrl: args['repo-url'] || gitRemoteUrl || '',
	};

	// Determine if we are running fully non-interactive (all three required
	// flags were provided via CLI). When non-interactive, skip customization
	// and metadata prompts entirely, using defaults for anything not provided.
	const isNonInteractive = args['project-name'] && args.hosting && args.theme;

	// -----------------------------------------------------------------------
	// 3. Show summary and allow customization
	// -----------------------------------------------------------------------

	const values = { ...defaults };

	if (!isNonInteractive) {
		console.log('\n  Derived values:\n');
		console.log(`  Plugin directory:     ${defaults.pluginSlug}`);
		console.log(`  Plugin namespace:     ${defaults.pluginNamespace}`);
		console.log(`  Plugin constants:     ${defaults.pluginConstant}_*`);
		console.log(`  Plugin text domain:   ${defaults.pluginTextDomain}`);
		console.log(`  Plugin human name:    ${defaults.pluginHumanName}`);
		console.log('');
		console.log(`  Theme directory:      ${defaults.themeSlug}`);
		console.log(`  Theme namespace:      ${defaults.themeNamespace}`);
		console.log(`  Theme constants:      ${defaults.themeConstant}_*`);
		console.log(`  Theme text domain:    ${defaults.themeTextDomain}`);
		console.log(`  Theme human name:     ${defaults.themeHumanName}`);
		console.log('');
		console.log(`  Composer vendor:      ${defaults.composerVendor}`);
		if (defaults.repoUrl) {
			console.log(`  Repository URL:       ${defaults.repoUrl}`);
		}
		console.log('');

		const customize = await select({
			message: 'How would you like to proceed?',
			choices: [
				{ name: 'Accept all derived values and continue to metadata', value: 'accept' },
				{ name: 'Customize each value individually', value: 'customize' },
			],
		});

		if (customize === 'customize') {
			console.log('\n  Plugin configuration:\n');
			values.pluginSlug = await input({
				message: 'Plugin slug / directory:',
				default: defaults.pluginSlug,
			});
			values.pluginNamespace = await input({
				message: 'Plugin PHP namespace:',
				default: defaults.pluginNamespace,
			});
			values.pluginConstant = await input({
				message: 'Plugin constant prefix:',
				default: defaults.pluginConstant,
			});
			values.pluginTextDomain = await input({
				message: 'Plugin text domain:',
				default: defaults.pluginTextDomain,
			});
			values.pluginHookPrefix = await input({
				message: 'Plugin hook prefix:',
				default: defaults.pluginHookPrefix,
			});
			values.pluginHumanName = await input({
				message: 'Plugin human name:',
				default: defaults.pluginHumanName,
			});
			values.pluginNpmName = await input({
				message: 'Plugin npm package name:',
				default: defaults.pluginNpmName,
			});

			console.log('\n  Theme configuration:\n');
			values.themeSlug = await input({
				message: 'Theme slug / directory:',
				default: defaults.themeSlug,
			});
			values.themeNamespace = await input({
				message: 'Theme PHP namespace:',
				default: defaults.themeNamespace,
			});
			values.themeConstant = await input({
				message: 'Theme constant prefix:',
				default: defaults.themeConstant,
			});
			values.themeTextDomain = await input({
				message: 'Theme text domain:',
				default: defaults.themeTextDomain,
			});
			values.themeHookPrefix = await input({
				message: 'Theme hook prefix:',
				default: defaults.themeHookPrefix,
			});
			values.themeHumanName = await input({
				message: 'Theme human name:',
				default: defaults.themeHumanName,
			});
			values.themeNpmName = await input({
				message: 'Theme npm package name:',
				default: defaults.themeNpmName,
			});
		}

		// Prompt for metadata
		console.log('\n  Project metadata:\n');
		values.authorName = await input({
			message: 'Author name:',
			default: defaults.authorName,
		});
		values.authorEmail = await input({
			message: 'Author email:',
			default: defaults.authorEmail,
		});
		values.authorUri = await input({ message: 'Author URI:', default: defaults.authorUri });
		values.description = await input({
			message: 'Project description:',
			default: defaults.description,
		});
		values.composerVendor = await input({
			message: 'Composer vendor slug:',
			default: defaults.composerVendor,
		});
		values.homepageUrl = await input({
			message: 'Homepage URL:',
			default: defaults.homepageUrl,
		});
		values.repoUrl = await input({ message: 'Repository URL:', default: defaults.repoUrl });
	}

	// -----------------------------------------------------------------------
	// 4. Confirm
	// -----------------------------------------------------------------------

	console.log('\n  Summary of changes:\n');
	console.log(`  Hosting:              ${isVip ? 'WordPress VIP' : 'Standard WordPress'}`);
	console.log(`  Theme type:           ${isBlock ? 'Block Theme' : 'Classic Theme'}`);
	console.log(`  Plugin:               ${values.pluginSlug} (${values.pluginNamespace})`);
	console.log(`  Theme:                ${values.themeSlug} (${values.themeNamespace})`);
	if (values.authorName) console.log(`  Author:               ${values.authorName}`);
	if (values.repoUrl) console.log(`  Repository:           ${values.repoUrl}`);
	console.log('');

	const muDir = isVip ? 'client-mu-plugins' : 'mu-plugins';

	// Determine whether to remove the scaffold script after running.
	let selfDestruct = args['self-destruct'] === true;
	if (!selfDestruct && !isNonInteractive) {
		selfDestruct = await confirm({
			message: 'Remove the scaffold script and its dependencies after running?',
			default: true,
		});
	}

	if (!args.yes) {
		const ok = await confirm({ message: 'Apply these changes?', default: true });
		if (!ok) {
			console.log('\n  Aborted. No changes were made.\n');
			process.exit(0);
		}
	}

	// -----------------------------------------------------------------------
	// 5. Execute changes
	// -----------------------------------------------------------------------

	console.log('\n  Applying changes...\n');

	// -- Step 1: Delete unused theme --
	const themeToDelete = isBlock ? 'themes/10up-theme' : 'themes/10up-block-theme';
	const themeToDeletePath = join(ROOT, themeToDelete);
	if (existsSync(themeToDeletePath)) {
		rmSync(themeToDeletePath, { recursive: true, force: true });
		console.log(`  Deleted ${themeToDelete}/`);
	}

	// -- Step 2: Move plugin to client-mu-plugins (VIP) --
	if (isVip) {
		const clientMuDir = join(ROOT, 'client-mu-plugins');
		if (!existsSync(clientMuDir)) {
			mkdirSync(clientMuDir, { recursive: true });
		}

		// Move plugin directory
		const oldPluginDir = join(ROOT, 'mu-plugins', '10up-plugin');
		const newPluginDir = join(clientMuDir, '10up-plugin');
		if (existsSync(oldPluginDir)) {
			renameSync(oldPluginDir, newPluginDir);
			console.log('  Moved mu-plugins/10up-plugin/ -> client-mu-plugins/10up-plugin/');
		}

		// Move loader file
		const oldLoader = join(ROOT, 'mu-plugins', '10up-plugin-loader.php');
		const newLoader = join(clientMuDir, '10up-plugin-loader.php');
		if (existsSync(oldLoader)) {
			renameSync(oldLoader, newLoader);
			console.log(
				'  Moved mu-plugins/10up-plugin-loader.php -> client-mu-plugins/10up-plugin-loader.php',
			);
		}

		// Clean up empty mu-plugins dir if it's now empty
		const muPluginsDir = join(ROOT, 'mu-plugins');
		if (existsSync(muPluginsDir)) {
			const remaining = readdirSync(muPluginsDir);
			if (remaining.length === 0) {
				rmSync(muPluginsDir, { recursive: true, force: true });
			}
		}
	}

	// -- Step 3: Pre-replacement cleanup --
	// Remove the deleted theme's entries from config files BEFORE string
	// replacements run. This prevents duplicate constants (both theme constant
	// sets would otherwise be renamed to the same prefix).

	// 3a: phpstan.neon - remove deleted theme path lines
	const phpstanPath = join(ROOT, 'phpstan.neon');
	if (existsSync(phpstanPath)) {
		let phpstanContent = readFileSync(phpstanPath, 'utf-8');
		// Remove any path line referencing a directory that no longer exists.
		const lines = phpstanContent.split('\n');
		phpstanContent = lines
			.filter((line) => {
				const trimmed = line.trim();
				if (trimmed.startsWith('- ')) {
					const path = trimmed.slice(2).trim();
					const fullPath = join(ROOT, path);
					if (!existsSync(fullPath)) return false;
				}
				return true;
			})
			.join('\n');
		writeFileSync(phpstanPath, phpstanContent);
		console.log('  Cleaned phpstan.neon paths');
	}

	// 3b: phpstan/constants.php - remove the deleted theme's constant block
	const phpstanConstantsPath = join(ROOT, 'phpstan', 'constants.php');
	if (existsSync(phpstanConstantsPath)) {
		let constContent = readFileSync(phpstanConstantsPath, 'utf-8');

		if (isBlock) {
			// Keep TENUP_BLOCK_THEME_* (will be renamed by replacements).
			// Remove TENUP_THEME_* but NOT lines that also match TENUP_BLOCK_THEME_.
			constContent = constContent
				.split('\n')
				.filter((line) => {
					if (line.includes('TENUP_BLOCK_THEME_')) return true;
					if (line.includes('TENUP_THEME_')) return false;
					return true;
				})
				.join('\n');
		} else {
			// Keep TENUP_THEME_* (will be renamed by replacements).
			// Remove TENUP_BLOCK_THEME_*.
			constContent = constContent
				.split('\n')
				.filter((line) => {
					return !line.includes('TENUP_BLOCK_THEME_');
				})
				.join('\n');
		}

		constContent = constContent.replace(/\n{3,}/g, '\n\n');
		writeFileSync(phpstanConstantsPath, constContent);
		console.log('  Cleaned phpstan/constants.php');
	}

	// -- Step 4: Build replacement map --
	// Order: longest match first to avoid partial matches.
	// Also include path prefix replacements for VIP.

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

	// -- Step 4: Apply string replacements across all files --
	const files = walkFiles(ROOT);
	let filesChanged = 0;

	for (const filePath of files) {
		// Skip the scaffold script itself
		if (filePath === resolve(ROOT, 'bin', 'scaffold.mjs')) continue;

		let content;
		try {
			content = readFileSync(filePath, 'utf-8');
		} catch {
			continue;
		}

		let updated = content;

		for (const [search, replace] of replacements) {
			if (updated.includes(search)) {
				updated = updated.replaceAll(search, replace);
			}
		}

		if (updated !== content) {
			writeFileSync(filePath, updated);
			filesChanged++;
		}
	}
	console.log(`  Updated strings in ${filesChanged} files`);

	// -- Step 6: Directory and file renames --

	// Rename plugin directory
	const currentPluginParent = isVip ? join(ROOT, 'client-mu-plugins') : join(ROOT, 'mu-plugins');
	const oldPluginDirName = join(currentPluginParent, '10up-plugin');
	const newPluginDirName = join(currentPluginParent, values.pluginSlug);
	if (existsSync(oldPluginDirName) && oldPluginDirName !== newPluginDirName) {
		renameSync(oldPluginDirName, newPluginDirName);
		console.log(`  Renamed plugin directory to ${values.pluginSlug}/`);
	}

	// Rename theme directory
	const oldThemeDirName = isBlock
		? join(ROOT, 'themes', '10up-block-theme')
		: join(ROOT, 'themes', '10up-theme');
	const newThemeDirName = join(ROOT, 'themes', values.themeSlug);
	if (existsSync(oldThemeDirName) && oldThemeDirName !== newThemeDirName) {
		renameSync(oldThemeDirName, newThemeDirName);
		console.log(`  Renamed theme directory to ${values.themeSlug}/`);
	}

	// Rename loader file
	const oldLoaderName = join(currentPluginParent, '10up-plugin-loader.php');
	const newLoaderName = join(currentPluginParent, `${values.pluginSlug}-loader.php`);
	if (existsSync(oldLoaderName) && oldLoaderName !== newLoaderName) {
		renameSync(oldLoaderName, newLoaderName);
		console.log(`  Renamed loader to ${values.pluginSlug}-loader.php`);
	}

	// Rename .pot files
	const pluginPotOld = join(newPluginDirName, 'languages', 'TenUpPlugin.pot');
	const pluginPotNew = join(newPluginDirName, 'languages', `${values.pluginNamespace}.pot`);
	if (existsSync(pluginPotOld) && pluginPotOld !== pluginPotNew) {
		renameSync(pluginPotOld, pluginPotNew);
		console.log(`  Renamed TenUpPlugin.pot to ${values.pluginNamespace}.pot`);
	}

	if (!isBlock) {
		const themePotOld = join(newThemeDirName, 'languages', 'TenUpTheme.pot');
		const themePotNew = join(newThemeDirName, 'languages', `${values.themeNamespace}.pot`);
		if (existsSync(themePotOld) && themePotOld !== themePotNew) {
			renameSync(themePotOld, themePotNew);
			console.log(`  Renamed TenUpTheme.pot to ${values.themeNamespace}.pot`);
		}
	}

	// -- Step 7: Post-rename fixups --
	// Fix globalStylesDir/globalMixinsDir in plugin package.json.
	// After rename, the plugin is at its new location. The path references
	// "10up-theme" which may not have been caught if block theme was chosen.
	const renamedPluginPkg = join(newPluginDirName, 'package.json');
	if (existsSync(renamedPluginPkg)) {
		let pluginPkgContent = readFileSync(renamedPluginPkg, 'utf-8');
		// Replace any remaining old theme directory references in paths
		if (isBlock) {
			pluginPkgContent = pluginPkgContent.replaceAll('10up-theme', values.themeSlug);
			pluginPkgContent = pluginPkgContent.replaceAll('10up-block-theme', values.themeSlug);
		} else {
			pluginPkgContent = pluginPkgContent.replaceAll('10up-theme', values.themeSlug);
		}
		writeFileSync(renamedPluginPkg, pluginPkgContent);
	}

	// Fix the loader file content to point to the renamed directory.
	// The loader file does: require_once __DIR__ . '/10up-plugin/plugin.php';
	// After string replacement, "10up-plugin" was already replaced, but double check.
	if (existsSync(newLoaderName)) {
		let loaderContent = readFileSync(newLoaderName, 'utf-8');
		loaderContent = loaderContent.replaceAll('10up-plugin', values.pluginSlug);
		writeFileSync(newLoaderName, loaderContent);
	}

	// -- Step 8: Cleanup --

	// Delete package-lock.json
	const lockPath = join(ROOT, 'package-lock.json');
	if (existsSync(lockPath)) {
		rmSync(lockPath);
		console.log('  Deleted package-lock.json');
	}

	// Delete composer.lock files
	for (const lockFile of [
		join(ROOT, 'composer.lock'),
		join(newPluginDirName, 'composer.lock'),
		join(newThemeDirName, 'composer.lock'),
	]) {
		if (existsSync(lockFile)) {
			rmSync(lockFile);
			console.log(`  Deleted ${lockFile.replace(`${ROOT}/`, '')}`);
		}
	}

	// -- Self-destruct: remove scaffold script and dependencies --
	if (selfDestruct) {
		const rootPkgPath = join(ROOT, 'package.json');
		if (existsSync(rootPkgPath)) {
			const rootPkg = JSON.parse(readFileSync(rootPkgPath, 'utf-8'));
			if (rootPkg.scripts?.scaffold) {
				delete rootPkg.scripts.scaffold;
			}
			if (rootPkg.devDependencies?.['@inquirer/prompts']) {
				delete rootPkg.devDependencies['@inquirer/prompts'];
			}
			writeFileSync(rootPkgPath, `${JSON.stringify(rootPkg, null, '  ')}\n`);
			console.log('  Removed scaffold script and @inquirer/prompts from package.json');
		}

		// Delete the scaffold script file
		const scriptPath = resolve(ROOT, 'bin', 'scaffold.mjs');
		if (existsSync(scriptPath)) {
			rmSync(scriptPath);
			console.log('  Deleted bin/scaffold.mjs');
		}

		// Remove the bin/ directory if it is now empty
		const binDir = resolve(ROOT, 'bin');
		if (existsSync(binDir) && readdirSync(binDir).length === 0) {
			rmSync(binDir, { recursive: true });
			console.log('  Deleted empty bin/ directory');
		}
	}

	// -- Done! --
	console.log('\n  Done! Your project has been scaffolded.\n');
	console.log('  Next steps:\n');
	console.log('    1. Run npm install');
	console.log(
		`    2. Run composer install in the root, ${muDir}/${values.pluginSlug}, and themes/${values.themeSlug}`,
	);
	console.log('    3. Run npm run build');
	console.log('');
}

main().catch((err) => {
	if (err.name === 'ExitPromptError') {
		console.log('\n  Aborted.\n');
		process.exit(0);
	}
	console.error(err);
	process.exit(1);
});
