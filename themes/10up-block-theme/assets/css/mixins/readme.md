# CSS Global Mixins

Every `.css` file directly in this folder is a globals file. The theme's `vite.config.ts` passes them to `wp()` with `css: { globals: [..., 'assets/css/mixins/*.css'] }`, and [Lightning CSS](https://lightningcss.dev) compiles every stylesheet with them ([`wpCss`](https://github.com/10up/wp-tooling/blob/trunk/packages/wp-vite-plugins/docs/wp-css.md)).

The mixins defined here with `@define-mixin name { ... }` are available to every stylesheet without an `@import`. So individual block styles, the main stylesheet, etc. all have access to these mixins. `@mixin name;` pastes the mixin's declarations in place.

> [!WARNING]
> These CSS files should not produce any output. They are only meant to define mixins for every stylesheet.
> Also the loading order of these files should not matter at all. They get auto included via a glob expression.
> Other global definitions such as `@custom-media`, `@custom-selector`, etc. also get their special treatment. They have a special [`globals`](../globals/) folder located next to this `mixins` folder
