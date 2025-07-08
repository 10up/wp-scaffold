# Creating Post Types and Taxonomies in the MU-Plugin

The MU plugin contains abstract classes that can be extended to easily register new post types and taxonomies. This document will explain how to use these classes to create new post types and taxonomies.

If you want to jump right in, take a look at the `TenUpPlugin\PostTypes\Demo::class` and `TenUpPlugin\Taxonomies\Demo::class` classes.

## Post Types

To create a new post type, you will need to create a new class that extends the `TenupFramework\PostTypes\AbstractPostType` class. This class will contain the configuration for the new post type.

Once you've extended the class (or copied the `Demo` class), you will need to define the following methods:

- `get_name()` - This should return the name of the post type as it will be used within the WordPress database.
- `get_singular_label()` - This should return the singular label for the post type.
- `get_plural_label()` - This should return the plural label for the post type.
- `get_menu_icon()` - This should return the dashicon to use for the post type in the WordPress admin menu, it can also return a base64 encoded SVG or the string `'none'`.
- `can_register()` - Whether the post type should be actively registered or not. See [Registering Classes](./registering-classes.md).

Once those are defined, ensure that `can_register()` is returning `true` and you should be able to see your post type within the admin.

### Hierarchical Post Types

Should you need to create a hierarchical post type, you can override the `is_hierarchical()` method and return `true`.

```php
/**
 * Is the post type hierarchical?
 *
 * @return bool
 */
public function is_hierarchical() {
	return true;
}
```

### Changing the Post Supports

There are a few options that can exist within [post supports](https://developer.wordpress.org/reference/functions/register_post_type/#parameters:~:text=handling.%0ADefault%20false.-,supports,-array), the scaffold aims to set sensible defaults but sometimes they will need changing. There are two ways to do this:

a. Override the `get_editor_supports()` method and return an array of the supports you want to enable.

```php
/**
 * Default post type supported feature names.
 *
 * @return array
 */
public function get_editor_supports() {
	$supports = [
		'title',
		'editor',
		'thumbnail',
		'custom-fields',
	];

	return $supports;
}
```

b. Merge your additional supports into the base version

```php
/**
 * Default post type supported feature names.
 *
 * @return array
 */
public function get_editor_supports() {
	$supports   = parent::get_editor_supports();
	$supports[] = 'custom-fields';

	return $supports;
}
```

### Changing Post Type Options

Much like with the Supports, the scaffold aims to set sensible defaults for the post type options. Should you want to change these, you can handle it in much the same way:

a. Override the `get_options()` method and return an array of the options you want to enable.

```php
/**
 * Default post type supported feature names.
 *
 * @return array
 */
public function get_options() {
	$options = [
			'labels'            => $this->get_labels(),
			'public'            => false,
			'has_archive'       => false,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => false,
			'show_in_rest'      => true,
			'supports'          => $this->get_editor_supports(),
			'menu_icon'         => $this->get_menu_icon(),
			'menu_position'     => $this->get_menu_position(),
			'hierarchical'      => $this->is_hierarchical(),
		];

	return $options;
}
```

b. Merge your additional options into the base version

```php
/**
 * Default post type supported feature names.
 *
 * @return array
 */
public function get_options() {
	$options                = parent::get_options();
	$options['public']      = false;
	$options['has_archive'] = false;

	return $options;
}
```

### Adding Taxonomies to a Post Type

To add a taxonomy to a post type, you'll need to override the `get_supported_taxonomies()` method on the post type class and return an array of taxonomies you'd like to support.

E.G.

```php
/**
 * Returns the default supported taxonomies. The subclass should declare the
 * Taxonomies that it supports here if required.
 *
 * @return array
 */
public function get_supported_taxonomies() {
	return [
		'category',
		'post_tag',
	];
}
```

### Registering Post Meta

Whilst there is no official way to register post meta, the scaffold provides an `after_register()` method that's called after the post type is registered.

A useful pattern is:

```php
/**
 * Run any code after the post type has been registered.
 *
 * @return void
 */
public function after_register() {
	register_post_meta(
		$this->get_name(),
		'my_test_meta_key',
		[
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		]
	);
}
```

The `after_register()` method can also be useful to hook into anything else you may need to do, directly related to the registration of the post type.

## Taxonomies

To create a new taxonomy, you will need to create a new class that extends the `TenupFramework\Taxonomies\AbstractTaxonomy` class. This class will contain the configuration for the new taxonomy.

Once you've extended the class (or copied the `Demo` class), you will need to define the following methods:

- `get_name()` - This should return the name of the taxonomy as it will be used within the WordPress database.
- `get_singular_label()` - This should return the singular label for the taxonomy.
- `get_plural_label()` - This should return the plural label for the taxonomy.
- `can_register()` - Whether the taxonomy should be actively registered or not. See [Registering Classes](./registering-classes.md).

Once those are defined, ensure that `can_register()` is returning `true` and that you've [registered the taxonomy with a post type](#adding-taxonomies-to-a-post-type), then you should be able to see your taxonomy within the admin.

### Hierarchical Taxonomies

Should you need to create a hierarchical taxonomy, you can override the `is_hierarchical()` method and return `true`.

```php
/**
 * Is the taxonomy hierarchical?
 *
 * @return bool
 */
public function is_hierarchical() {
	return true;
}
```

### Changing the Taxonomy Options

The scaffold aims to set sensible defaults for the taxonomy options. Should you want to change these, you can handle it in much the same way as post types:

a. Override the `get_options()` method and return an array of the options you want to enable.

```php
/**
 * Get the options for the taxonomy.
 *
 * @return array
 */
public function get_options() {
	$options = [
		'labels'            => $this->get_labels(),
		'hierarchical'      => $this->is_hierarchical(),
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_rest'      => true,
		'public'            => true,
	];

	return $options;
}
```

b. Merge your additional options into the base version

```php
/**
 * Get the options for the taxonomy.
 *
 * @return array
 */
public function get_options() {
	$options                = parent::get_options();
	$options['public']      = false;

	return $options;
}
```

### Registering Taxonomy Meta

Whilst there is no official way to register taxonomy meta, the scaffold provides an `after_register()` method that's called after the taxonomy is registered.

A useful pattern is:

```php
/**
 * Run any code after the taxonomy has been registered.
 *
 * @return void
 */
public function after_register() {
	register_term_meta(
		$this->get_name(),
		'my_test_meta_key',
		[
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
		]
	);
}
```

## Further Information

I encourage you to look at the abstract post classes, whilst they aim to provide a sensible set of defaults, everything within them can be overridden, should it need to be.

If you find yourself making the same changes across multiple classes or projects, please consider [opening a ticket](https://github.com/10up/wp-scaffold/issues/new?assignees=&labels=type%3Aenhancement&projects=&template=2-enhancement.yml) which describes those changes.
That way we can look at updating the scaffold to help others in the future.
