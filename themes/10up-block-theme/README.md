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

1. Clone the repository:
   ```bash
   git clone <repo-url> 10up-block-theme
   cd 10up-block-theme
   ```
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```
3. Build assets:
   ```bash
   npm run build
   ```
4. Activate theme in WordPress admin: Appearance → Themes → "10up Block Theme"

---

## 4. Developer workflow

### 4.1 Quick start (repeat for copy/paste)

```bash
cd /path/to/themes/10up-block-theme
composer install
npm install
npm run build
```

### 4.2 Local development

- `npm run watch` (or `npm start`) → 10up-toolkit watch with HMR/hot-refresh
- `composer exec phpcs -- --standard=WordPress` → PHP lint
- `npm run lint` → JS lint
- `npm run lint-style` → CSS lint
- `npm run test` → unit tests
- `npm run clean-dist` → remove generated assets

### 4.3 Common gotchas

- Always build `dist/` after changing source assets (`assets/`, `blocks/`, `src/`).
- If using a check-in strategy for `dist/`, ensure CI runs `npm run build` before release.
- `theme.json` is the source of truth for editor styles, palette, and layout. Adjust there for global styles.
- Some references may still use legacy naming in older helper docs; prefer 10up-block-theme naming in code changes.

---

## 5. Working with the theme

- Update editor and global styles in `theme.json`.
- Add frontend styles in `assets/css/` and scripts in `assets/js/`.
- Define theme style variations in `styles/` and activate them from the Site Editor styles panel.
- Use `npm run scaffold:block` to create new blocks, then update the generated files under `blocks/<block-name>/`.
- Register template parts in `parts/`, and templates in `templates/`.

### Style variations

The theme supports block theme style variations via JSON files in `styles/`.
Create a file like `styles/my-style.json` with a `title`, `slug`, and `styles` section.
Once added, open the Site Editor and select the style variation from the Styles panel.

## 6. Adding a block

1. Create a new folder under `blocks/<block-name>/`.
2. Add `block.json`, `index.ts`, `edit.tsx`, and `style.css`.
3. Run `npm run build`.
4. The theme auto-registers blocks from `dist/blocks/*`.

## 8. Useful commands

- `composer install`
- `npm install`
- `npm run build`
- `npm run watch`
- `composer exec phpcs -- --standard=WordPress`
- `npm run lint`
- `npm run lint-style`
- `npm run test`
- `npm run clean-dist`
- `npm run wp-compat` — scan installed WordPress package versions for compatibility with the declared WordPress requirement
- `npm run wp-compat:fix` — install compatible `@wordpress/*` package versions if any are too new
- `npm run wp-compat:info` — inspect installed `@wordpress/*` package compatibility metadata

## 9. Notes for new engineers

- This theme is built to keep PHP minimal and put layout, styles, and block logic into the theme files.
- Use `theme.json` for most editor and global style settings.
- If you add or change blocks, always rebuild with `npm run build` before testing in WordPress.
- Keep one README here at the theme root; nested feature READMEs were removed to reduce confusion.

---

## 10. Maintenance tasks

- PHP lint: `composer exec phpcs -- --standard=WordPress`
- JS lint: `npm run lint`
- Style lint: `npm run lint-style`
- Format JS: `npm run format-js`
- Clean dist: `npm run clean-dist`

