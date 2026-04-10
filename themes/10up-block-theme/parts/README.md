# parts/

Reusable theme parts used in block templates and patterns.

## Contents
- `header.html`, `footer.html`, and area-specific part files like `site-header-navigation-area.html` and `site-footer-legal-navigation-area.html`.

## Usage
- Use these as `template-part` blocks from the Site Editor (Appearance -> Editor).
- Keep markup minimal: structure and block placeholders, avoid heavy styling directly in these files.
- Update in the Site Editor to automatically persist changes if desired, or use these files as source-of-truth for versioned code.

## Example
Create `parts/site-header-navigation-area.html` with a `navigation` block and save. In `templates/index.html`, include it as `<!-- wp:template-part {"slug":"site-header-navigation-area"} /-->`.

