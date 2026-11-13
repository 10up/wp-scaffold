# CSS Globals

Every `.css` file directly in this folder is a globals file. The theme's `vite.config.ts` passes them to `wp()` with `css: { globals: ['assets/css/globals/*.css', ...] }`, and [Lightning CSS](https://lightningcss.dev) compiles every stylesheet with them ([`wpCss`](https://github.com/10up/wp-tooling/blob/trunk/packages/wp-vite-plugins/docs/wp-css.md)).

The `@custom-media` and `@custom-selector` definitions in these files are available to every stylesheet without an `@import`. So individual block styles, the main stylesheet, etc. all have access to these definitions.

> [!WARNING]
> These CSS files should not produce any output. They are only meant to define custom media and custom selectors for every stylesheet.
> Also the loading order of these files should not matter at all. They get auto included via a glob expression.

Mixins also get their special treatment. They have a special [`mixins`](../mixins/) folder located next to this `globals` folder
