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
- `blocks/`: block template parts and custom block asset complements
- `parts/`: reusable theme parts for header, footer, etc.
- `patterns/`: block pattern PHP registration / markup
- `src/`: PHP classes for theme setup, block registration, asset loading
- `styles/`: theme styles tokens / color scales
- `templates/`: full-site editing template files

## Getting Started
1. Clone repository into WordPress `wp-content/themes/`
2. Activate theme in admin under Appearance -> Themes
3. Visit Site Editor (Appearance -> Editor) to customize templates and styles

## Development
- PHP coding standard: WordPress (use `composer exec phpcs -- --standard=WordPress`)
- JS lint: `npm run lint`
- Keep theme JSON in `theme.json` aligned with template and pattern expectations.

## Expanding the Theme
- Add block patterns to `patterns/` and register in `src/Blocks.php`
- Add template parts in `parts/` and include via FSE template markup
- Add CSS in `assets/css/` and ensure it is enqueued in `src/Assets.php`
- Add component styles to `assets/base/`, `assets/blocks/`, etc.

## Conventions
- PHP class files are namespaced in `src/`
- Template and pattern files should be minimal and self-contained
- Avoid heavy inline styles; prefer design tokens in `theme.json` + `styles/`

## Example workflow
1. Add a new pattern file to `patterns/` (e.g., `card.php`).
2. Register in `src/Blocks.php` and confirm in `theme.json` if required.
3. Add or update the editor style in `assets/css/blocks/core/image.css`.
4. Add reference to pattern in `templates/singular.html` using `pattern` block.

## Glossary
- FSE: Full Site Editing.
- template-part: reusable HTML block part used in `templates/` and `parts/`.
- pattern: a prebuilt group of blocks that can be inserted into posts/pages.
- design tokens: shared values (colors, spacing) in `styles/*.json`, surfaced via `theme.json`.

## FAQ
Q: "I updated CSS but nothing changed in site editor." 
A: Flush browser cache, revisit Appearance → Editor, and confirm your 10up-toolkit build outputs are up-to-date.

Q: "My custom pattern doesn't appear in the inserter." 
A: Ensure pattern file is registered and `theme.json` includes the correct `blockPatterns` namespace.

Q: "Why are new template parts not used?" 
A: In FSE themes, template parts must be connected in the active template, then saved in editor. Use `parts/*.html` from site editor once created.

## Troubleshooting
- If editor styles not loading, confirm `theme.json` path and ensure styles are enqueued in `functions.php` via theme support.
- For block style changes, clear browser cache and regenerate CSS if using build tools.
