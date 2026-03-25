# assets/

Contains static theme assets. Use this folder to keep the theme's source CSS, JavaScript, fonts, and images.

## Subfolders
- `css/`: core styles and split source files (`base`, `blocks`, `components`, `globals`, `mixins`, `templates`, `utilities`)
- `fonts/`: font files used by theme
- `images/`: image assets used in templates/patterns
- `js/`: JavaScript entrypoints and block feature extensions
- `svg/`: inline svg icons or assets

## How to use
- Keep editable CSS here.
- `js/frontend.js`: theme front-end behavior
- `js/block-*`: block variations, filters, styles, and extensions

## Notes
- Changes in this folder typically require a page refresh and sometimes rebuild. 
- When adding CSS for blocks, prefer adding to `assets/css/blocks/core/` to avoid global overrides.
