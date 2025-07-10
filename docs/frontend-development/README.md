# Frontend Development

This section provides documentation on frontend development practices and standards used in a WP Scaffold project. It covers CSS/PostCSS, JavaScript, build processes, and responsive design approaches implemented in the themes.

## Themes

The WP Scaffold includes two themes:

1. **10up-theme**: A traditional WordPress theme with modern frontend development practices.
2. **10up-block-theme**: A block-based theme designed for the WordPress Full Site Editing experience.

## Directory Structure

Both themes follow a similar structure for frontend assets:

```
wp-content/themes/[theme-name]/
├── assets/
│   ├── css/
│   │   ├── frontend/
│   │   ├── blocks/
│   │   ├── globals/
│   │   └── mixins/
│   ├── js/
│   │   ├── frontend/
│   │   └── block-editor/
│   ├── images/
│   ├── fonts/
│   └── svg/
├── dist/
│   ├── css/
│   ├── js/
│   └── images/
└── src/ (PHP files for theme functionality)
```

## Documentation

The following documents provide detailed information on specific aspects of frontend development:

- [CSS Guidelines](css-guidelines.md): Standards and best practices for writing CSS/PostCSS
- [JavaScript Guidelines](javascript-guidelines.md): Standards and best practices for writing JavaScript
- [Frontend Build Process](frontend-build-process.md): Tools and workflows for compiling and optimizing frontend assets

## Build Process

The themes use a modern build process based on [10up Toolkit](https://github.com/10up/10up-toolkit/) to compile, optimize, and bundle frontend assets. This process is configured in the theme's `package.json` and related configuration files.

To get started with frontend development:

1. Navigate to the theme directory
2. Run `npm install` to install dependencies
3. Run `npm start` for development mode with file watching
4. Run `npm run build` for production builds
