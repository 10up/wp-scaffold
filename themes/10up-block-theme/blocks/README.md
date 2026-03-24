# blocks/

Contains block-specific files and support assets used by custom or core block functionality in this theme.

## Purpose
- Hold block templates and related assets used in block pattern construction
- Store block extensions/variations registration code under `assets/js/block-*`

## Recommended workflow
1. Add or update block style partials in `assets/css/blocks/`
2. Register block styles or variations in `assets/js/block-styles` / `assets/js/block-variations`
3. Keep block-specific template HTML in `blocks/` when providing reusable block-based fragments.

## If adding new custom blocks
- Keep block-specific logic outside of the theme in a plugin if when possible (best practice)
- If theme-level block registration is required, do it in `src/Blocks.php` and use proper `register_block_type()` actions.

## Example
To add a new block variation, create `blocks/hero.html` with block markup, then set up registration in `assets/js/block-variations/index.js` and ensure `10up-toolkit` picks it up from `blocks/`.

