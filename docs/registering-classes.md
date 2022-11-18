# Registering Classes in the MU-Plugin

The MU-Plugin and the theme utilize a system to uniformly, auto-register classes that lie within their namespaces. Whilst there are a few constraints, it eases the requirements for engineers to add their classes to multiple locations each time they add one to the system.

To do this, it uses the [haydenpierce/class-finder](https://packagist.org/packages/haydenpierce/class-finder) package, which reads the `composer.json` file to help locate files that belong in certain namespaces.

## How do I define a class to be auto-registered?

All you need to do to get a class to auto-register is extend the `TenUpPlugin\Module::class` or `TenUpTheme\Module::class` classes. That will require you to implement a `can_register()` and a `register()` method.

### `can_register()`

The `can_register()` method is used to decide whether a class should be registered or not. Some examples of valid `can_register()` methods are:

#### Always Register

```php
public function can_register() {
    return true;
}
```

#### Register if in the admin area

```php
public function can_register() {
    return is_admin();
}
```

#### Register if a specific plugin is active

```php
public function can_register() {
    return plugin_active( 'plugin-directory/plugin-name.php' );
}
```

#### Register if running via WP-CLI

```php
public function can_register() {
    return defined( 'WP_CLI' ) && WP_CLI;
}
```

As you can hopfully see, it's easy enough to do everything we could previously with this approach.

### `register()`

The `register()` method is where you hook in to do your actual logic, E.G. adding `add_action()` or `add_filter()` calls. Your `register()` methods should look something like:

```php
public function register() {
    add_action( 'init', [ $this, 'register_post_types' ] );
}
```

One thing worth noting is that the `register()` method will be called at the priority of `8`. This means that you can use the priorty default of `10` or hook in earlier at `9` if you need to.

### Putting it all together

Below is a sample of a class that would be auto-registered when in the admin area, used to register some settings via FieldManager:

```php
namespace TenUpPlugin\Admin;

/**
 * Provide a Site Settings screen.
 */
class SiteSettings extends \TenUpPlugin\Module {

	/**
	 * Fieldmanager Setting ID
	 *
	 * @var string
	 */
	public $name = 'site_settings';

	/**
	 * Only register if on an admin page and if fieldmanager plugin is active.
	 *
	 * @return bool
	 */
	public function can_register() {
		return is_admin() && function_exists( 'fm_register_submenu_page' );
	}

	/**
	 * Register our hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', [ $this, 'register_site_settings' ] );
	}

	/**
	 * Creates a new Site Settings Screen.
	 *
	 * @return void
	 */
	public function register_site_settings() {
		// Register the submenu page.
		fm_register_submenu_page(
			$this->name,
			'options-general.php',
			__( 'Site Settings', 'tenup-plugin' )
		);

		// Load the fields.
		add_action(
			'fm_submenu_' . $this->name,
			[ $this, 'load' ]
		);
	}

	/**
	 * Configures the site settings.
	 *
	 * @return void
	 */
	public function load() {
		$config = [
			'name'     => $this->name,
			'children' => [
				// FM field config.
			],
		];

		$fm = new \Fieldmanager_Group( $config );
		$fm->activate_submenu_page();
	}
}

```

## How do I get an instance of my registered class?

The old way of doing this would be to use the `get_plugin_support()` function. As we no longer define and register our classes in the same way, this doesn't work.

The best way now, is to use the `get_module()` function that ships with the plugin and the theme.

```php
$site_settings = \TenUpPlugin\get_module( '\TenUpPlugin\Admin\SiteSettings' );
$a_theme_class = \TenUpTheme\get_module( '\TenUpTheme\Some\Theme\Class' );
```

If it can't find the class, it will return `false`.

One major difference between the old way and the new way is that when calling the `get_module()` function, you pass in the class name as a string containing the class name with its full namespace.

## I need to control the order that my classes load

By default classes will be loaded in the order they're found. It'd often required to load certain classes before another, for example, loading Taxonomies before Post Types.

To get around this, there is a `$load_order` property available on the `Module` abstract classes.

`$load_order` accepts an integer and lets us choose which classes will load first. It has no correlation to the `init` priority to a class, but works in the same way, the lower numbers will load first.

To see it in action, see below.

```php
namespace TenUpPlugin\Admin;

/**
 * Taxonomy Factory
 */
class TaxonomyFactory extends \TenUpPlugin\Module {

  public $load_order = 9;

  // Rest of class removed for brevity.
}
```

```php
namespace TenUpPlugin\Admin;

/**
 * Post Type Factory
 */
class PostTypeFactory extends \TenUpPlugin\Module {

  // Rest of class removed for brevity.
}
```

We've defined two classes, one using the default load order (`10`) and another with a custom load order (`9`).

Because of this, the `TaxonomyFactory` class will always be loaded before the `PostTypeFactory` class.


## Known Issues

### Could not locate `composer.json`

During deployment, we must deploy the `composer.json` file. This is how the class finder works, so if it doesn't exist you'll get an exception that states:

```
Could not locate composer.json. You can get around this by setting ClassFinder::$appRoot manually.
```

More information on this issue is available [here](https://gitlab.com/hpierce1102/ClassFinder/-/blob/master/docs/exceptions/missingComposerConfig.md).
