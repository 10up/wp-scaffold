# PostCSS Global Mixins

Any individual `.css` files placed in this folder or any nested folder will automatically get loaded by the [postcss-mixins](https://github.com/postcss/postcss-mixins) plugin.

This ensures that the mixins defined in these files become available to all CSS entrypoints. So individual block styles, the main stylesheet, etc. all have access to these mixins.

> [!WARNING]
> These CSS files should not produce any output. They are only meant to define global postcss features to become available to all entrypoints
> Also the loading order of these files should not matter at all. They get auto included via a glob expression.

Other global definitions such as `@custom-media`, `@custom-selector`, etc. also get their special treatment. They have a special [`globals`](../globals/) folder located next to this `mixins` folder
