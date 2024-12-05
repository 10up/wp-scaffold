# PostCSS Globals

Any individual `.css` files placed in this folder or any nested folder will automatically get loaded by the [@csstools/postcss-global-data](https://github.com/csstools/postcss-plugins/tree/main/plugins/postcss-global-data) plugin.

This ensures that the definitions defined in these files become available to all CSS entrypoints. So individual block styles, the main stylesheet, etc. all have access to these definitions.

> [!WARNING]
> These CSS files should not produce any output. They are only meant to define global postcss features to become available to all entrypoints
> Also the loading order of these files should not matter at all. They get auto included via a glob expression.

Mixins also get their special treatment. They have a special [`mixins`](../mixins/) folder located next to this `globals` folder
