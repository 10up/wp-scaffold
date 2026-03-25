# src/

PHP core theme classes and wrapper logic.

## Purpose
- Keep PHP functions and setup logic organized in class-based files.
- Avoid scattering code inside `functions.php`.

## Files
- `Assets.php`: enqueue styles/scripts and add theme support options.
- `Blocks.php`: block style/variation registration (feature flags, etc.).
- `TemplateTags.php`: helper functions for templates.
- `ThemeCore.php`: bootstraps theme features and ties registration callbacks together.

## Best practice
- Add new features as single-responsibility classes or methods.
- Keep public API in ThemeCore and specific feature classes.

## Loading
- `functions.php` should instantiate `ThemeCore` (or similar) and call init actions.
- Keep `functions.php` as container only, with as little direct logic as possible.
