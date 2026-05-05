#!/usr/bin/env node

/**
 * WordPress VIP Overlay Scaffold CLI
 *
 * Overlays the 10up wp-scaffold onto an existing WordPress VIP repo,
 * merging configs and preserving VIP-specific files.
 *
 * Run with: npm run scaffold:vip -- --target /path/to/vip-repo
 */

import { select, input, confirm } from '@inquirer/prompts';
import { execSync } from 'node:child_process';
import {
	cpSync,
	existsSync,
	mkdirSync,
	readFileSync,
	readdirSync,
	renameSync,
	rmSync,
	writeFileSync,
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
	buildReplacementMap,
} from './scaffold-helpers.mjs';

// ---------------------------------------------------------------------------
// Constants
// ---------------------------------------------------------------------------

const SOURCE = resolve(import.meta.dirname, '..');

// ---------------------------------------------------------------------------
// CLI argument parsing
// ---------------------------------------------------------------------------

const CLI_OPTIONS = {
	// Core
	target: { type: 'string' },
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

	// Ignite
	fueled: { type: 'boolean', default: false },
	ignite: { type: 'boolean', default: false },
	'skip-ignite': { type: 'boolean', default: false },
};

function printHelp() {
	console.log(`
  Usage: npm run scaffold:vip -- --target <path> [options]

  Overlays the 10up wp-scaffold onto an existing WordPress VIP repo.

  Required:
    --target <path>              Path to the existing VIP repo

  Options:
    -n, --project-name <name>    Project name (e.g. "Acme Corp")
    -t, --theme <type>           Theme type: "block" or "classic"
    -y, --yes                    Skip confirmation prompt
    --self-destruct              Remove scaffold scripts from target after running

  Plugin overrides:
    --plugin-slug <slug>         Plugin directory and slug
    --plugin-namespace <ns>      PHP namespace (e.g. AcmeCorpPlugin)
    --plugin-constant <prefix>   Constant prefix (e.g. ACME_CORP_PLUGIN)
    --plugin-text-domain <domain> Text domain
    --plugin-hook-prefix <prefix> Hook prefix (e.g. acme_corp_plugin)
    --plugin-human-name <name>   Human-readable name
    --plugin-npm-name <name>     npm package name

  Theme overrides:
    --theme-slug <slug>          Theme directory and slug
    --theme-namespace <ns>       PHP namespace (e.g. AcmeCorpTheme)
    --theme-constant <prefix>    Constant prefix (e.g. ACME_CORP_THEME)
    --theme-text-domain <domain> Text domain
    --theme-hook-prefix <prefix> Hook prefix (e.g. acme_corp_theme)
    --theme-human-name <name>    Human-readable name
    --theme-npm-name <name>      npm package name

  Metadata:
    --author-name <name>         Author name
    --author-email <email>       Author email
    --author-uri <uri>           Author URI
    --description <text>         Project description
    --composer-vendor <vendor>   Composer vendor slug
    --homepage-url <url>         Project homepage URL
    --repo-url <url>             Repository URL

  Ignite:
    --fueled                     Mark as a Fueled/10up project (defaults to block theme + Ignite)
    --ignite                     Install Ignite WP plugins after scaffolding
    --skip-ignite                Skip Ignite installation (useful with --fueled)

  Examples:
    npm run scaffold:vip -- --target /path/to/vip-repo
    npm run scaffold:vip -- --target /path/to/vip-repo -n "Acme Corp" -t block -y
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
// Validation helpers
// ---------------------------------------------------------------------------

function validateTarget(targetPath) {
	if (!existsSync(targetPath)) {
		console.error(`\n  Error: Target path does not exist: ${targetPath}\n`);
		process.exit(1);
	}

	const markers = [
		join(targetPath, 'vip-config'),
		join(targetPath, '.deployignore'),
		join(targetPath, 'client-mu-plugins'),
	];
	const found = markers.filter((m) => existsSync(m));

	if (found.length === 0) {
		console.error(
			'\n  Error: Target does not appear to be a VIP repo.' +
				'\n  Expected at least one of: vip-config/, .deployignore, client-mu-plugins/\n',
		);
		process.exit(1);
	}
}

function validateSource() {
	const required = [
		join(SOURCE, 'mu-plugins', '10up-plugin'),
		join(SOURCE, 'themes', '10up-block-theme'),
		join(SOURCE, 'themes', '10up-theme'),
		join(SOURCE, 'bin', 'scaffold.mjs'),
	];
	for (const p of required) {
		if (!existsSync(p)) {
			console.error(`\n  Error: Required source file missing: ${p}\n`);
			process.exit(1);
		}
	}
}

function checkGitStatus(targetPath) {
	try {
		const status = execSync('git status --porcelain', {
			cwd: targetPath,
			encoding: 'utf-8',
		}).trim();
		if (status) {
			console.log(
				'\n  Warning: Target repo has uncommitted changes.' +
					'\n  Consider committing first so the overlay can be reviewed as a single diff.\n',
			);
		}
	} catch {
		// Not a git repo or git not available — that's fine
	}
}

// ---------------------------------------------------------------------------
// Config merge helpers
// ---------------------------------------------------------------------------

function mergeComposerJson(targetPath) {
	const ourComposer = JSON.parse(readFileSync(join(SOURCE, 'composer.json'), 'utf-8'));
	const vipComposer = JSON.parse(readFileSync(join(targetPath, 'composer.json'), 'utf-8'));

	// Merge require-dev: ours + theirs, minus VIP-provided debug plugins
	const mergedRequireDev = { ...ourComposer['require-dev'] };
	delete mergedRequireDev['wpackagist-plugin/debug-bar'];
	delete mergedRequireDev['wpackagist-plugin/query-monitor'];
	delete mergedRequireDev['wpackagist-plugin/debug-bar-slow-actions'];

	// Add VIP-specific dev deps
	if (vipComposer['require-dev']) {
		Object.assign(mergedRequireDev, vipComposer['require-dev']);
	}

	// Build merged composer.json with VIP paths
	const scripts = { ...ourComposer.scripts };
	if (scripts.setup) {
		scripts.setup = scripts.setup.map((cmd) =>
			cmd.replace('mu-plugins/', 'client-mu-plugins/'),
		);
	}
	if (scripts['setup:local']) {
		scripts['setup:local'] = scripts['setup:local'].map((cmd) =>
			cmd.replace('mu-plugins/', 'client-mu-plugins/'),
		);
	}

	const merged = {
		name: ourComposer.name,
		description: ourComposer.description,
		license: vipComposer.license || 'GPL-2.0-or-later',
		authors: ourComposer.authors,
		repositories: ourComposer.repositories,
		require: { php: ourComposer.require.php },
		'minimum-stability': ourComposer['minimum-stability'],
		'prefer-stable': ourComposer['prefer-stable'],
		'require-dev': mergedRequireDev,
		scripts,
		extra: ourComposer.extra,
		config: {
			'allow-plugins': {
				...(ourComposer.config?.['allow-plugins'] || {}),
				...(vipComposer.config?.['allow-plugins'] || {}),
			},
		},
	};

	writeFileSync(join(targetPath, 'composer.json'), `${JSON.stringify(merged, null, '  ')}\n`);
	console.log('  Merged composer.json');
}

function writePackageJson(targetPath) {
	const ourPkg = JSON.parse(readFileSync(join(SOURCE, 'package.json'), 'utf-8'));

	// Update workspace path for VIP
	ourPkg.workspaces = ourPkg.workspaces.map((ws) =>
		ws.replace('mu-plugins/10up-plugin', 'client-mu-plugins/10up-plugin'),
	);

	// Add scaffold:vip script
	ourPkg.scripts['scaffold:vip'] = 'node bin/scaffold-vip.mjs';

	writeFileSync(join(targetPath, 'package.json'), `${JSON.stringify(ourPkg, null, '  ')}\n`);
	console.log('  Wrote package.json');
}

function mergeGitignore(targetPath) {
	const content = `# Dependencies
node_modules
bower_components
/vendor/

# Uploads directory
/uploads/

# Leftover core/plugin upgrade files
/upgrade/

# mu-plugins; these are managed at the platform-level
/mu-plugins/

# drop-ins; these are managed at the platform-level
/object-cache.php
/db.php

# Composer-managed plugins
plugins

# Project files
dist
backstop_data
release
cache

# Editors
*.esproj
*.tmproj
*.tmproject
tmtags
.*.sw[a-z]
*.un~
Session.vim
*.swp
.vscode/launch.json
tasks.json
.idea

# Mac OSX
.DS_Store
.DS_Store?
._*
.Spotlight-V100
.Trashes

# Windows
Thumbs.db
ehthumbs.db
Desktop.ini

# Misc
/debug.log
phpunit.xml
`;

	writeFileSync(join(targetPath, '.gitignore'), content);
	console.log('  Merged .gitignore');
}

function writeEditorconfig(targetPath) {
	const content = `root = true

[*]
charset = utf-8
end_of_line = lf
insert_final_newline = true
trim_trailing_whitespace = true
indent_style = tab

[{*.json,*.yml,.babelrc,.bowerrc,.browserslistrc,.postcssrc, .eslintrc, .stylelintrc}]
indent_style = space
indent_size = 2

[*.md]
trim_trailing_whitespace = false

[{*.txt,wp-config-sample.php}]
end_of_line = crlf
`;

	writeFileSync(join(targetPath, '.editorconfig'), content);
	console.log('  Merged .editorconfig');
}

function writePhpcsXml(targetPath) {
	const content = `<?xml version="1.0"?>
<ruleset name="10up VIP PHPCS">
	<description>10up PHPCS extended for WordPress VIP.</description>

	<!-- Scan these directories -->
	<file>themes</file>
	<file>client-mu-plugins</file>

	<!-- Don't scan these directories -->
	<exclude-pattern>node_modules/</exclude-pattern>
	<exclude-pattern>vendor/</exclude-pattern>
	<exclude-pattern>dist/</exclude-pattern>

	<!-- Use VIP ruleset (higher precedence) and 10up ruleset -->
	<rule ref="WordPress-VIP-Go" />
	<rule ref="10up-Default" />

	<!-- PHP Compatibility -->
	<rule ref="PHPCompatibilityWP"/>
	<config name="testVersion" value="8.3-"/>

	<!-- VIP minimum WP version -->
	<config name="minimum_supported_wp_version" value="6.8"/>

	<!-- Ignore filecomment for the plugin loader -->
	<rule ref="Squiz.Commenting.FileComment.Missing">
		<exclude-pattern>./client-mu-plugins/plugin-loader.php</exclude-pattern>
	</rule>

	<arg value="sp"/> <!-- Show sniff and progress -->
	<arg name="colors"/> <!-- Show results with colors -->
	<arg name="basepath" value="."/> <!-- Strip the file paths down to the relevant bit -->
	<arg name="parallel" value="8"/> <!-- Parallel processing -->
	<arg name="extensions" value="php"/> <!-- Limit to PHP -->
	<arg name="severity" value="1"/> <!-- Match VIP Code Analysis Bot defaults -->
</ruleset>
`;

	writeFileSync(join(targetPath, 'phpcs.xml'), content);
	console.log('  Wrote phpcs.xml');
}

function writePhpstanNeon(targetPath, isBlock) {
	const themePath = isBlock ? 'themes/10up-block-theme' : 'themes/10up-theme';
	const content = `includes:
    - phpstan/default.neon

parameters:
    paths:
        - ${themePath}
        - client-mu-plugins/10up-plugin
`;

	writeFileSync(join(targetPath, 'phpstan.neon'), content);
	console.log('  Wrote phpstan.neon');
}

function appendDeployignore(targetPath) {
	const deployignorePath = join(targetPath, '.deployignore');
	if (!existsSync(deployignorePath)) return;

	let content = readFileSync(deployignorePath, 'utf-8');

	const additions = [
		'phpstan.neon',
		'phpstan/',
		'.lintstagedrc.json',
		'.npmrc',
		'.nvmrc',
		'.eslintrc',
		'stylelint.config.js',
		'bin/',
		'.husky/',
	];

	// Only add lines that aren't already present
	const newLines = additions.filter((line) => !content.includes(line));
	if (newLines.length > 0) {
		if (!content.endsWith('\n')) content += '\n';
		content += '\n# 10up development tooling\n';
		content += newLines.join('\n') + '\n';
		writeFileSync(deployignorePath, content);
		console.log('  Updated .deployignore');
	}
}

function updatePluginLoader(targetPath) {
	const loaderPath = join(targetPath, 'client-mu-plugins', 'plugin-loader.php');
	if (!existsSync(loaderPath)) {
		console.log('  Warning: client-mu-plugins/plugin-loader.php not found, skipping loader update');
		return;
	}

	let content = readFileSync(loaderPath, 'utf-8');

	// Check if our plugin is already loaded
	if (content.includes('10up-plugin/plugin.php')) {
		console.log('  Plugin already referenced in plugin-loader.php');
		return;
	}

	// Append our require line
	if (!content.endsWith('\n')) content += '\n';
	content += `\nrequire __DIR__ . '/10up-plugin/plugin.php';\n`;

	writeFileSync(loaderPath, content);
	console.log('  Updated client-mu-plugins/plugin-loader.php with plugin loader');
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

	console.log('\n  WordPress VIP Overlay Scaffold\n');

	// -----------------------------------------------------------------------
	// 1. Validate target
	// -----------------------------------------------------------------------

	if (!args.target) {
		console.error('\n  Error: --target is required. Provide the path to the VIP repo.\n');
		printHelp();
		process.exit(1);
	}

	const TARGET = resolve(args.target);
	validateTarget(TARGET);
	validateSource();
	checkGitStatus(TARGET);

	if (resolve(TARGET) === resolve(SOURCE)) {
		console.error('\n  Error: Target cannot be the same as the source scaffold repo.\n');
		process.exit(1);
	}

	// -----------------------------------------------------------------------
	// 2. Prompts (same as scaffold.mjs, but hosting is always VIP)
	// -----------------------------------------------------------------------

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

	// Fueled / Ignite prompts
	const isNonInteractiveEarly = args['project-name'] && args.theme;

	const isFueled =
		args.fueled === true
			? true
			: isNonInteractiveEarly
				? false
				: await confirm({
						message: 'Is this a Fueled / 10up project?',
						default: true,
					});

	if (isFueled && themeType === 'classic' && !args.theme) {
		console.log('\n  Note: Block themes are recommended for Fueled/10up projects.\n');
	}

	const installIgnite = args['skip-ignite']
		? false
		: args.ignite === true || (isFueled && isNonInteractiveEarly)
			? true
			: isNonInteractiveEarly
				? false
				: isFueled
					? await confirm({
							message:
								'Install Ignite WP plugins? (Recommended for Fueled/10up projects)',
							default: true,
						})
					: await confirm({
							message: 'Would you like to install Ignite WP plugins?',
							default: false,
						});

	const projectName = args['project-name']
		? args['project-name']
		: await input({
				message: 'Project name (human-readable, e.g. "Acme Corp"):',
				validate: (v) => (v.trim().length > 0 ? true : 'Project name is required.'),
			});

	const isBlock = themeType === 'block';
	const slug = toKebab(projectName.trim());

	// Auto-detect git remote from the target repo
	const gitRemoteUrl = getGitRemoteUrl(TARGET);
	const gitOrg = getGitOrgFromUrl(gitRemoteUrl);

	// -----------------------------------------------------------------------
	// 3. Derive values
	// -----------------------------------------------------------------------

	const defaults = {
		pluginSlug: args['plugin-slug'] || `${slug}-plugin`,
		pluginNamespace: args['plugin-namespace'] || `${toPascal(slug)}Plugin`,
		pluginConstant: args['plugin-constant'] || `${toConstant(slug)}_PLUGIN`,
		pluginTextDomain: args['plugin-text-domain'] || `${slug}-plugin`,
		pluginHookPrefix: args['plugin-hook-prefix'] || `${toSnake(slug)}_plugin`,
		pluginHumanName: args['plugin-human-name'] || `${toTitle(slug)} Plugin`,
		pluginNpmName: args['plugin-npm-name'] || `${slug}-plugin`,

		themeSlug: args['theme-slug'] || `${slug}-theme`,
		themeNamespace: args['theme-namespace'] || `${toPascal(slug)}Theme`,
		themeConstant: args['theme-constant'] || `${toConstant(slug)}_THEME`,
		themeTextDomain: args['theme-text-domain'] || `${slug}-theme`,
		themeHookPrefix: args['theme-hook-prefix'] || `${toSnake(slug)}_theme`,
		themeHumanName: args['theme-human-name'] || `${toTitle(slug)} Theme`,
		themeNpmName: args['theme-npm-name'] || `${slug}-theme`,

		authorName: args['author-name'] || '',
		authorEmail: args['author-email'] || '',
		authorUri: args['author-uri'] || '',
		description: args.description || '',
		composerVendor: args['composer-vendor'] || gitOrg || slug,
		homepageUrl: args['homepage-url'] || '',
		repoUrl: args['repo-url'] || gitRemoteUrl || '',
	};

	const isNonInteractive = args['project-name'] && args.theme;
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
	console.log(`  Target:               ${TARGET}`);
	console.log(`  Hosting:              WordPress VIP`);
	console.log(`  Theme type:           ${isBlock ? 'Block Theme' : 'Classic Theme'}`);
	console.log(`  Plugin:               ${values.pluginSlug} (${values.pluginNamespace})`);
	console.log(`  Theme:                ${values.themeSlug} (${values.themeNamespace})`);
	console.log(`  Fueled project:       ${isFueled ? 'Yes' : 'No'}`);
	if (installIgnite) console.log(`  Ignite WP:            Will install after scaffolding`);
	if (values.authorName) console.log(`  Author:               ${values.authorName}`);
	if (values.repoUrl) console.log(`  Repository:           ${values.repoUrl}`);
	console.log('');

	// Determine whether to remove the scaffold script after running.
	let selfDestruct = args['self-destruct'] === true;
	if (!selfDestruct && !isNonInteractive) {
		selfDestruct = await confirm({
			message: 'Remove the scaffold scripts and their dependencies after running?',
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
	// 5. Execute: Copy our files to target
	// -----------------------------------------------------------------------

	console.log('\n  Applying changes...\n');

	// -- Copy directories --
	const dirCopies = [
		['bin', 'bin'],
		['.husky', '.husky'],
		['.vscode', '.vscode'],
		['phpstan', 'phpstan'],
		['docs', 'docs'],
	];

	for (const [src, dest] of dirCopies) {
		const srcPath = join(SOURCE, src);
		const destPath = join(TARGET, dest);
		if (existsSync(srcPath)) {
			cpSync(srcPath, destPath, { recursive: true });
			console.log(`  Copied ${src}/`);
		}
	}

	// Copy .github/workflows/
	const ghSrc = join(SOURCE, '.github', 'workflows');
	const ghDest = join(TARGET, '.github', 'workflows');
	if (existsSync(ghSrc)) {
		mkdirSync(ghDest, { recursive: true });
		cpSync(ghSrc, ghDest, { recursive: true });
		console.log('  Copied .github/workflows/');
	}

	// -- Copy chosen theme --
	const themeSrcName = isBlock ? '10up-block-theme' : '10up-theme';
	const themeSrc = join(SOURCE, 'themes', themeSrcName);
	const themeDest = join(TARGET, 'themes', themeSrcName);
	if (existsSync(themeSrc)) {
		cpSync(themeSrc, themeDest, { recursive: true });
		console.log(`  Copied themes/${themeSrcName}/`);
	}

	// -- Copy plugin to client-mu-plugins --
	const pluginSrc = join(SOURCE, 'mu-plugins', '10up-plugin');
	const pluginDest = join(TARGET, 'client-mu-plugins', '10up-plugin');
	if (existsSync(pluginSrc)) {
		mkdirSync(join(TARGET, 'client-mu-plugins'), { recursive: true });
		cpSync(pluginSrc, pluginDest, { recursive: true });
		console.log('  Copied mu-plugins/10up-plugin/ -> client-mu-plugins/10up-plugin/');
	}

	// -- Copy individual files --
	const fileCopies = ['.eslintrc', 'stylelint.config.js', '.lintstagedrc.json', '.npmrc', '.nvmrc'];
	for (const file of fileCopies) {
		const srcPath = join(SOURCE, file);
		if (existsSync(srcPath)) {
			cpSync(srcPath, join(TARGET, file));
			console.log(`  Copied ${file}`);
		}
	}

	// -----------------------------------------------------------------------
	// 6. Delete replaced files from target
	// -----------------------------------------------------------------------

	const deletions = [
		'themes/twentytwentyfive',
		'plugins/hello.php',
		'.phpcs.xml.dist',
		'composer.lock',
	];

	for (const rel of deletions) {
		const fullPath = join(TARGET, rel);
		if (existsSync(fullPath)) {
			rmSync(fullPath, { recursive: true, force: true });
			console.log(`  Deleted ${rel}`);
		}
	}

	// -----------------------------------------------------------------------
	// 7. Merge config files
	// -----------------------------------------------------------------------

	mergeComposerJson(TARGET);
	writePackageJson(TARGET);
	mergeGitignore(TARGET);
	writeEditorconfig(TARGET);
	writePhpcsXml(TARGET);
	writePhpstanNeon(TARGET, isBlock);
	appendDeployignore(TARGET);
	updatePluginLoader(TARGET);

	// -----------------------------------------------------------------------
	// 8. Pre-replacement cleanup (phpstan)
	// -----------------------------------------------------------------------

	// Remove the deleted theme's entries from phpstan.neon BEFORE string
	// replacements run (same logic as scaffold.mjs).
	const phpstanPath = join(TARGET, 'phpstan.neon');
	if (existsSync(phpstanPath)) {
		let phpstanContent = readFileSync(phpstanPath, 'utf-8');
		const lines = phpstanContent.split('\n');
		phpstanContent = lines
			.filter((line) => {
				const trimmed = line.trim();
				if (trimmed.startsWith('- ')) {
					const path = trimmed.slice(2).trim();
					const fullPath = join(TARGET, path);
					if (!existsSync(fullPath)) return false;
				}
				return true;
			})
			.join('\n');
		writeFileSync(phpstanPath, phpstanContent);
	}

	// phpstan/constants.php - remove the deleted theme's constant block
	const phpstanConstantsPath = join(TARGET, 'phpstan', 'constants.php');
	if (existsSync(phpstanConstantsPath)) {
		let constContent = readFileSync(phpstanConstantsPath, 'utf-8');

		if (isBlock) {
			constContent = constContent
				.split('\n')
				.filter((line) => {
					if (line.includes('TENUP_BLOCK_THEME_')) return true;
					if (line.includes('TENUP_THEME_')) return false;
					return true;
				})
				.join('\n');
		} else {
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

	// -----------------------------------------------------------------------
	// 9. String replacements
	// -----------------------------------------------------------------------

	// Pass isVip: false because the overlay already handles all VIP path
	// conversions (mu-plugins -> client-mu-plugins) during the copy and merge
	// phases. The VIP replacements in buildReplacementMap would double-prefix
	// paths that are already correct (e.g. client-mu-plugins -> client-client-mu-plugins).
	const replacements = buildReplacementMap({ isBlock, isVip: false, values, slug });
	const files = walkFiles(TARGET);
	let filesChanged = 0;

	for (const filePath of files) {
		// Skip scaffold scripts themselves
		if (filePath === resolve(TARGET, 'bin', 'scaffold.mjs')) continue;
		if (filePath === resolve(TARGET, 'bin', 'scaffold-vip.mjs')) continue;
		if (filePath === resolve(TARGET, 'bin', 'scaffold-plugin.mjs')) continue;

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

	// -----------------------------------------------------------------------
	// 10. Directory and file renames
	// -----------------------------------------------------------------------

	// Rename plugin directory
	const oldPluginDir = join(TARGET, 'client-mu-plugins', '10up-plugin');
	const newPluginDir = join(TARGET, 'client-mu-plugins', values.pluginSlug);
	if (existsSync(oldPluginDir) && oldPluginDir !== newPluginDir) {
		renameSync(oldPluginDir, newPluginDir);
		console.log(`  Renamed plugin directory to ${values.pluginSlug}/`);
	}

	// Rename theme directory
	const oldThemeDir = join(TARGET, 'themes', themeSrcName);
	const newThemeDir = join(TARGET, 'themes', values.themeSlug);
	if (existsSync(oldThemeDir) && oldThemeDir !== newThemeDir) {
		renameSync(oldThemeDir, newThemeDir);
		console.log(`  Renamed theme directory to ${values.themeSlug}/`);
	}

	// Rename .pot files
	const pluginPotOld = join(newPluginDir, 'languages', 'TenUpPlugin.pot');
	const pluginPotNew = join(newPluginDir, 'languages', `${values.pluginNamespace}.pot`);
	if (existsSync(pluginPotOld) && pluginPotOld !== pluginPotNew) {
		renameSync(pluginPotOld, pluginPotNew);
		console.log(`  Renamed TenUpPlugin.pot to ${values.pluginNamespace}.pot`);
	}

	if (!isBlock) {
		const themePotOld = join(newThemeDir, 'languages', 'TenUpTheme.pot');
		const themePotNew = join(newThemeDir, 'languages', `${values.themeNamespace}.pot`);
		if (existsSync(themePotOld) && themePotOld !== themePotNew) {
			renameSync(themePotOld, themePotNew);
			console.log(`  Renamed TenUpTheme.pot to ${values.themeNamespace}.pot`);
		}
	}

	// -- Post-rename fixups --

	// Fix plugin package.json theme path references
	const renamedPluginPkg = join(newPluginDir, 'package.json');
	if (existsSync(renamedPluginPkg)) {
		let pluginPkgContent = readFileSync(renamedPluginPkg, 'utf-8');
		if (isBlock) {
			pluginPkgContent = pluginPkgContent.replaceAll('10up-theme', values.themeSlug);
			pluginPkgContent = pluginPkgContent.replaceAll('10up-block-theme', values.themeSlug);
		} else {
			pluginPkgContent = pluginPkgContent.replaceAll('10up-theme', values.themeSlug);
		}
		writeFileSync(renamedPluginPkg, pluginPkgContent);
	}

	// -----------------------------------------------------------------------
	// 11. Cleanup
	// -----------------------------------------------------------------------

	// Delete package-lock.json
	const lockPath = join(TARGET, 'package-lock.json');
	if (existsSync(lockPath)) {
		rmSync(lockPath);
		console.log('  Deleted package-lock.json');
	}

	// Delete composer.lock files
	for (const lockFile of [
		join(TARGET, 'composer.lock'),
		join(newPluginDir, 'composer.lock'),
		join(newThemeDir, 'composer.lock'),
	]) {
		if (existsSync(lockFile)) {
			rmSync(lockFile);
			console.log(`  Deleted ${lockFile.replace(`${TARGET}/`, '')}`);
		}
	}

	// -- Self-destruct: remove scaffold scripts and dependencies --
	if (selfDestruct) {
		const rootPkgPath = join(TARGET, 'package.json');
		if (existsSync(rootPkgPath)) {
			const rootPkg = JSON.parse(readFileSync(rootPkgPath, 'utf-8'));
			if (rootPkg.scripts?.scaffold) delete rootPkg.scripts.scaffold;
			if (rootPkg.scripts?.['scaffold:vip']) delete rootPkg.scripts['scaffold:vip'];
			if (rootPkg.scripts?.['scaffold:plugin']) delete rootPkg.scripts['scaffold:plugin'];
			if (rootPkg.devDependencies?.['@inquirer/prompts']) {
				delete rootPkg.devDependencies['@inquirer/prompts'];
			}
			if (rootPkg.devDependencies?.tar) {
				delete rootPkg.devDependencies.tar;
			}
			writeFileSync(rootPkgPath, `${JSON.stringify(rootPkg, null, '  ')}\n`);
			console.log('  Removed scaffold scripts and deps from package.json');
		}

		const binDir = join(TARGET, 'bin');
		if (existsSync(binDir)) {
			rmSync(binDir, { recursive: true, force: true });
			console.log('  Deleted bin/ directory');
		}
	}

	// -- Done! --
	console.log('\n  Done! Your VIP project has been scaffolded.\n');

	// -- Run Ignite CLI if requested --
	if (installIgnite) {
		console.log('  Installing Ignite WP plugins...\n');
		try {
			execSync('npx @10up/ignite-cli install', {
				cwd: TARGET,
				stdio: 'inherit',
			});
			console.log('\n  Ignite WP plugins installed successfully.\n');
		} catch {
			console.log('\n  Ignite WP plugin installation failed or was cancelled.');
			console.log('  You can run it manually later: npx @10up/ignite-cli install\n');
		}
	}

	const muDir = 'client-mu-plugins';
	console.log('  Next steps:\n');
	console.log('    1. Run npm install');
	console.log(
		`    2. Run composer install in the root, ${muDir}/${values.pluginSlug}, and themes/${values.themeSlug}`,
	);
	console.log('    3. Run npm run build');
	if (installIgnite) {
		console.log('    4. Activate the Ignite plugins you installed');
	}
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
