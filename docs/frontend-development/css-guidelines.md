# CSS Guidelines

This document outlines the standards and best practices for writing CSS in a WP Scaffold project.

## Table of Contents
- [Introduction](#introduction)
- [File Organization](#file-organization)
- [Naming Conventions](#naming-conventions)
- [CSS Features](#css-features)
- [CSS Architecture](#css-architecture)
- [Performance Considerations](#performance-considerations)
- [Accessibility](#accessibility)
- [Linting](#linting)

## Introduction

The WP Scaffold project writes plain CSS. The `wp()` plugin from [`@10up/wp-vite-plugins`](https://github.com/10up/wp-tooling/tree/trunk/packages/wp-vite-plugins) compiles every stylesheet with [Lightning CSS](https://lightningcss.dev): it inlines local `@import`s, lowers nesting and newer syntax for the browserslist targets, and minifies the output. It also adds custom media, custom selectors and mixins from shared globals files. These guidelines ensure consistency across the codebase and promote best practices for CSS development.

## File Organization

### Directory Structure

The WP Scaffold themes organize CSS files in the following structure:

```
wp-content/themes/[theme-name]/assets/css/
├── frontend/
│   ├── components/
│   ├── base/
│   └── templates/
├── blocks/
├── globals/
│   └── media-queries.css
└── mixins/
    └── visually-hidden.css
```

`globals/` and `mixins/` hold definitions only (see [Globals and Mixins](#globals-and-mixins)).

Compiled CSS is output to the `dist/css/` directory during the build process.

### Import Order

CSS imports are typically organized in the following order. Each local `@import` is inlined once at build time.

```css
/* Example import order */
/* Base styles */
@import 'frontend/base/reset.css';
@import 'frontend/base/layout.css';

/* Components */
@import 'frontend/components/buttons.css';
@import 'frontend/components/forms.css';
@import 'frontend/components/navigation.css';

/* Templates/Pages */
@import 'frontend/templates/home.css';
@import 'frontend/templates/single.css';
```

Don't import files from `globals/` or `mixins/`. Their definitions reach every stylesheet without an `@import`.

The block styles in the `blocks` directory are treated specially. They don't need to be imported in the main CSS file, but they will be automatically loaded and compiled into individual block stylesheets that are only loaded when that block is actually rendered on the frontend.

## Naming Conventions

### BEM Methodology

The project follows the BEM (Block, Element, Modifier) naming convention:

```css
.block {}
.block__element {}
.block--modifier {}
.block__element--modifier {}
```

Examples:

```css
.card {}
.card__title {}
.card__image {}
.card--featured {}
.card__title--large {}
```

### Prefixing

Use a project-specific prefix for global components to avoid conflicts:

```css
.scaffold-button {}
.scaffold-form {}
```

### State Classes

Use `is-` or `has-` prefixes for state classes:

```css
.is-active {}
.is-disabled {}
.has-error {}
```

## CSS Features

### Globals and Mixins

Each theme's `vite.config.ts` passes its globals files to `wp()`:

```ts
wp({
	// ...
	// Custom media, custom selectors and mixins every stylesheet can use.
	css: { globals: ['assets/css/globals/*.css', 'assets/css/mixins/*.css'] },
}),
```

`@custom-media`, `@custom-selector` and `@define-mixin` definitions in these files are available in every stylesheet: CSS entries, block styles and the files they import. You don't import them. These files must not produce any output of their own. See the [`wpCss` documentation](https://github.com/10up/wp-tooling/blob/trunk/packages/wp-vite-plugins/docs/wp-css.md).

### Media Query Handling

Use the custom media queries from `globals/media-queries.css`:

```css
/* Example media query usage */
.container {
  width: 100%;

  @media (--bp-medium) {
    width: 50%;
  }

  @media (--bp-large) {
    width: 33.333%;
  }
}
```

### Custom Selectors

Use custom selectors to create reusable selector patterns. Define them in a `globals/` file to share them across stylesheets:

```css
/* Example custom selectors */
@custom-selector :--heading h1, h2, h3, h4, h5, h6;
@custom-selector :--button .btn, .button;

:--heading {
  font-family: var(--font-family-base);
  line-height: 1.2;
}

:--button {
  display: inline-block;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
```

### Mixins

Define a mixin with `@define-mixin` in a `mixins/` file. `@mixin name;` pastes its declarations in place, so a declaration after it overrides it:

```css
/* mixins/visually-hidden.css */
@define-mixin visually-hidden {
	border: 0;
	clip-path: inset(50%);
	height: 1px;
	margin: -1px;
	overflow: hidden;
	padding: 0;
	position: absolute;
	width: 1px;
}

/* Any stylesheet */
.visually-hidden {
	@mixin visually-hidden;
}
```

An unknown mixin or custom selector fails the build. Mixins can't take parameters.

## CSS Architecture

### Component-Based Approach

Follow a component-based approach where each UI component has its own CSS file:

```css
/* Example component files */
// card.css
.card {
}

// button.css
.button {
}
```

### Single Responsibility Principle

Each class should have a single responsibility:

```css
/* Good */
.card {
  border: 1px solid #ddd;
}

.card__title {
  font-size: 1.125rem
}

/* Avoid */
.card {
  border: 1px solid #ddd;

  h2 {
    font-size: 1.125rem
  }
}
```

### Utility Classes

Use utility classes for common, single-purpose styles:

```css
.text-center { text-align: center; }
.margin-top { margin-top: 16px; }
.hidden { display: none; }
```

## Performance Considerations

### Selector Efficiency

- Avoid deeply nested selectors
- Minimize the use of universal selectors (`*`)
- Avoid overqualified selectors (`ul.navigation`)

### CSS Specificity

- Keep specificity as low as possible
- Avoid using `!important` (except in utility classes)
- Use classes instead of IDs for styling

### File Size

- Remove unused CSS
- Use shorthand properties
- Minify CSS for production

## Accessibility

### Color Contrast

Ensure sufficient color contrast for text readability:

```css
/* Good */
.text {
  color: #333;
  background-color: #fff;
}

/* Avoid */
.text {
  color: #ccc;
  background-color: #fff;
}
```

### Focus States

Always style focus states for interactive elements:

```css
/* Example focus state */
.button {
  &:focus-visible {
    outline: 2px solid #007bff;
    outline-offset: 2px;
  }
}
```

### Screen Reader Text

Include utility classes for screen reader text:

```css
.screen-reader-text {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip-path: inset(50%);
  white-space: nowrap;
  border: 0;
}
```

## Linting

The project uses Stylelint to lint CSS. One `stylelint.config.js` at the repository root extends [`@10up/stylelint-config`](https://github.com/10up/wp-tooling/tree/trunk/packages/stylelint-config):

```js
const config = {
	extends: ['@10up/stylelint-config'],
	rules: {},
};

module.exports = config;
```

The config knows `@define-mixin`, `@mixin` and `@custom-selector`. It leaves formatting to Oxfmt: `vp fmt` (part of `vp check`) formats CSS, and Stylelint only lints it.

To run the linter from the repository root:

```bash
npm run lint-style
```

It lints `themes/*/assets/css/**/*.css` and `mu-plugins/*/assets/css/**/*.css`. Before each commit, `vp staged` runs `vp fmt` and `stylelint --fix` on staged CSS files.
