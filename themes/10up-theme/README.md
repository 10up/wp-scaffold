# 10up Theme

## Overview

This is a lightweight starter WordPress theme scaffold built with modern PHP, Composer, and NPM tooling. It provides a simple base for classic templates, block support, and reusable theme patterns while keeping the project easy to extend.

## Project Structure

- `assets/`: CSS, JavaScript, images, and frontend asset sources
- `blocks/`: custom block definitions and block-related code
- `partials/`: reusable theme templates and layout fragments
- `patterns/`: registered block patterns and pattern markup
- `src/`: PHP classes for theme setup, asset loading, and block registration
- `theme.json`: editor and frontend style settings
- `functions.php`: theme bootstrap and support registration
- `header.php`, `footer.php`, `search.php`: classic theme templates stored at the theme root

---

## 1. Theme overview

This theme is designed to work with WordPress modern theme tooling and 10up conventions. It includes:

- `theme.json` for editor defaults and frontend styling
- Composer-managed PHP dependencies and PSR-4 autoloading
- NPM tooling for asset building, linting, and local development
- Classic and block-friendly templates with reusable partials

Root files:
- `style.css` — WordPress theme header and base stylesheet
- `theme.json` — global settings, styles, color palettes, and editor options
- `functions.php` — theme initialization, asset registration, and support setup
- `header.php` — theme header template
- `footer.php` — theme footer template
- `search.php` — search results template
- `index.php` — base fallback template
- `composer.json` — PHP dependency management and autoload configuration
- `package.json` — JS tooling, build scripts, and package configuration

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
   npm run setup:local
   npm run setup
   ```
3. Build theme assets from root:
   ```bash
   npm run build
   ```
4. Activate the theme in WordPress admin: Appearance → Themes → "10up Theme"

### Theme-only install (rare instances)

1. Change into the theme directory:
   ```bash
   cd /path/to/wordpress/wp-content/themes/10up-theme
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
4. Activate the theme in WordPress admin: Appearance → Themes → "10up Theme"

---

## 4. Commands

- `composer install` — install PHP dependencies for the theme
- `npm install` — install JavaScript dependencies
- `npm run build` — compile theme assets
- `npm run watch` — start watch mode for frontend assets
- `npm run clean-dist` — remove generated build assets
- `npm run lint` — run JS linting
- `npm run lint-style` — run style/CSS linting
- `npm run test` — run unit tests
- `npm run format-js` — format JavaScript source files
- `composer lint-fix` — auto-fix PHP lint issues using `phpcbf`

---

## 5. Working with the theme

- Update editor and frontend styles in `theme.json`
- Add frontend styles in `assets/css/` and scripts in `assets/js/`
- Keep reusable template fragments in `partials/`
- Define patterns in `patterns/` and register them in theme setup
- Use `src/` for theme-specific PHP classes and bootstrap logic
- Rebuild assets after changing block or asset source files before testing in WordPress

---

## 6. Notes for new engineers

- This theme is intended to be minimal, easy to extend, and compatible with 10up tooling.
- If you add or change assets, run `npm run build` before verifying in WordPress.
- Keep theme-specific PHP logic in `src/` rather than `functions.php` when possible.
- Prefer `theme.json` configuration before adding custom block CSS or editor styles.

---

## 7. References

- Theme JSON Handbook: https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-json/
- Gutenberg Training: https://gutenberg.10up.com/training/
