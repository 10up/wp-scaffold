# 10up Theme

## Working with `theme.json`
The default theme scaffold now ships with a very basic version of the `theme.json` file. This is to ensure all the side-affects of introducing this file are there from the beginning of a project and therefore set projects up for success if they want to adopt more features through the `theme.json` mechanism.

### Basics of `theme.json`
The `theme.json` file allows you to take control of your blocks in both the editor and the frontend. The file is structured in a `settings` and a `styles` section where you can define options on a global level and then override them / adjust them on a block level.

The values that you provide in the `theme.json` file will be added both on the frontend and in the editor as [CSS custom properties following a fixed naming scheme](https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-json/#css-custom-properties-presets-custom).

### 🙋 FAQ
<details>
<summary>Where has the `.wp-block-group__inner-container` gone?</summary>
<br />

Core has made the decision to drop the additional inner container of the group block. The rationale behind that decision is that the additional `div` semantically isn't necessary and modern layout techniques don't rely on it anymore. The container is still present for _legacy_ themes (themes without a `theme.json` file).

For new builds it is suggested that we use the `settings.layout.contentWidth` and `settings.layout.wideWidth` options of the `theme.json` for this. The group block has an option in the editor to allow editors to inherit the width for its inner elements.

<img width="1904" alt="Screen Shot 2021-10-20 at 12 45 15" src="https://user-images.githubusercontent.com/20684594/138079160-44a28c10-417b-4769-905d-cd5c104e78c0.png">

```json
{
    "version": 1,
    "settings": {
        "layout": {
            "contentSize": "800px",
            "wideSize": "900px"
        }
    }
}
```

For this, there isn't even any custom CSS needed.

There isn't the best story for responsive overrides in here but the recommendation at this point in time would be using `clamp` as we have officially dropped the IE11 support and that would allow us to have a fluid with scale here for the elements.
[https://caniuse.com/css-math-functions](https://caniuse.com/css-math-functions)


If we need to use different content widths here we can stick to the core way and apply the `max-width` settings to the children of the group block instead of the wrapper element.

```css
.wp-block-group > * {
    max-width: var(--site-max-width);
}
```

If there are instances where we really cannot get by with styling the child blocks directly there is a hook in PHP that allows us to filter the block editor settings and therefore allows us to override the underlying `supportsLayout` property:

```php
add_filter(
	'block_editor_settings_all',
	'remove_layout_support_from_editor_settings'
);

/**
 * This function sets the `supportsLayout` option in the editor settings to false
 * Therefore it adds back the `wp-block-group__inner-container` element
 *
 * As a side effect of this change the `contentWidth` and `wideWidth` defined in the theme.json
 * no longer have any effect and all the blocks in the editor won't have any width restrictions
 * applied to them. So that needs to do be manually done by the theme.
 *
 * @param array $settings block editor settings
 */
function remove_layout_support_from_editor_settings( $settings ) {
	$settings['supportsLayout'] = false;
	return $settings;
}
```
</details>

<details>
<summary>Where can I find documentation for `theme.json`</summary>

### Core Handbook
You can find the Core Documentation here: [https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-json/](https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-json/). This should give you an overview of the options that are available and be a starting point for you to explore. In the Code examples you will get ones for `WordPress` and ones for `Gutenberg`. The ones for WordPress always are for the version in Core and therefore what we would want to look at.

### Code completion and validation
Additionally you can add inline documentation & code completion to your editor by adding the `JSON Schema` to your editor.

For VSCode you can add the following to your Settings. But other editors also support this and you can find more information on the topic here: [https://json.schemastore.org](https://json.schemastore.org)
```json
{
	"json.schemas": [
		{
			"fileMatch": [
				"/theme.json"
			],
			"url": "https://json.schemastore.org/theme-v1.json"
		}
	],
}
```

</details>

<sub>* for 10uppers, reach out to Fabian for any questions / guidance / support in regards to `theme.json`</sub>

# Performance Utilities
The theme now supports `ct.css`. Uh what?
`ct.css` is a diagnostic stylesheet that exposes potential performance issues in your pages `<head>` element. `ct.css` will return color-coded visual cues with regards to render blocking elements in the theme. This provides a great way for engineers to debug and identify problem resources.

You can activate `ct.css` on any page load by including `?debug_perf=1` in the URL.

Considering we do not want to load script everywhere throughout the theme, we have provided engineeers with a way to trigger the `ct.css` output by using a query param.

<sub>* for 10uppers, reach out to Daine for any questions / guidance / support in regards to `ct.css`</sub>
