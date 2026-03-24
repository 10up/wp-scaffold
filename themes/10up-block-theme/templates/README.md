# templates/

Full-site editing templates for the theme.

## Files
- `404.html`
- `index.html`
- `single.html`
- `singular.html`

## How it works
- These templates are used by WordPress FSE to render core view routes.
- Use block markup in these files and include `template-part` blocks for reusable sections.

## Customization
- Add new templates (`archive.html`, `page.html`, etc.) as needed.
- Use the site editor to override templates in the official user interface.
- Keep templates clean by delegating repeating content to `parts/` template parts.

## Example
Add `templates/page.html` with a `template-part` for header and `query` block, then copy into site editor and save to make it active. Use `parts/site-footer-legal-navigation-area.html` for footer insert.

