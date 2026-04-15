# 10up Block Theme

## Overview
This is a lightweight starter theme for WordPress block themes. It provides the minimal structure to get a modern full-site editing theme up and running, while also being easy to extend.

## Project Structure
- `assets/`: theme asset source files (CSS, JS, fonts, images)
- `blocks/`: custom blocks
- `parts/`: reusable theme parts for header, footer, etc.
- `patterns/`: block pattern PHP registration / markup
- `styles/`: block theme style variations in JSON format
- `src/`: PHP classes for theme setup, block registration, asset loading
- `templates/`: full-site editing template files

---

## 1. Theme overview

This is a full-site block theme built using:
- WordPress block theme system (`theme.json`, template files, template parts)
- 10up toolkit (`10up-toolkit`) for asset bundling, linting, testing
- 10up framework (`10up/wp-framework`) for modular PHP class loading
- Modern CSS and JS modules in `assets/`
- Custom blocks under `blocks/`

Main root files:
- `style.css` - WordPress theme header and bare stylesheet.
- `theme.json` - block theme settings, styles, color/spacing scales, templates.
- `functions.php` - Bootstrap: constants, composer loading, fast-refresh and theme setup.
- `composer.json` - PHP dependencies and PSR-4 autoload.
- `package.json` - JS/asset build and 10up-toolkit configuration.

---

## 2. Requirements

- PHP >= 8.4
- Node >= 24
- NPM >= 10
- Composer 2
- WordPress >= 6.9

---

## 3. First-time setup

### Preferred: full repository install

1. Clone the repo and open the root:
   ```bash
   git clone git@github.com:10up/wp-scaffold.git
   cd wp-scaffold
   ```
2. Install dependencies from the repo root:
   ```bash
   composer install
   npm install
   ```
3. Build the theme assets from root:
   ```bash
   npm run build
   ```
4. Activate the theme in WordPress admin: Appearance → Themes → "10up Block Theme"

### Theme-only install

If you only have `themes/10up-block-theme` installed, use the theme folder directly:

1. Change into the theme directory:
   ```bash
   cd /path/to/wordpress/wp-content/themes/10up-block-theme
   ```
2. Install theme-specific dependencies:
   ```bash
   composer install
   npm install
   ```
3. Build assets:
   ```bash
   npm run build
   ```
4. Activate the theme in WordPress admin: Appearance → Themes → "10up Block Theme"

---

## 4. Commands

- `composer install` — install PHP dependencies for the theme.
- `npm install` — install JavaScript dependencies.
- `npm run build` — compile theme assets into `dist/`.
- `npm run watch` — start 10up-toolkit in watch mode with HMR/hot-refresh.
- `npm run scaffold:block` — generate a new block scaffold in `blocks/` using the local template.
- `composer lint` — run PHP, JS, and CSS linting across the theme.
- `npm run lint` — run JS linting only.
- `npm run lint-style` — run CSS/style linting only.
- `npm run test` — run unit tests.
- `npm run clean-dist` — remove generated `dist/` assets.
- `npm run wp-compat` — analyze installed `@wordpress/*` package versions against the theme's minimum WordPress requirement.
- `npm run wp-compat:fix` — install compatible `@wordpress/*` package versions when they are too new.
- `npm run wp-compat:info` — show compatibility metadata for installed `@wordpress/*` packages.
- `npm run format-js` — format JavaScript sources.
- `composer lint-fix` — auto-fix PHP lint issues using `phpcbf`.

---

## 6. Recommended WordPress packages

This theme does not install every `@wordpress/*` package by default. `10up-toolkit` only bundles the packages you actually import in source, so keep your dependencies curated to what your blocks and editor extensions use.

When you need WordPress block packages, this is a good starting list:

- `@wordpress/blocks`
- `@wordpress/block-editor`
- `@wordpress/components`
- `@wordpress/data`
- `@wordpress/dom-ready`
- `@wordpress/i18n`
- `@wordpress/hooks`
- `@wordpress/api-fetch`
- `@wordpress/server-side-render`

Install only the ones you need for your block or editor code:

```bash
npm install --save-dev @wordpress/blocks @wordpress/block-editor @wordpress/components @wordpress/data @wordpress/i18n
```

If you want the full common set, use this list:

```bash
npm install --save-dev \
  @wordpress/api-fetch \
  @wordpress/block-editor \
  @wordpress/blocks \
  @wordpress/components \
  @wordpress/compose \
  @wordpress/core-data \
  @wordpress/data \
  @wordpress/dom-ready \
  @wordpress/hooks \
  @wordpress/i18n \
  @wordpress/interactivity \
  @wordpress/plugins \
  @wordpress/server-side-render
```

These packages are useful to keep available when developing blocks, but they do not need to be bundled into `package.json` unless your theme consumes them directly.

---

## 7. Working with the theme

- Update editor and global styles in `theme.json`.
- Add frontend styles in `assets/css/` and scripts in `assets/js/`.
- Block behavior logic lives in `assets/js/`, including `block-extensions.ts`, `block-filters/index.ts`, `block-variations/index.ts`, and `block-variations/unregister-variations.ts`.
- Define theme style variations in `styles/` and activate them from the Site Editor styles panel.
- Use `npm run scaffold:block` to create new blocks, then update the generated files under `blocks/<block-name>/`.
- Register template parts in `parts/`, and templates in `templates/`.

### Style variations
- https://ignitewp.10uplabs.com/block-styling-and-css/

---

## 6. Extending with IgniteWP

- https://ignitewp.10uplabs.com/
- npx @10up/ignite-cli install
- npx @10up/ignite-cli install core accordion carousel
- Activate the plugins in WordPress and configure them to match your project requirements. Most plugins work out of the box with sensible defaults.
- https://ignitewp.10uplabs.com/installing-ignite-plugins/

---

## 7. Notes for new engineers

- This theme is built to be minimal and extendable.
- If you add or change blocks, always rebuild with `npm run build` before testing in WordPress.
- Please go through the Gutenberg Training if you have questions on using: https://gutenberg.10up.com/training/

