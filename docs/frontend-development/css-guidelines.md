# PostCSS/CSS Guidelines

This document outlines the standards and best practices for writing PostCSS/CSS in a WP Scaffold project.

## Table of Contents
- [Introduction](#introduction)
- [File Organization](#file-organization)
- [Naming Conventions](#naming-conventions)
- [PostCSS Features](#postcss-features)
- [CSS Architecture](#css-architecture)
- [Performance Considerations](#performance-considerations)
- [Accessibility](#accessibility)
- [Linting](#linting)

## Introduction

The WP Scaffold project uses PostCSS as the primary CSS processor to create more maintainable and organized stylesheets. PostCSS allows for modern CSS features and custom plugins while maintaining a clean and efficient workflow. These guidelines ensure consistency across the codebase and promote best practices for CSS development.

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
│   ├── _variables.css
│   ├── _typography.css
│   └── _global.css
└── mixins/
    ├── _responsive.css
    └── _utilities.css
```

Compiled CSS is output to the `dist/css/` directory during the build process.

### Import Order

CSS imports with PostCSS are typically organized in the following order:

```css
/* Example import order */
/* Globals */
@import 'globals/variables.css';
@import 'globals/typography.css';
@import 'globals/global.css';

/* Mixins */
@import 'mixins/responsive.css';
@import 'mixins/utilities.css';

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

## PostCSS Features

### PostCSS Plugins

The WP Scaffold project comes with a preconfigured PostCSS setup that includes several useful features and plugins. If you need to customize or add new PostCSS plugins, you can do so by following the instructions within [10up Toolkit](https://github.com/10up/10up-toolkit/blob/develop/packages/toolkit/README.md#customizing-postcss).

### Media Query Handling

Use media queries with PostCSS:

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

Use custom selectors to create reusable selector patterns:

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

```scss
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
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
```

## Linting

The project uses Stylelint to enforce coding standards. The configuration is in `.stylelintrc`:

```json
{
  "extends": "stylelint-config-standard-scss",
  "rules": {
    "indentation": 2,
    "string-quotes": "single",
    "no-duplicate-selectors": true,
    "color-hex-case": "lower",
    "color-hex-length": "short",
    "selector-combinator-space-after": "always",
    "selector-attribute-quotes": "always",
    "declaration-block-trailing-semicolon": "always",
    "declaration-colon-space-before": "never",
    "declaration-colon-space-after": "always",
    "property-no-vendor-prefix": true,
    "value-no-vendor-prefix": true,
    "selector-no-vendor-prefix": true,
    "media-feature-name-no-vendor-prefix": true
  }
}
```

To run the linter:

```bash
npm run lint:css
```
