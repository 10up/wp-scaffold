# styles/

Design token and theme style definition files.

## Purpose
- Store theme color surfaces, semantics and design system scales in JSON.

## Current files
- `surface-primary.json`
- `surface-secondary.json`
- `surface-tertiary.json`

## Usage
- Import these values into `theme.json` and CSS variables during build/development.
- Keep tokens consistent with WordPress global styles and named `core/*` settings where practical.

## Extend
1. Add new token JSON file for spacing, typography, etc.
2. Update `theme.json` references in `settings` and `styles` sections.
3. Flush theme settings and refresh editor styles.

## Example
Add `styles/spacing.json` with values, then map in `theme.json` under `settings -> spacing` and `styles -> spacing`.

