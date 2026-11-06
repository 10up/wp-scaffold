# Creating Custom Blocks

This guide will walk you through the process of creating custom Gutenberg blocks for a WP Scaffold project.

## Table of Contents
- [Introduction](#introduction)
- [Prerequisites](#prerequisites)
- [Block Development Workflow](#block-development-workflow)
- [Using the Scaffold Block Command](#using-the-scaffold-block-command)
- [Choosing a Variant](#choosing-a-variant)
- [Block Structure](#block-structure)
- [Block Registration](#block-registration)
- [Block Attributes](#block-attributes)
- [Edit Component](#edit-component)
- [Server-Side Rendering](#server-side-rendering)
- [Frontend Interactivity](#frontend-interactivity)
- [Block Styles](#block-styles)
- [Block Patterns](#block-patterns)
- [Editor Defaults](#editor-defaults)
- [Testing Your Block](#testing-your-block)
- [Advanced Techniques](#advanced-techniques)
- [Troubleshooting](#troubleshooting)

## Introduction

Gutenberg blocks are the building blocks of the WordPress editor. They allow users to create rich content layouts with a user-friendly interface. This guide will help you create custom blocks that integrate seamlessly with a WP Scaffold project.

Before building a block, check whether a core block, a pattern, or a block style already covers the need. Build a custom block when you need logic, data, or an editing experience core can't provide.

## Prerequisites

Before you begin, ensure you have:

- A local development environment set up
- Familiarity with TypeScript, React, and WordPress
- Node.js and npm installed
- The WP Scaffold project set up

## Block Development Workflow

The recommended workflow for developing custom blocks is:

1. Plan your block's functionality and UI
2. Use the `scaffold:block` command to generate the block
3. Implement the edit component (`edit.tsx`) for the editor interface
4. Implement the server-side rendering in `render.php`
5. Add styles and, if needed, frontend interactivity (`view.ts`)
6. Test and refine your block

## Using the Scaffold Block Command

Run the command from the project root and pass the theme's workspace name:

```bash
# Block theme
npm run scaffold:block -w=tenup-block-theme

# Classic theme
npm run scaffold:block -w=tenup-theme
```

The command prompts for the variant, slug, namespace, title, description, dashicon, and category, and creates the block in the theme's `blocks/` directory. The namespace defaults to the project slug and the text domain is read from the theme's `style.css`.

You can skip the prompts by passing the slug and options directly:

```bash
npm run scaffold:block -w=tenup-block-theme -- my-block --variant interactive
```

## Choosing a Variant

| Variant | Use it when | What you get |
|---|---|---|
| `default` | The block displays content editors type in place | A `content` attribute edited inline with `RichText`, rendered in `render.php` |
| `innerBlocks` | The block wraps other blocks (a container) | `useInnerBlocksProps` in the editor, `save: () => <InnerBlocks.Content />`, and `render.php` that outputs `$content` |
| `interactive` | The block needs frontend behavior | Everything in `default`, plus an Interactivity API store in `view.ts` and directives in `render.php` |

Frontend behavior always goes through the Interactivity API. There's no classic `viewScript` variant.

## Block Structure

The scaffold generates the following structure:

```
blocks/my-block/
├── block.json    # Block metadata, the single source of truth
├── index.ts      # Block registration (index.tsx for the innerBlocks variant)
├── edit.tsx      # Editor component (named BlockEdit export)
├── icon.tsx      # Block icon (skipped when you enter a dashicon)
├── render.php    # Server-side rendering
├── style.css     # Styles for the frontend and the editor
└── view.ts       # Interactivity API store (interactive variant only)
```

There is no `save.js` and no `deprecated.js`. Every block is server-rendered.

## Block Registration

The `block.json` file defines your block's metadata:

```json
{
	"$schema": "https://schemas.wp.org/trunk/block.json",
	"apiVersion": 3,
	"name": "acme/my-block",
	"title": "My Block",
	"category": "text",
	"description": "A custom block",
	"example": {
		"attributes": {
			"content": "Example content."
		}
	},
	"attributes": {
		"content": {
			"type": "string",
			"role": "content",
			"default": ""
		}
	},
	"supports": {
		"html": false,
		"interactivity": {
			"clientNavigation": true
		}
	},
	"textdomain": "tenup-block-theme",
	"editorScript": "file:./index.ts",
	"style": "file:./style.css",
	"render": "file:./render.php"
}
```

- `example` gives the block a preview in the inserter. Every block should have one.
- `"html": false` stops editors from switching the block to HTML editing.
- `role: "content"` marks attributes that hold content, so they stay editable when the block is in a content-only (locked) context.

The build compiles `blocks/*` into `dist/blocks/*`, and the theme's `Blocks` module registers every `dist/blocks/*/block.json` automatically. You don't need to call `register_block_type()` yourself.

The `index.ts` file registers the editor side of the block. `save` is always inline: `() => null`, or `() => <InnerBlocks.Content />` for blocks with inner blocks.

```ts
import { registerBlockType, type BlockType } from '@wordpress/blocks';

import metadata from './block.json';
import { BlockEdit } from './edit';
import { icon } from './icon';

registerBlockType(metadata, {
	icon: { src: icon },
	// The editor component is typed with the block's own attributes.
	edit: BlockEdit as BlockType['edit'],
	save: () => null,
});
```

## Block Attributes

Attributes define the data stored by your block. Define them in `block.json`, and mirror them in the `Attributes` type in `edit.tsx`:

```json
{
	"attributes": {
		"title": {
			"type": "string",
			"role": "content",
			"default": ""
		}
	}
}
```

Prefer block supports (spacing, color, typography, alignment, layout) over custom attributes. Add custom attributes only for what supports can't express.

## Edit Component

The edit component defines how your block appears in the editor. Content is edited in place, in markup that matches `render.php`. Use the sidebar (`InspectorControls`) only for optional settings, never as the way to enter content.

```tsx
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import type { BlockEditProps } from '@wordpress/blocks';

type Attributes = {
	title: string;
};

/**
 * Editor component for the My Block block.
 *
 * @param props Block edit props.
 * @returns The block as it renders in the editor.
 */
export const BlockEdit = (props: BlockEditProps<Attributes>) => {
	const { attributes, setAttributes } = props;
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<RichText
				tagName="h2"
				className="wp-block-acme-my-block__title"
				placeholder={__('Add title…', 'tenup-block-theme')}
				value={attributes.title}
				onChange={(title) => setAttributes({ title })}
			/>
		</div>
	);
};
```

## Server-Side Rendering

The WP Scaffold renders every block on the server. The block's `save` returns `null`, and the markup lives in `render.php`. Only blocks with inner blocks save something: `<InnerBlocks.Content />`, so the inner blocks are stored in the post.

We use dynamic blocks instead of saving HTML to the database because it lets us change any block's markup later without deprecations or updating older instances of the block. Clients keep asking for changes, and dynamic blocks make those changes much easier to ship.

`render.php` receives `$attributes`, `$content`, and `$block`. Build the wrapper with `get_block_wrapper_attributes()` and escape everything else on output:

```php
<?php
/**
 * My Block block markup.
 *
 * @package Acme\Blocks\MyBlock
 *
 * @var array{title: string} $attributes Block attributes.
 * @var string   $content Block content.
 * @var WP_Block $block   Block instance.
 */

if ( empty( $attributes['title'] ) ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<h2 class="wp-block-acme-my-block__title">
		<?php echo wp_kses_post( $attributes['title'] ); ?>
	</h2>
</div>
```

## Frontend Interactivity

The `interactive` variant uses the [Interactivity API](https://developer.wordpress.org/block-editor/reference-guides/interactivity-api/). The store in `view.ts` is keyed by the block name, and `render.php` connects to it with directives:

```ts
import { store, getContext } from '@wordpress/interactivity';

type Context = {
	isOpen: boolean;
};

type Store = {
	state: Record<string, never>;
	actions: {
		toggle: () => void;
	};
	callbacks: Record<string, never>;
};

store<Store>('acme/my-block', {
	state: {},
	actions: {
		toggle() {
			const context = getContext<Context>();
			context.isOpen = !context.isOpen;
		},
	},
	callbacks: {},
});
```

```php
$wrapper_attributes = get_block_wrapper_attributes(
	[
		'data-wp-interactive' => 'acme/my-block',
		'data-wp-context'     => (string) wp_json_encode(
			[ 'isOpen' => false ],
			JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
		),
	]
);
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<button type="button" data-wp-on--click="actions.toggle" data-wp-bind--aria-expanded="context.isOpen">
		<?php esc_html_e( 'Toggle', 'tenup-block-theme' ); ?>
	</button>
	<p data-wp-bind--hidden="!context.isOpen">…</p>
</div>
```

`block.json` loads the store as a script module with `"viewScriptModule": "file:./view.ts"` and declares `"supports": { "interactivity": { "interactive": true, "clientNavigation": true } }`.

## Block Styles

Each block has a colocated `style.css`, loaded on the frontend and in the editor through the `style` key in `block.json`. Style the wrapper class (`.wp-block-acme-my-block`) and name inner elements BEM-style (`.wp-block-acme-my-block__title`).

Overrides for core blocks live in `assets/css/blocks/core/<block>.css`. They are built to `dist/blocks/autoenqueue/core/<block>.css` and only load when that block is on the page.

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

## Editor Defaults

The themes curate the editor so editors work within the design system:

- The block directory (third-party block suggestions) is removed (`Blocks` module).
- Core block patterns and remote patterns from the WordPress.org Pattern Directory are not loaded (`ThemeCore` module).
- The Openverse media category and the Font Library are disabled in the editor, and the Appearance > Fonts screen is removed (`Blocks` module).

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

The `innerBlocks` variant passes the block props through `useInnerBlocksProps` onto a single wrapper:

```tsx
import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const TEMPLATE = [['core/paragraph', { placeholder: __('Add content…', 'tenup-block-theme') }]];

export const BlockEdit = () => {
	const innerBlocksProps = useInnerBlocksProps(useBlockProps(), {
		template: TEMPLATE,
		// Deliberately unlocked: editors may add, move and remove inner blocks.
		templateLock: false,
	});

	return <div {...innerBlocksProps} />;
};
```

Restrict which blocks can be inserted with `allowedBlocks` in `block.json`. In `render.php`, output `$content` as is. It was sanitized when it was saved, and running it through `wp_kses_post()` again strips valid block markup:

```php
<div <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
```

### Block Controls

Add controls to your block. Toolbar controls (`BlockControls`) hold common actions; sidebar controls (`InspectorControls`) hold optional settings:

```tsx
import { useBlockProps, RichText, BlockControls, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, ToolbarButton } from '@wordpress/components';
import type { BlockEditProps } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { link } from '@wordpress/icons';

type Attributes = {
	title: string;
	isFeatured: boolean;
};

export const BlockEdit = (props: BlockEditProps<Attributes>) => {
	const { attributes, setAttributes } = props;
	const blockProps = useBlockProps();

	return (
		<>
			<BlockControls group="block">
				<ToolbarButton
					icon={link}
					label={__('Add link', 'tenup-block-theme')}
					onClick={() => {}}
				/>
			</BlockControls>
			<InspectorControls group="settings">
				<PanelBody title={__('Settings', 'tenup-block-theme')}>
					<ToggleControl
						label={__('Featured', 'tenup-block-theme')}
						checked={attributes.isFeatured}
						onChange={(isFeatured) => setAttributes({ isFeatured })}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				<RichText
					tagName="h2"
					value={attributes.title}
					onChange={(title) => setAttributes({ title })}
					placeholder={__('Add title…', 'tenup-block-theme')}
				/>
			</div>
		</>
	);
};
```

## Troubleshooting

### Common Issues

- **Block not appearing in the editor**: Check that `block.json` is valid and that the block was built to `dist/blocks/<block>/`.
- **Styles not applying**: Verify that your CSS files are being generated in the correct location.
- **Server-side rendering not working**: Check that `block.json` has `"render": "file:./render.php"` and that `render.php` was copied to `dist/blocks/<block>/`.
- **Interactivity not working**: Check that the store name in `view.ts` matches `data-wp-interactive` in `render.php`, and that `block.json` has `viewScriptModule` and `supports.interactivity`.

### Debugging Tips

- Use `console.log()` in your JavaScript files to debug issues.
- Check the browser console for errors.
- Examine the `Blocks.php` file to understand how blocks are registered and loaded.
- Use the WordPress block validator to identify issues.
