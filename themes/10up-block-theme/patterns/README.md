# patterns/

Block patterns and registration helper files in PHP.

## Purpose
- Provide reusable block pattern snippets for site editor insertion.
- Register patterns with WordPress (if required) via `patterns/card.php`.

## How to add
1. Create a new pattern file with HTML markup for the pattern.
2. Register the pattern in `src/Patterns` or wherever you keep pattern registration (matching `src/Blocks.php` style).
3. Follow the block pattern guidelines: static markup + dynamic classes/attributes controlled by user styles.

## Tips
- Use semantic wrapper blocks and clear naming.
- Separate pattern concerns (layout vs presentation).
- Document pattern usage in a comment near registration.

## Example
Implement `patterns/card.php` with a card block group, then import or register it via `register_block_pattern('10up/card', array(...))` in `src/Blocks.php`. Confirm pattern appears in inserter with `10up/card` namespace.

