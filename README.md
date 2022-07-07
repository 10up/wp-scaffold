# 10up WP Scaffold

This scaffold is the starting point for all 10up WordPress projects.

It contains a bare bones theme and must use plugin for you to base your development off of. Asset bundling is handled entirely by [10up Toolkit](https://github.com/10up/10up-toolkit).

## Requirements

- Node >= 16
- NPM >= 7

## How to Use

1. [Download a zip](https://github.com/10up/wp-scaffold/archive/trunk.zip) of the repository into your project. At 10up, by default we version control the `wp-content` directory (ignoring obvious things like `uploads`). This enables us to have plugins, theme, etc. all in one repository. Having separate repositories for each plugin and theme only happens in rare circumstances that are outside of our control.
2. Take what you need. If your project doesn't have a theme, remove the theme. If your project doesn't need any plugin functionality, remove the MU plugin. If your plugin doesn't need CSS/JS, remove it. If your plugin doesn't need to be translated, remove all the translation functionality.
3. Compiling, minifying, bundling, etc. of JavaScript and CSS is all done by [10up Toolkit](https://github.com/10up/10up-toolkit). 10up Toolkit is included as a dev dependency in both the plugin and theme. If you want to develop on the theme (and vice-versa the plugin), you would navigate to the theme directory in your console and run `npm run start` (after running `npm install` first of course). Inside `package.json` edit `10up-toolkit.devURL` to your local development URL for if you're not using a `.test`. `10up-toolkit.entry` are the paths to CSS/JS files that need to be bundled. Edit these as needed.
4. Make sure to add `define( 'SCRIPT_DEBUG', true )` to `wp-config.php` to enable Hot Module Reload and React Fast Refresh.
5. [npm workspaces](https://docs.npmjs.com/cli/v7/using-npm/workspaces) is used to manage npm dependencies. The main benefit of using npm workspaces is that we can hoist all dependencies to the root folder and avoid installing duplicate dependencies, saving time and space. By default the `workspaces` config are setup so that `mu-plugins/10up-plugin` and all themes are treated as "packages", if you are building a new plugin/theme make sure to update `workspaces` in `package.json` See the example below:

```json
  "workspaces": [
		"themes/*",
		"mu-plugins/10up-plugin",
		"mu-plugins/my-other-awesome-10up-plugin",
  ],
```
6. To build plugins/themes simply run `npm install` at the root and `npm run [build|start|watch]` and npm will automatically build all themes and plugins.
7. `npm workspaces` do not have the ability to run scripts from multiple packages in parrallel. Because of that we use the `npm-run-all` package and we define specific scripts in `package.json` so you will need to update the `watch:*` scripts in `package.json` and replace `tenup-theme` and `tenup-plugin` with the actual package names.

```json
	"watch:theme": "npm run watch -w=tenup-theme",
	"watch:plugin": "npm run watch -w=tenup-plugin",
	"watch": "run-s watch:theme watch:plugin",
```
7. To add npm dependencies to your theme and/or plugins add the `-w=package-name` flag to the `npm install` command. E.g: `npm install --save prop-types -w=tenup-plugin` **DO NOT RUN** `npm install` inside an individual workspace/package. Always run the from the root folder.
8. If you're building Gutenberg blocks and importing `@wordpress/*` packages, **you do not** need to manually install them as `10up-toolkit` will handle these packages properly.

## Scaffold Rules

Much of the functionality in the scaffold is intended to be optional depending on the needs of your project e.g. i18n functionality. However, there are a few important principles that you must follow:

1. [10up Toolkit](https://github.com/10up/10up-toolkit) must be used for asset bundling. Over the years we've found differences in how assets are built across projects to be very confusing for engineers.  As such, we are standardizing on 10up Toolkit (which you can extend as needed). 10up Toolkit contains in depth docs on how it works.
2. Functionality should be built into the 10up must-use functionality as much as possible. Presentation should be kept in the theme. Separating these two makes long term development, maintenance, and extensibility much easier.
3. Blocks should be built into the theme and follow the [example block](https://github.com/10up/wp-scaffold/tree/trunk/themes/10up-theme/includes/blocks/example-block) provided.
5. When creating new themes or plugins make sure to  follow the `scripts` convention:
```json
  "scripts": {
    "start": "npm run watch",
    "watch": "10up-toolkit watch --hot",
    "build": "10up-toolkit build",
    "format-js": "10up-toolkit format-js",
    "lint-js": "10up-toolkit lint-js",
    "lint-style": "10up-toolkit lint-style",
    "test": "10up-toolkit test-unit-jest",
    "clean-dist": "rm -rf ./dist"
  },
```

## Husky and Lint-Staged

Husky and Lint-Staged are both set up to run on the pre-commit hook. The lint-staged configuration file is available to edit in `.lintstagedrc.json`.
By default it will run the following:

- `eslint` on JS and JSX files.
- `stylelint` on CSS files.
- `phpcs` on PHP files.
