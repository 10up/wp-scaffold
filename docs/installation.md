# Installation Guide

This guide provides step-by-step instructions for installing and configuring a WP Scaffold project using Local WP.

## Table of Contents
- [System Requirements](#system-requirements)
- [Cloning the Repository](#cloning-the-repository)
- [Setting Up with Local WP](#setting-up-with-local-wp)
- [Installing Dependencies](#installing-dependencies)
- [Running the Scaffold CLI](#running-the-scaffold-cli)
- [Database Setup](#database-setup)
- [Troubleshooting](#troubleshooting)

## System Requirements
- Local WP (https://localwp.com/)
- Git
- Composer (for PHP dependencies)
- Node.js >= 20 and npm >= 9 (for JS dependencies)

## Cloning the Repository

```bash
git clone <your-repo-url>
```

## Setting Up with Local WP
1. Open Local WP and create a new site.
2. Choose 'Custom' setup and set the site path to your cloned repository.
3. Set PHP version, web server, and database as needed.
4. Ensure the `wp-content` directory in Local WP points to your project's `wp-content` folder.

## Installing Dependencies

Open a terminal in the `wp-content` directory and run:

```bash
npm install
```

## Running the Scaffold CLI

Before you start developing, run the scaffold CLI to replace all placeholder names with your project's actual names. This is a one-time step that renames namespaces, constants, slugs, directories, config files, and translation files throughout the codebase.

### Interactive mode

```bash
npm run scaffold
```

The CLI walks you through a series of prompts:

1. **Hosting platform** - Standard WordPress or WordPress VIP. VIP projects move the mu-plugin into `client-mu-plugins/` instead of `mu-plugins/`.
2. **Theme type** - Block Theme (recommended) or Classic Theme. The theme you do not choose is deleted.
3. **Project name** - A human-readable name like "Acme Corp". All other naming conventions (slugs, namespaces, constants, text domains, hook prefixes) are derived from this automatically.
4. **Customization** - You can accept all derived values or customize each one individually.
5. **Metadata** - Author name, email, URI, project description, Composer vendor, homepage, and repository URL. The repository URL is auto-detected from your Git remote when available.
6. **Self-destruct** - Whether to remove the scaffold script and its dependencies after running. This is recommended for production projects since the script is only needed once.

### Non-interactive mode

You can skip the prompts entirely by passing all required values as flags. This is useful for CI pipelines or when you already know exactly what you want:

```bash
npm run scaffold -- \
  --project-name "Acme Corp" \
  --theme block \
  --hosting standard \
  --yes \
  --self-destruct
```

When the three required flags (`--project-name`, `--hosting`, `--theme`) are all provided, the script runs in fully non-interactive mode. It uses auto-derived defaults for any value you do not explicitly pass.

### All available flags

| Flag | Short | Description |
|------|-------|-------------|
| `--project-name` | `-n` | Project name (e.g. "Acme Corp") |
| `--hosting` | `-h` | Hosting platform: `standard` or `vip` |
| `--theme` | `-t` | Theme type: `block` or `classic` |
| `--yes` | `-y` | Skip the confirmation prompt |
| `--self-destruct` | | Remove the scaffold script after running |
| `--plugin-slug` | | Plugin directory and slug |
| `--plugin-namespace` | | PHP namespace (e.g. AcmeCorpPlugin) |
| `--plugin-constant` | | Constant prefix (e.g. ACME_CORP_PLUGIN) |
| `--plugin-text-domain` | | Text domain |
| `--plugin-hook-prefix` | | Hook prefix (e.g. acme_corp_plugin) |
| `--plugin-human-name` | | Human-readable plugin name |
| `--plugin-npm-name` | | npm package name |
| `--theme-slug` | | Theme directory and slug |
| `--theme-namespace` | | PHP namespace (e.g. AcmeCorpTheme) |
| `--theme-constant` | | Constant prefix (e.g. ACME_CORP_THEME) |
| `--theme-text-domain` | | Text domain |
| `--theme-hook-prefix` | | Hook prefix (e.g. acme_corp_theme) |
| `--theme-human-name` | | Human-readable theme name |
| `--theme-npm-name` | | npm package name |
| `--author-name` | | Author name |
| `--author-email` | | Author email |
| `--author-uri` | | Author URI |
| `--description` | | Project description |
| `--composer-vendor` | | Composer vendor slug |
| `--homepage-url` | | Project homepage URL |
| `--repo-url` | | Repository URL |

### What the scaffold does

When you run the script, it performs the following steps in order:

1. Deletes the theme you did not choose (Block or Classic).
2. For VIP projects, moves the mu-plugin and its loader into `client-mu-plugins/`.
3. Cleans up PHPStan config and constants for the deleted theme.
4. Walks every file in the project and replaces all placeholder strings (namespaces, constants, slugs, text domains, package names, URLs, author metadata).
5. Renames the plugin and theme directories to match the new slugs.
6. Renames translation `.pot` files to match the new text domains.
7. Updates npm workspace paths and watch scripts in the root `package.json`.
8. Deletes `package-lock.json` and `composer.lock` so you can regenerate them cleanly.
9. Optionally removes the scaffold script itself (`--self-destruct`).

### After scaffolding

Once the scaffold completes, run the following to set up your project:

```bash
npm install
composer install
npm run build
```

You will also need to run `composer install` inside the mu-plugin and theme directories where `composer.json` files exist.

## Adding a New Plugin

After your project is scaffolded and underway, you may need to add additional plugins. The `scaffold:plugin` command downloads the reference 10up-plugin from GitHub and integrates it into your existing project.

### Interactive mode

```bash
npm run scaffold:plugin
```

The CLI walks you through:

1. **Plugin name** - A human-readable name like "Content Syndication". All other naming conventions are derived automatically.
2. **Metadata** - Author name, email, URI, description, and Composer vendor slug. The vendor slug is auto-detected from your existing `composer.json`.

The script automatically detects whether your project uses `mu-plugins/` or `client-mu-plugins/` (for VIP projects).

### Non-interactive mode

```bash
npm run scaffold:plugin -- \
  --name "Content Syndication" \
  --composer-vendor acme \
  --yes
```

### All available flags

| Flag | Short | Description |
|------|-------|-------------|
| `--name` | `-n` | Plugin name (e.g. "Content Syndication") |
| `--mu-dir` | | MU plugins directory (auto-detected if not provided) |
| `--ref` | | GitHub ref to download from (default: trunk) |
| `--yes` | `-y` | Skip confirmation prompt |
| `--plugin-slug` | | Plugin directory and slug |
| `--plugin-namespace` | | PHP namespace (e.g. ContentSyndicationPlugin) |
| `--plugin-constant` | | Constant prefix (e.g. CONTENT_SYNDICATION_PLUGIN) |
| `--plugin-text-domain` | | Text domain |
| `--plugin-hook-prefix` | | Hook prefix (e.g. content_syndication_plugin) |
| `--plugin-human-name` | | Human-readable name |
| `--plugin-npm-name` | | npm package name |
| `--author-name` | | Author name |
| `--author-email` | | Author email |
| `--author-uri` | | Author URI |
| `--description` | | Plugin description |
| `--composer-vendor` | | Composer vendor slug |

### What it does

When you run the command, it:

1. Auto-detects your project's mu-plugins directory (`mu-plugins/` or `client-mu-plugins/`)
2. Downloads the reference plugin from the 10up/wp-scaffold GitHub repository
3. Applies all string replacements (namespaces, constants, slugs, text domains, metadata)
4. Places the plugin and loader file in the correct directory
5. Updates project config files:
   - `package.json` - adds workspace, adds watch script
   - `phpstan.neon` - adds plugin paths
   - `phpstan/constants.php` - adds plugin constants
   - `phpcs.xml` - adds loader file exclusion
   - `.github/workflows/php.yml` - adds CI steps

### After adding a plugin

Run the following to integrate the new plugin:

```bash
npm install
composer install --working-dir=mu-plugins/your-plugin-slug
npm run build
```

## Database Setup
- Local WP will create a database for your site automatically.
- If you have a database dump (e.g., `local.sql`), import it using Local WP's database tools or via the command line.
- Update your site's settings in the WordPress admin as needed.

## Troubleshooting
If you encounter issues, see the [Troubleshooting Guide](./troubleshooting-faq/troubleshooting-guide.md) or check the logs in the `wp-content` directory if available.
