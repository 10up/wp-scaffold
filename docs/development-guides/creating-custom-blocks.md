# Creating Custom Blocks

This guide will walk you through the process of creating custom Gutenberg blocks for a WP Scaffold project.

## Table of Contents
- [Introduction](#introduction)
- [Prerequisites](#prerequisites)
- [Block Development Workflow](#block-development-workflow)
- [Using the Scaffold Block Command](#using-the-scaffold-block-command)
- [Block Structure](#block-structure)
- [Block Registration](#block-registration)
- [Block Attributes](#block-attributes)
- [Edit Function](#edit-function)
- [Server-Side Rendering](#server-side-rendering)
- [Block Styles](#block-styles)
- [Block Patterns](#block-patterns)
- [Testing Your Block](#testing-your-block)
- [Advanced Techniques](#advanced-techniques)
- [Troubleshooting](#troubleshooting)

## Introduction

Gutenberg blocks are the building blocks of the WordPress editor. They allow users to create rich content layouts with a user-friendly interface. This guide will help you create custom blocks that integrate seamlessly with a WP Scaffold project.

## Prerequisites

Before you begin, ensure you have:

- A local development environment set up
- Familiarity with JavaScript, React, and WordPress
- Node.js and npm installed
- The WP Scaffold project set up

## Block Development Workflow

The recommended workflow for developing custom blocks is:

1. Plan your block's functionality and UI
2. Use the scaffold:block command to generate the block structure
3. Implement the edit function for the editor interface
4. Implement the server-side rendering with markup.php
5. Add styles and additional features
6. Test and refine your block

## Using the Scaffold Block Command

The WP Scaffold includes a convenient npm command to generate a new block:

```bash
npm run scaffold:block -w=tenup-theme
```

This command will create a new block in the `blocks` directory with all the necessary files.

## Block Structure

When you create a block using the scaffold:block command, it generates the following structure:

```
blocks/my-block-name/
├── block.json    # Block metadata
├── edit.js       # Editor interface
├── index.js      # Block registration
├── markup.php    # Server-side rendering
└── save.js       # Empty save function (for dynamic blocks)
```

## Block Registration

The block.json file defines your block's metadata:

```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "title": "My Block",
  "description": "A custom block",
  "textdomain": "tenup-theme",
  "name": "tenup/my-block",
  "icon": "feedback",
  "category": "formatting",
  "attributes": {
    "title": {
      "type": "string"
    }
  },
  "supports": {
    "html": false
  },
  "editorScript": "file:./index.js"
}
```

The index.js file registers your block with WordPress:

```jsx
/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';
import save from './save';
import block from './block.json';

/**
 * Register block
 */
registerBlockType(block, {
  edit,
  save,
});
```

## Block Attributes

Attributes define the data stored by your block. Define them in your `block.json` file:

```json
{
  "attributes": {
    "title": {
      "type": "string"
    },
    "content": {
      "type": "string"
    }
  }
}
```

## Edit Function

The edit function defines how your block appears in the editor:

```jsx
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';

/**
 * Edit component.
 *
 * @param {object}   props                  The block props.
 * @param {object}   props.attributes       Block attributes.
 * @param {string}   props.attributes.title Custom title to be displayed.
 * @param {string}   props.className        Class name for the block.
 * @param {Function} props.setAttributes    Sets the value for block attributes.
 * @returns {Function} Render the edit screen
 */
export const MyBlockEdit = (props) => {
  const { attributes, setAttributes } = props;
  const { title } = attributes;

  const blockProps = useBlockProps();

  return (
    <div {...blockProps}>
      <RichText
        className="wp-block-my-block__title"
        tagName="h2"
        placeholder={__('Custom Title')}
        value={title}
        onChange={(title) => setAttributes({ title })}
      />
    </div>
  );
};
```

## Server-Side Rendering

The WP Scaffold uses server-side rendering for blocks. The save.js file returns null, and the actual rendering is done in the markup.php file. Only when we are including inner blocks in our block, we need to add the return <InnerBlocks.Content /> in the save.js file so that the inner blocks are saved.

We are using dynamic blocks instead of saving the HTML to the database via the save.js file because it allows us to make modifications to the markup of any block down the line without having to worry about deprecation issues or about how we need to update older instances of the block. For the type of work we are doing, where clients have new requests and we need to make changes to the block, it's much easier to do so with dynamic blocks.

```javascript
// save.js
/**
 * Dynamic blocks do not save the HTML unless for saving inner blocks.
 *
 * @returns {null} Dynamic blocks do not save the HTML.
 */
export const MyBlockSave = () => null;
```

```php
// markup.php
<?php
/**
 * Block markup
 *
 * @package TenUpTheme\Blocks\MyBlock
 *
 * @var array    $attributes         Block attributes.
 * @var string   $content            Block content.
 * @var WP_Block $block              Block instance.
 * @var array    $context            Block context.
 */

?>
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
  <h2 class="wp-block-tenup-my-block__title">
    <?php echo wp_kses_post( $attributes['title'] ); ?>
  </h2>
</div>
```

## Block Styles

You can add styles for your block by creating CSS files that will be automatically loaded. The build process handles this for you.

For block-specific styles, they will be automatically enqueued if placed in the appropriate location:

```
dist/blocks/autoenqueue/tenup/my-block.css
```

## Block Patterns

You can register block patterns to provide pre-designed layouts by creating a pattern file inside the theme's `patterns` directory. Any pattern file will be automatically loaded and registered. It needs to contain some metadata in the header to be properly registered.

```php
// patterns/my-pattern.php
<?php
/**
 * Title: My Pattern
 * Slug: tenup-theme/my-pattern
 * Description: A custom pattern
 * Inserter: true
 *
 * @package TenupBlockTheme
 */

?>
<!-- wp:paragraph -->
<p>My pattern content</p>
<!-- /wp:paragraph -->
```

## Testing Your Block

To test your block:

1. Build your block assets:
   ```bash
   npm run build
   ```

2. Or use the watch mode during development:
   ```bash
   npm run watch
   ```

3. Open the WordPress editor
4. Add your block to a post or page
5. Test all functionality and interactions
6. Preview the post to see how the block renders on the frontend

## Advanced Techniques

### InnerBlocks

Allow nested blocks within your block:

```jsx
// edit.js
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function Edit() {
  const blockProps = useBlockProps();
  const innerBlocksProps = useInnerBlocksProps( blockProps, {
    template: [
      ['core/paragraph', { placeholder: 'Add content...' }]
    ],
    allowedBlocks: ['core/paragraph', 'core/image']
  });

  return (
    <div {...innerBlocksProps} />
  );
}
```

```php
// markup.php
<div <?php echo get_block_wrapper_attributes(); ?>>
  <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
```

### Block Controls

Add controls to your block:

```jsx
// edit.js
import {
  useBlockProps,
  RichText,
  BlockControls,
  InspectorControls,
} from '@wordpress/block-editor';
import {
  PanelBody,
  ToolbarGroup,
  ToolbarButton
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { alignLeft } from '@wordpress/icons';

export default function Edit({ attributes, setAttributes }) {
  const { title } = attributes;
  const blockProps = useBlockProps();

  return (
    <>
      <BlockControls group="block">
        <ToolbarButton
          icon={alignLeft}
          title={__('Align Left', 'tenup-theme')}
          onClick={() => setAttributes({ alignment: 'left' })}
        />
      </BlockControls>
      <InspectorControls group="settings">
        <PanelBody title={__('Block Settings', 'tenup-theme')}>
          {/* Add your controls here */}
        </PanelBody>
      </InspectorControls>
      <div {...blockProps}>
        <RichText
          tagName="h2"
          value={title}
          onChange={(title) => setAttributes({ title })}
          placeholder={__('Enter title...', 'tenup-theme')}
        />
      </div>
    </>
  );
}
```

## Troubleshooting

### Common Issues

- **Block not appearing in the editor**: Check that your block is properly registered and that the block.json file is correctly formatted.
- **Styles not applying**: Verify that your CSS files are being generated in the correct location.
- **Server-side rendering not working**: Check that your markup.php file is correctly formatted and that the block is registered with a render_callback.

### Debugging Tips

- Use `console.log()` in your JavaScript files to debug issues.
- Check the browser console for errors.
- Examine the Blocks.php file to understand how blocks are registered and loaded.
- Use the WordPress block validator to identify issues.
