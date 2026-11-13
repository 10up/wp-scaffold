# Frontend Build Process

This document outlines the frontend build process used in a WP Scaffold project, including the tools, configuration, and workflows for compiling and optimizing frontend assets.

## Table of Contents
- [Overview](#overview)
- [Prerequisites](#prerequisites)
- [Build Tools](#build-tools)
- [Project Structure](#project-structure)
- [NPM Scripts](#npm-scripts)
- [Vite Configuration](#vite-configuration)
- [Dev Server](#dev-server)
- [Asset Optimization](#asset-optimization)
- [SVG Icons](#svg-icons)
- [Development Workflow](#development-workflow)
- [Production Builds](#production-builds)
- [Troubleshooting](#troubleshooting)

## Overview

The WP Scaffold project uses a modern frontend build process to compile, optimize, and bundle assets such as JavaScript, CSS, and images. This process ensures that the frontend code is efficient, maintainable, and follows best practices.

## Prerequisites

Before working with the frontend build process, ensure you have:

- [Node.js](https://nodejs.org/) `^22.18.0 || ^24.11.0 || >=26` (`.nvmrc` pins 24)
- [npm](https://www.npmjs.com/) (usually comes with Node.js)
- Basic understanding of JavaScript, CSS, and build tools
- Local development environment set up (see [Local Development](../testing-quality/local-development.md))

## Build Tools

The WP Scaffold project uses the following build tools:

- **[Vite+](https://viteplus.dev)** (`vp`): Vite 8 with the Rolldown bundler, plus the task runner, linter, formatter and test runner in one toolchain
- **[`@10up/wp-vite-plugins`](https://github.com/10up/wp-tooling/tree/trunk/packages/wp-vite-plugins)**: the WordPress parts — `block.json` discovery, `@wordpress/*` externals with `.asset.php` files, per-block stylesheets, static asset copying and the dev server bridge
- **[Lightning CSS](https://lightningcss.dev)**: CSS compilation through `wp()`'s `css` option, with the shared globals and mixins
- **Oxlint and Oxfmt**: JavaScript and TypeScript linting (with [`@10up/oxlint-config`](https://github.com/10up/wp-tooling/tree/trunk/packages/oxlint-config)), and JavaScript, TypeScript and CSS formatting (`vp check`)
- **tsgo**: TypeScript type checking
- **Stylelint**: CSS linting with [`@10up/stylelint-config`](https://github.com/10up/wp-tooling/tree/trunk/packages/stylelint-config), run once from the root

## Project Structure

The frontend assets in the WP Scaffold themes are organized in the following structure:

```
wp-content/themes/[theme-name]/
├── assets/
│   ├── js/
│   │   ├── frontend/
│   │   │   ├── components/
│   │   │   └── frontend.js
│   │   └── block-editor/
│   ├── css/
│   │   ├── frontend/
│   │   ├── blocks/
│   │   ├── globals/
│   │   └── mixins/
│   ├── images/
│   ├── fonts/
│   └── svg/
├── dist/ (compiled assets)
│   ├── css/
│   ├── js/
│   └── images/
├── blocks/ (Gutenberg blocks)
├── src/ (PHP files)
├── package.json
└── vite.config.ts
```

## NPM Scripts

Each theme and plugin defines the same scripts in its `package.json`:

```json
{
  "scripts": {
    "start": "npm run watch",
    "build": "vp build",
    "watch": "vp build --watch",
    "dev": "vp dev",
    "check": "vp check && tsgo --noEmit",
    "lint-js": "vp lint",
    "format-js": "vp fmt",
    "test": "vp test run --passWithNoTests"
  }
}
```

The root `package.json` runs each task in every workspace with `vp run -r`, and caches the results. CSS linting is a root-only script: `npm run lint-style` runs Stylelint with the root `stylelint.config.js` over `themes/*/assets/css` and `mu-plugins/*/assets/css`.

### Common Commands

- `npm run build`: Create a production build of every theme and plugin
- `npm start` / `npm run watch`: Rebuild on file changes
- `npm run dev:theme` (or `dev:block-theme`, `dev:plugin`): Start a Vite dev server with hot reloading for one workspace
- `npm run check`: Check formatting, lint and types
- `npm run lint-style`: Lint CSS (root only)
- `npm test`: Run tests with Vitest

## Vite Configuration

Each workspace configures its build in `vite.config.ts` with the `wp()` plugin from `@10up/wp-vite-plugins`:

```ts
wp({
	blocksDir: './blocks',                  // block.json discovery
	blocksStylesDir: './assets/css/blocks', // per-core-block styles in dist/autoenqueue/
	copyAssetsDir: './assets',              // images, fonts and SVG copied to dist/
	// Custom media, custom selectors and mixins every stylesheet can use.
	css: { globals: ['assets/css/globals/*.css', 'assets/css/mixins/*.css'] },
}),
```

Theme-level entries live in `build.rollupOptions.input`. Name a JavaScript entry `js/<name>` and a stylesheet entry `css/<name>`. Give a stylesheet a different name from any script, for example `css/frontend-style` next to `js/frontend`. `GetAssetInfo::get_asset_info()` looks up `js/<name>` before `css/<name>`, so a shared name would give the stylesheet the script's version.

`npm run build` runs `vp build`, which builds everything into one `dist/` in two Vite build environments:

1. `client` builds classic scripts, all stylesheets, the `block.json` and `render.php` copies, `dist/blocks-manifest.php` and the static assets.
2. `modules` builds Script Modules: `scriptModule` and `viewScriptModule` entries from `block.json`. It is skipped while no block declares one.

`dist/` is emptied once, before either environment builds. See [Classic scripts and Script Modules](https://github.com/10up/wp-tooling/blob/trunk/packages/wp-vite-plugins/docs/wp.md#classic-scripts-and-script-modules).

Every script and stylesheet gets an `.asset.php` file with its dependencies and a content-hash version. `@wordpress/*` imports become WordPress script dependencies, or Script Module imports, based on each installed package's own metadata. Install every `@wordpress/*` package you import in the workspace that imports it.

The themes register their blocks from `dist/blocks-manifest.php` with `wp_register_block_types_from_metadata_collection()` (WordPress 6.7+).

## Dev Server

`npm run dev:theme` starts a Vite dev server for the theme. Every build writes `dist/dev-server.php`, and `functions.php` loads it. While the dev server runs, that file points WordPress's script and style URLs at the dev server, so changes show up without a rebuild. When the dev server stops, WordPress loads the built files again. The file does nothing in production.

If WordPress runs on a different host than the dev server, for example in Docker or on a `.test` domain, set `server.origin` and `server.cors` in the workspace's `vite.config.ts`. See the [`wpDevServer` documentation](https://github.com/10up/wp-tooling/blob/trunk/packages/wp-vite-plugins/docs/wp-dev-server.md).

## Asset Optimization

### JavaScript Optimization

JavaScript files are optimized through:

- **Transpilation**: Rolldown and Oxc compile TypeScript and JSX for the browserslist targets
- **Bundling**: Each classic script is a self-contained file; Script Modules share chunks through WordPress's import map
- **Minification**: Production builds are minified
- **Tree Shaking**: Unused code is removed from the final bundle

### CSS Optimization

CSS files are optimized through:

- **Lightning CSS**: `wp()` compiles every stylesheet with Lightning CSS. Files in `assets/css/globals/` and `assets/css/mixins/` reach every stylesheet without an `@import`, and local `@import`s are inlined once. See [`wpCss`](https://github.com/10up/wp-tooling/blob/trunk/packages/wp-vite-plugins/docs/wp-css.md)
- **Syntax lowering**: Nesting and newer syntax are lowered for the browserslist targets
- **Minification**: Production builds are minified with Lightning CSS

### Images and Fonts

Images, fonts and SVG files in `assets/` are copied to `dist/` unchanged, at the same relative path.

## SVG Icons

SVG files in a theme's `assets/svg/` folder are built to `dist/svg/` and registered as icons by the theme's `Assets` class. Since WordPress 7.0 core sanitizes every registered icon in `WP_Icons_Registry::sanitize_icon_content()` with a fixed `wp_kses` allowlist that has no filter:

| Element | Allowed attributes |
| --- | --- |
| `svg` | `class`, `xmlns`, `width`, `height`, `viewBox`, `aria-hidden`, `role`, `focusable` |
| `path` | `fill`, `fill-rule`, `d`, `transform` |
| `polygon` | `fill`, `fill-rule`, `points`, `transform`, `focusable` |

Everything else is removed without a warning: elements such as `g`, `line`, `polyline`, `circle`, `rect`, `ellipse`, `defs`, `clipPath`, `use`, `title` and `style`, and attributes such as `stroke`, `stroke-width`, `fill-opacity`, `opacity`, `clip-rule`, `id` and `style`. The icon still registers, but it renders empty or wrong. A typical stroke-based icon set (Feather, Lucide) comes back as an empty `<svg>`.

The check skips losses that change nothing visible: `title`, `desc`, `metadata` and `defs` elements, the `clip-rule`, `id`, `version`, `xmlns:xlink` and `xml:space` attributes, and a `fill="currentColor"` on the root `<svg>`. Core's own icons ship with that root fill, and core's Icon block puts it back with CSS.

> **This is a lint, not a security control.** It catches icons that would render wrong after core's sanitizer, and nothing more. It reads files committed to the theme, which are trusted code like any PHP file. It must never be used to vet untrusted SVG, such as uploads. Core's `wp_kses` pass on registration is what removes dangerous markup. Anything that accepts SVG from users needs a real sanitizer that rebuilds the markup rather than filtering it.

Run the check with:

```bash
npm run lint-svg                                 # every themes/*/assets/svg and mu-plugins/*/assets/svg
npm run lint-svg -- themes/my-theme/assets/svg   # a folder, file or glob
```

It also runs on staged icons before each commit and in CI. XML declarations, doctypes and comments are fine; kses strips them harmlessly.

To fix an icon it flags:

- **Strokes:** outline the strokes in your design tool before exporting (Figma: *Outline stroke*), so the icon is drawn with filled paths. Use `fill="currentColor"` on the paths if the icon should follow the text color.
- **Shapes** (`line`, `polyline`, `circle`, `rect`, `ellipse`): convert them to paths, for example with SVGO's `convertShapeToPath`.
- **Groups:** flatten `<g>` wrappers and move any `fill` or `transform` they carry onto the paths.

Core is widening the allowlist (see [WordPress/wordpress-develop#12197](https://github.com/WordPress/wordpress-develop/pull/12197) and [#13539](https://github.com/WordPress/wordpress-develop/pull/13539)). When a release ships with a wider list, update the `ALLOWED` constant in `bin/lint-svg.mjs` to match.

## Development Workflow

### Starting Development

1. Navigate to your theme directory:
   ```bash
   cd wp-content/themes/your-theme
   ```

2. Install dependencies (if not already installed):
   ```bash
   npm install
   ```

3. Start the development build process:
   ```bash
   npm start
   ```

This rebuilds assets when files change. Use `npm run dev` instead for the Vite dev server with hot reloading.

### Working with CSS

When working with CSS:

1. Create or edit files in the `assets/css/` directory
2. Follow the project's CSS guidelines (see [CSS Guidelines](./css-guidelines.md))
3. Import your CSS files in the main entry point (`assets/css/frontend.css`)

### Working with JavaScript

When working with JavaScript:

1. Create or edit files in the `assets/js/src/` directory
2. Follow the project's JavaScript guidelines (see [JavaScript Guidelines](./javascript-guidelines.md))
3. Import your JavaScript modules in the main entry point (`assets/js/src/main.js`)

## Production Builds

When preparing for production:

1. Check formatting, lint and types:
   ```bash
   npm run check
   npm run lint-style
   ```

2. Run tests to ensure functionality:
   ```bash
   npm test
   ```

3. Create a production build:
   ```bash
   npm run build
   ```

This will generate optimized, production-ready assets in the `dist` directories.

## Troubleshooting

### Common Issues

- **Node.js version conflicts**: Ensure you're using the correct Node.js version (check `.nvmrc` if available)
- **Missing dependencies**: Run `npm install` to ensure all dependencies are installed
- **Build errors**: Check the console output for specific error messages
- **Asset not updating**: Ensure the file is properly imported and the build process is running

### Debugging Tips

- Check the browser console for JavaScript errors
- Use source maps to debug JavaScript and CSS issues
- Clear your browser cache if you're not seeing changes
- Try stopping and restarting the build process
