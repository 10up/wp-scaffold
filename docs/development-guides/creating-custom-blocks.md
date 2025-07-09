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
npm run scaffold:block my-block-name
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

```javascript
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

```javascript
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
const MyBlockEdit = (props) => {
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
export default MyBlockEdit;
```

## Server-Side Rendering

The WP Scaffold uses server-side rendering for blocks. The save.js file returns null, and the actual rendering is done in the markup.php file:

```javascript
// save.js
/**
 * Dynamic blocks do not save the HTML.
 *
 * @returns {null} Dynamic blocks do not save the HTML.
 */
const MyBlockSave = () => null;

export default MyBlockSave;
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

You can register block patterns to provide pre-designed layouts using your blocks:

```php
// This is handled in the Blocks.php file
register_block_pattern_category(
  '10up-theme',
  [ 'label' => __( '10up Theme', 'tenup-theme' ) ]
);

// You can add your own patterns
register_block_pattern(
  'tenup-theme/my-pattern',
  array(
    'title'       => __('My Pattern', 'tenup-theme'),
    'description' => __('A custom pattern', 'tenup-theme'),
    'categories'  => array('10up-theme'),
    'content'     => '<!-- wp:tenup/my-block {"title":"Pattern Title"} /-->'
  )
);
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

```javascript
// edit.js
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

export default function Edit() {
  const blockProps = useBlockProps();

  return (
    <div {...blockProps}>
      <InnerBlocks
        allowedBlocks={['core/paragraph', 'core/image']}
        template={[
          ['core/paragraph', { placeholder: 'Add content...' }]
        ]}
      />
    </div>
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

```javascript
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

export default function Edit({ attributes, setAttributes }) {
  const { title } = attributes;
  const blockProps = useBlockProps();

  return (
    <>
      <BlockControls>
        <ToolbarGroup>
          <ToolbarButton
            icon="editor-alignleft"
            title={__('Align Left', 'tenup-theme')}
            onClick={() => setAttributes({ alignment: 'left' })}
          />
        </ToolbarGroup>
      </BlockControls>
      <InspectorControls>
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
