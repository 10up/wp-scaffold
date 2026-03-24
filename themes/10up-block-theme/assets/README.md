# assets/

Contains static theme assets. Use this folder to keep the theme's source CSS, JavaScript, fonts, and images.

## Subfolders
- `css/`: core styles and split source files (`base`, `blocks`, `components`, `globals`, `mixins`, `templates`, `utilities`)
- `fonts/`: font files used by theme
- `images/`: image assets used in templates/patterns
- `js/`: JavaScript entrypoints and block feature extensions
- `svg/`: inline svg icons or assets

## How to use
- Keep editable CSS here. Generated/minified CSS should go to the same folder if no build step; with tooling, generated artifacts may be created in `build/` (not present here).
- `js/frontend.js`: theme front-end behavior
- `js/block-*`: block variations, filters, styles registration helpers

## Notes
- Changes in this folder typically require a page refresh and sometimes rebuild (if using a build chain). 
- When adding CSS for blocks, prefer adding to `assets/css/blocks/core/` to avoid global overrides.

## Example
Add a new editor style file: `assets/css/blocks/core/alert.css`, import it in your main style entry (if required), and run `npm run build`. In `src/Assets.php`, confirm `wp_enqueue_style` includes the generated file for both front-end and editor.

