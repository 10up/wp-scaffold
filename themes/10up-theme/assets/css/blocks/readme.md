# Block Specific Styles

This directory contains block-specific styles. And file you create in this directory will be automatically included in the editor and on the front end when the block is used. The file should be named after the blocks name and be placed in a directory named after the blocks namespace.

So if you have some styles you want to only load when the `core/paragraph` block is used, you would create a file at `wp-content/themes/10up-theme/assets/css/blocks/core/paragraph.css`.

Similarly if you work with a block from a plugin that has a namespace of `acme`, you would create a file at `wp-content/themes/10up-theme/assets/css/blocks/acme/block-name.css`.
