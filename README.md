# 10up WP Scaffold

This scaffold is the starting point for all 10up WordPress projects.

It contains a bare bones theme and must use plugin for you to base your development off of. Asset bundling, linting, formatting, type-checking, and testing are handled by [Vite+](https://viteplus.dev) (`vp`) together with [`@10up/wp-vite-plugins`](https://github.com/10up/wp-vite-plugins).

## Requirements

- Node >= 24
- NPM >= 10

## How to Use

### Quick Start (Recommended)

The fastest way to get started is to run the scaffold CLI. It walks you through a few questions, then renames every placeholder string, directory, config reference, and translation file in the project so it matches your new project name.

1. Clone or download the scaffold into your `wp-content` directory.
2. Install dependencies:

```bash
npm install
```

3. Run the scaffold:

```bash
npm run scaffold
```

The CLI will ask you to choose a hosting platform (Standard or VIP), a theme type (Block or Classic), and a project name. It then derives all the namespaces, constants, slugs, text domains, and other naming conventions from the project name automatically. You can accept the defaults or customize each value individually.

When it finishes, the unused theme is deleted, all placeholder strings are replaced, directories are renamed, and lock files are cleaned up so you can start fresh.

#### Non-interactive mode

If you already know what you want, you can pass everything as flags and skip the prompts entirely:

```bash
npm run scaffold -- \
  --project-name "Acme Corp" \
  --theme block \
  --hosting standard \
  --author-name "Acme Inc" \
  --description "The Acme Corp website" \
  --yes
```

Run `npm run scaffold -- --help` to see all available options, including individual overrides for plugin/theme slugs, namespaces, constants, and metadata.

#### Self-destruct

By default the script will ask whether you want to remove it after scaffolding is complete. You can also pass the `--self-destruct` flag to do this automatically. When enabled, the script removes the `bin/scaffold.mjs` file, the `scaffold` npm script, and the `@inquirer/prompts` dependency from `package.json`.

### Adding New Plugins

After your project is scaffolded, you can add additional plugins using the `scaffold:plugin` command. It downloads the reference 10up-plugin from GitHub and integrates it into your project:

```bash
npm run scaffold:plugin
```

The command auto-detects your project structure (standard or VIP), prompts for the plugin name and metadata, applies all replacements, and updates your project config files (`package.json`, `phpstan.neon`, `phpcs.xml`, CI workflows). See the [full documentation](docs/installation.md#adding-a-new-plugin) for all available options and flags.

### Manual Setup

You can also set up the scaffold manually without the CLI:

1. [Download a zip](https://github.com/10up/wp-scaffold/archive/trunk.zip) of the repository into your project. At 10up, by default we version control the `wp-content` directory (ignoring obvious things like `uploads`). This enables us to have plugins, theme, etc. all in one repository. Having separate repositories for each plugin and theme only happens in rare circumstances that are outside of our control.
2. Take what you need. If your project doesn't have a theme, remove the theme. If your project doesn't need any plugin functionality, remove the MU plugin. If your plugin doesn't need CSS/JS, remove it. If your plugin doesn't need to be translated, remove all the translation functionality.
3. Compiling, minifying, bundling, etc. of JavaScript and CSS is all done by [Vite+](https://viteplus.dev) with [`@10up/wp-vite-plugins`](https://github.com/10up/wp-vite-plugins). Install Vite+ once with `curl -fsSL https://vite.plus | bash`. If you want to develop on the theme (and vice-versa the plugin), you would navigate to the theme directory in your console and run `npm run start` (after running `npm install` at the root first of course). Entries are configured in each workspace's `vite.config.ts` — block entries are discovered automatically from `block.json` files.
4. The build runs in two passes: a script pass (`vp build`) and a Script Module pass (`vp build --mode modules`) for blocks that declare `scriptModule` / `viewScriptModule`. The module pass is safe to run unconditionally — while no block needs it, `@10up/wp-vite-plugins` turns it into a no-op build.
5. [npm workspaces](https://docs.npmjs.com/cli/v7/using-npm/workspaces) is used to manage npm dependencies. The main benefit of using npm workspaces is that we can hoist all dependencies to the root folder and avoid installing duplicate dependencies, saving time and space. By default the `workspaces` config are setup so that `mu-plugins/10up-plugin` and all themes are treated as "packages", if you are building a new plugin/theme make sure to update `workspaces` in `package.json` See the example below:

```json
  "workspaces": [
		"themes/*",
		"mu-plugins/10up-plugin",
		"mu-plugins/my-other-awesome-10up-plugin",
  ],
```

6. To build plugins/themes simply run `npm install` at the root and `npm run [build|start|watch]` and npm will automatically build all themes and plugins. If a WordPress critical error is received run `composer install` in all locations that have an existing `composer.lock` file; example locations: `root`, `/mu-plugins/10up-plugin`, `/themes/10up-theme`. Upon build completion set the `10up-theme` as active within WordPress admin by running `wp theme activate 10up-theme`.
7. Cross-workspace tasks run through Vite+'s task runner (`vp run`), which executes packages in parallel and caches results. The root scripts fan out with `vp run -r <task>`; target a single package with `--filter`, e.g.:

```json
	"watch:theme": "vp run --filter tenup-theme watch",
	"watch:plugin": "vp run --filter tenup-plugin watch",
	"watch": "vp run -r watch",
```

8. To add npm dependencies to your theme and/or plugins add the `-w=package-name` flag to the `npm install` command. E.g: `npm install --save prop-types -w=tenup-plugin` **DO NOT RUN** `npm install` inside an individual workspace/package. Always run the from the root folder.
9. If you're building Gutenberg blocks and importing `@wordpress/*` packages, **you do need** to install them in the workspace that imports them. `@10up/wp-vite-plugins` reads each package's own metadata (`wpScript` / `wpScriptModuleExports`) to decide whether it is externalized against WordPress core or bundled — and you get real TypeScript types out of it.

## Scaffold Rules

Much of the functionality in the scaffold is intended to be optional depending on the needs of your project e.g. i18n functionality. However, there are a few important principles that you must follow:

1. [Vite+](https://viteplus.dev) with [`@10up/wp-vite-plugins`](https://github.com/10up/wp-vite-plugins) must be used for asset bundling. Over the years we've found differences in how assets are built across projects to be very confusing for engineers. As such, we are standardizing on one toolchain: `vp build` (Vite/Rolldown), `vp check` (Oxfmt, Oxlint, tsgo), `vp test` (Vitest), and `vp run` (cached tasks).
2. Functionality should be built into the 10up must-use functionality as much as possible. Presentation should be kept in the theme. Separating these two makes long term development, maintenance, and extensibility much easier.
3. Blocks should be built into the theme and follow the [example block](https://github.com/10up/wp-scaffold/tree/trunk/themes/10up-theme/includes/blocks/example-block) provided.
4. When creating new themes or plugins make sure to follow the `scripts` convention:

```json
  "scripts": {
    "start": "npm run watch",
    "watch": "vp build --watch",
    "build": "vp build && vp build --mode modules",
    "check": "vp check && tsgo --noEmit",
    "typecheck": "tsgo --noEmit",
    "format-js": "vp fmt --write",
    "lint-js": "vp lint",
    "lint-style": "stylelint \"assets/css/**/*.css\"",
    "test": "vp test run --passWithNoTests",
    "clean-dist": "rm -rf ./dist"
  },
```

## Husky and `vp staged`

Husky runs [`vp staged`](https://viteplus.dev/guide/commit-hooks) on the pre-commit hook (replacing lint-staged). The configuration lives in the `staged` block of the root `vite.config.ts`.
By default it will run the following:

- `vp check --fix` (Oxfmt + Oxlint) on JS/TS files.
- `stylelint --fix` on CSS files.
- `phpcs` on PHP files.
