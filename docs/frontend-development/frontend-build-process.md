# Frontend Build Process

This document outlines the frontend build process used in a WP Scaffold project, including the tools, configuration, and workflows for compiling and optimizing frontend assets.

## Table of Contents
- [Overview](#overview)
- [Prerequisites](#prerequisites)
- [Build Tools](#build-tools)
- [Project Structure](#project-structure)
- [NPM Scripts](#npm-scripts)
- [Webpack Configuration](#webpack-configuration)
- [Asset Optimization](#asset-optimization)
- [SVG Icons](#svg-icons)
- [Development Workflow](#development-workflow)
- [Production Builds](#production-builds)
- [Troubleshooting](#troubleshooting)

## Overview

The WP Scaffold project uses a modern frontend build process to compile, optimize, and bundle assets such as JavaScript, CSS, and images. This process ensures that the frontend code is efficient, maintainable, and follows best practices.

## Prerequisites

Before working with the frontend build process, ensure you have:

- [Node.js](https://nodejs.org/) (version 24 or later)
- [npm](https://www.npmjs.com/) (usually comes with Node.js)
- Basic understanding of JavaScript, CSS, and build tools
- Local development environment set up (see [Local Development](../testing-quality/local-development.md))

## Build Tools

The WP Scaffold project uses the following build tools:

- **Webpack**: Module bundler for JavaScript and other assets
- **Babel**: JavaScript compiler for using next-generation JavaScript features
- **PostCSS**: Primary CSS processor with plugins for modern CSS features, transformations, and optimizations
- **ESLint**: JavaScript linter for identifying and fixing code issues
- **Stylelint**: CSS linter for consistent code style

## Project Structure

The frontend assets in the WP Scaffold themes are organized in the following structure:

```
wp-content/themes/[theme-name]/
├── assets/
│   ├── js/
│   │   ├── frontend/
│   │   │   ├── components/
│   │   │   └── frontend.js
│   │   └── block-editor/
│   ├── css/
│   │   ├── frontend/
│   │   ├── blocks/
│   │   ├── globals/
│   │   └── mixins/
│   ├── images/
│   ├── fonts/
│   └── svg/
├── dist/ (compiled assets)
│   ├── css/
│   ├── js/
│   └── images/
├── blocks/ (Gutenberg blocks)
├── src/ (PHP files)
├── package.json
└── webpack.config.js
```

## NPM Scripts

The project includes several npm scripts to streamline the build process. These scripts are defined in the `package.json` file:

```json
{
  "scripts": {
    "start": "webpack --watch --mode=development",
    "build": "webpack --mode=production",
    "lint:js": "eslint assets/js/src",
    "lint:css": "stylelint assets/css/**/*.css",
    "lint": "npm run lint:js && npm run lint:css",
    "test": "jest"
  }
}
```

### Common Commands

- `npm start`: Start the development build process with file watching
- `npm run build`: Create a production-ready build
- `npm run lint`: Run linting on JavaScript and CSS files
- `npm test`: Run JavaScript tests

## Webpack Configuration
The WP Scaffold project uses [10up Toolkit](https://github.com/10up/10up-toolkit/) for Webpack configuration, which provides a standardized setup for building WordPress themes.

This configuration can be overridden if needed. Please see the [10up Toolkit documentation](https://github.com/10up/10up-toolkit/blob/develop/packages/toolkit/README.md#customizations) for more details on how to customize the Webpack setup.

## Asset Optimization

### JavaScript Optimization

JavaScript files are optimized through:

- **Transpilation**: Converting modern JavaScript to browser-compatible code using Babel
- **Bundling**: Combining multiple JavaScript files into optimized bundles
- **Minification**: Reducing file size by removing whitespace and shortening variable names
- **Tree Shaking**: Eliminating unused code from the final bundle

### CSS Optimization

CSS files are optimized through:

- **PostCSS Processing**: Applying various transformations and optimizations via PostCSS plugins
- **Autoprefixer**: Adding vendor prefixes for cross-browser compatibility
- **Minification**: Reducing file size by removing whitespace and optimizing selectors
- **Source Maps**: Generating source maps for easier debugging

### Image Optimization

Images are optimized through:

- **Compression**: Reducing file size without significant quality loss
- **Responsive Images**: Generating multiple sizes for different devices
- **SVG Optimization**: Cleaning and minifying SVG files

## SVG Icons

SVG files in a theme's `assets/svg/` folder are built to `dist/svg/` and registered as icons by the theme's `Assets` class. Since WordPress 7.0 core sanitizes every registered icon in `WP_Icons_Registry::sanitize_icon_content()` with a fixed `wp_kses` allowlist that has no filter:

| Element | Allowed attributes |
| --- | --- |
| `svg` | `class`, `xmlns`, `width`, `height`, `viewBox`, `aria-hidden`, `role`, `focusable` |
| `path` | `fill`, `fill-rule`, `d`, `transform` |
| `polygon` | `fill`, `fill-rule`, `points`, `transform`, `focusable` |

Everything else is removed without a warning: elements such as `g`, `line`, `polyline`, `circle`, `rect`, `ellipse`, `defs`, `clipPath`, `use`, `title` and `style`, and attributes such as `stroke`, `stroke-width`, `fill-opacity`, `opacity`, `clip-rule`, `id` and `style`. The icon still registers, but it renders empty or wrong. A typical stroke-based icon set (Feather, Lucide) comes back as an empty `<svg>`.

Run the check with:

```bash
npm run lint-svg                                 # every themes/*/assets/svg and mu-plugins/*/assets/svg
npm run lint-svg -- themes/my-theme/assets/svg   # a folder, file or glob
```

It also runs on staged icons before each commit and in CI. XML declarations, doctypes and comments are fine; kses strips them harmlessly.

To fix an icon it flags:

- **Strokes:** outline the strokes in your design tool before exporting (Figma: *Outline stroke*), so the icon is drawn with filled paths. Use `fill="currentColor"` on the paths if the icon should follow the text color.
- **Shapes** (`line`, `polyline`, `circle`, `rect`, `ellipse`): convert them to paths, for example with SVGO's `convertShapeToPath`.
- **Groups:** flatten `<g>` wrappers and move any `fill` or `transform` they carry onto the paths.

Core is widening the allowlist (see [WordPress/wordpress-develop#12197](https://github.com/WordPress/wordpress-develop/pull/12197) and [#13539](https://github.com/WordPress/wordpress-develop/pull/13539)). When a release ships with a wider list, update the `ALLOWED` constant in `bin/lint-svg.mjs` to match.

## Development Workflow

### Starting Development

1. Navigate to your theme directory:
   ```bash
   cd wp-content/themes/your-theme
   ```

2. Install dependencies (if not already installed):
   ```bash
   npm install
   ```

3. Start the development build process:
   ```bash
   npm start
   ```

This will start Webpack in watch mode, which will automatically rebuild assets when files change.

### Working with PostCSS

When working with CSS and PostCSS:

1. Create or edit files in the `assets/css/` directory
2. Follow the project's CSS guidelines (see [CSS Guidelines](./css-guidelines.md))
3. Import your CSS files in the main entry point (`assets/css/frontend.css`)

### Working with JavaScript

When working with JavaScript:

1. Create or edit files in the `assets/js/src/` directory
2. Follow the project's JavaScript guidelines (see [JavaScript Guidelines](./javascript-guidelines.md))
3. Import your JavaScript modules in the main entry point (`assets/js/src/main.js`)

## Production Builds

When preparing for production:

1. Run linting to check for code issues:
   ```bash
   npm run lint
   ```

2. Run tests to ensure functionality:
   ```bash
   npm test
   ```

3. Create a production build:
   ```bash
   npm run build
   ```

This will generate optimized, production-ready assets in the `dist` directories.

## Troubleshooting

### Common Issues

- **Node.js version conflicts**: Ensure you're using the correct Node.js version (check `.nvmrc` if available)
- **Missing dependencies**: Run `npm install` to ensure all dependencies are installed
- **Build errors**: Check the console output for specific error messages
- **Asset not updating**: Ensure the file is properly imported and the build process is running

### Debugging Tips

- Check the browser console for JavaScript errors
- Use source maps to debug JavaScript and CSS issues
- Clear your browser cache if you're not seeing changes
- Try stopping and restarting the build process
