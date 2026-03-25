# 10up Block Theme

## Overview
This is a lightweight starter theme for WordPress block themes. It provides the minimal structure to get a modern full-site editing theme up and running, while also being easy to extend.

## Goals
- Fast setup for new WordPress engineers
- Clear convention-based project structure
- Build on WordPress block theme best practices
- Keep PHP logic minimal and isolated in `src/`

## Project Structure
- `assets/`: theme asset source files (CSS, JS, fonts, images)
- `blocks/`: custom blocks
- `parts/`: reusable theme parts for header, footer, etc.
- `patterns/`: block pattern PHP registration / markup
- `src/`: PHP classes for theme setup, block registration, asset loading
- `templates/`: full-site editing template files

Comprehensive developer guide:
- Architecture
- Build tools
- WordPress connections
- Blocks
- Templates
- Runtime behavior

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

- PHP >= 8.2
- Node >= 18
- Composer
- WordPress 6.8+ (implied by theme.json schema)

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

## 5. Core bootstrapping path

### 5.1 `functions.php`

- defines constants:
  - `TENUP_BLOCK_THEME_PATH`, `TENUP_BLOCK_THEME_DIST_PATH`, `TENUP_BLOCK_THEME_INC`, etc.
- points to composer/autoload (`vendor/autoload.php`)
- local fast-refresh via `dist/fast-refresh.php` when local environment
- includes helper files:
  - `includes/helpers.php`
  - `template-tags.php`
- creates and sets up `\TenupBlockTheme\ThemeCore`.

### 5.2 `src/ThemeCore.php`

- hooks:
  - `init` → `ThemeCore::init()`
  - `after_setup_theme` → `i18n()` + `theme_setup()`
  - `wp_head` → `js_detection()` + `scrollbar_detection()`
- `init()` ensures `TenupFramework\ModuleInitialization` exists, admin notice on failure
- `ModuleInitialization::instance()->init_classes( TENUP_BLOCK_THEME_INC )` automatically loads modules from `src/`.

---

## 6. Module system (10up framework)

By `ThemeCore::init()`, `\TenupFramework\ModuleInitialization` scans `src/` and initializes modules that implement `ModuleInterface`.

Current modules:
- `src/Blocks.php` (block registration and enqueueing)
- `src/Overrides.php` (WP output filters such as post date text for custom post types)
- `src/TemplateTags.php` (meta tag injection on `wp_head`)

Each module uses `TenupFramework\Module` trait to register hooks easily.

---

## 6. Block registration & runtime

### 6.1 `src/Blocks.php`

- `register_theme_blocks()` scans `dist/blocks/*/block.json` and calls `register_block_type_from_metadata()` for each found block.
- `allowed_block_types_all` filter extends allowed blocks with theme blocks.
- `enqueue_theme_block_styles()` scans `dist/blocks/autoenqueue/**/*.css` and registers/enqueues style/script for each block with assets info from `GetAssetInfo`.
- includes block render filter for TenUp navigation block (removes ARIA roles for fallback markup compatibility).

### 6.2 Blocks directory structure

`blocks/` contains block sources with standard WP block scaffold:
- `block.json`
- `edit.tsx`, `index.ts`, `markup.php`
- `style.css` / `editor.css` (for block styles)

`blocks/*` are transpiled and bundled by 10up-toolkit into `dist/blocks/*` (JS/CSS/asset metadata files).

---

## 7. Asset pipeline (10up-toolkit)

`package.json` includes a `10up-toolkit` config section:
- `useBlockAssets`: true (auto-handle block assets)
- `useScriptModules`: true
- `loadBlockSpecificStyles`: true
- `entry` maps compiled assets:
  - `editor-style-overrides`: `assets/css/editor-style-overrides.css`
  - `frontend`: `assets/js/frontend.ts`
  - `block-extensions`: `assets/js/block-editor/index.ts`
- `paths.blocksDir`: `./blocks`

`npm run build` calls `10up-toolkit build` and outputs to `dist/*`.

`npm run watch` does incremental build with file watching.

### 7.1 Dist layout

- `dist/css/` (compiled styles)
- `dist/js/` (compiled scripts)
- `dist/blocks/` (compiled block assets per block)
- `dist/fast-refresh.php` (local live-reload helper)

### 7.2 Theme script inclusion

- `src/ThemeCore::theme_setup()` calls `add_theme_support('editor-styles')` and `add_editor_style('/dist/css/frontend.css')`.
- Additional frontend script injection is likely handled in block metadata and `10up-toolkit` output, not explicit in theme code.

---

## 8. Styles + theme.json

### `theme.json`

Defines:
- `templateParts`: `header`, `footer`
- `customTemplates` for special post types
- `settings` with custom properties (`--wp--custom--*`, width computations, spacing, color palettes, gradients, transition variables)
- `layout` with content and wide max widths.

### `assets/css/` structure

- `editor-style-overrides.css` for editor-specific style overrides
- `frontend.css` for visual styles
- subdirectories for base/blocks/components/globals/mixins/patterns/templates/utilities

### `parts/` partial PHP templates
- `parts/header.html`, `parts/footer.html`, etc.

### `patterns/` reusable partial definitions
- e.g., `base-card.php`, etc.

---

## 9. Template and content hierarchy

`templates/` uses static HTML templates for site result paths:
- `index.html`, `home.html`, `single.html`, etc.

These are block theme templates in Full Site Editing model.

---

## 10. WordPress hook behavior at runtime

- `wp_head`:
  - `ThemeCore::js_detection()` toggles `no-js` to `js`
  - `ThemeCore::scrollbar_detection()` sets `--wp--custom--scrollbar-width`
  - `TemplateTags::add_viewport_meta_tag()` outputs viewport meta

- `after_setup_theme`: editor styles + disable core block patterns.

- `init`: loads module classes and runs block registration.

---

## 11. Debug and local enhancement

- local environment scenario (`.local`/`.test` or `local`/`development`) loads `dist/fast-refresh.php` and sets dist URL path with `TenUpToolkit\set_dist_url_path()` for hot-reload.

---

## 12. Extending the theme

1. Add block in `blocks/<block-name>/`.
2. Create `block.json`, `edit.tsx`, `index.ts`, style files as needed.
3. Run `npm run build`.
4. Block registers automatically from `dist/blocks/*` in `Blocks::register_theme_blocks()`.

---

## 13. Maintenance tasks

- PHP lint: `composer exec phpcs -- --standard=WordPress`
- JS lint: `npm run lint` / `npm run lint-js`
- Style lint: `npm run lint-style`
- Format JS: `npm run format-js`
- Clean dist: `npm run clean-dist`

---

## 14. Key entrypoints quick map

- `functions.php` → `ThemeCore` → class loader
- `src/Blocks.php` → block metadata and enqueueing
- `assets/js/frontend.ts` → frontend script implementation
- `blocks/*` + `10up-toolkit` → `dist/blocks/*`
- `theme.json` → WordPress UI/Editor settings

---

## 15. Helpful commands summary

- `composer install`
- `npm install`
- `npm run watch`
- `npm run build`
- `composer exec phpcs -- --standard=WordPress`
- `npm test`
- `npm run lint`

---

## 16. Further notes

- This theme is designed around a 10up block theme pattern (colors, spacing, utilities, templates).
- The 10up Toolkit plus the WP framework yields a highly modular, maintainable code path and is production-ready.
