# blocks/

Library of custom blocks for a theme

## Purpose
- Create custom blocks for a theme

## Example block: `example-block` (hello world)
1. Create `blocks/example-block/block.json` with metadata
2. Add `blocks/example-block/edit.tsx` and `blocks/example-block/index.ts`:
   - `BlockEdit` renders editor UI
   - `registerBlockType(metadata, { edit: BlockEdit, save: () => null })`
3. Add `blocks/example-block/markup.php` to output frontend markup:
   - Simple output: `<div class="wp-block-tenup-block-theme-example-block">Hello world</div>`
4. Add `blocks/example-block/style.css` for block style
5. Build the block bundle with `npm run build`