# Architecture

This document describes the high-level architecture of the TenUpPlugin.
If you want to familiarize yourself with the code base, you are just in the right place!

## Bird's Eye View

![](https://raw.githubusercontent.com/10up/wp-scaffold/d22480a6319bb85c639d49552679ab818937ce57/mu-plugins/tenup-plugin/docs/images/birds_eye_view_v1.png)

## Code Map

### `includes\TenUpPlugin`

The Plugin's PHP Code in the `TenUpPlugin` namespace. Modules are further organized into separate directories and namespaces like `TenUpPlugin\PostType` & `TenUpPlugin\Admin`.

### `tests\TenUpPlugin`

The PHP classes have corresponding Test files in the `tests\TenUp\Plugin` directory.

### `assets` & `dist`

The Javascript & CSS assets are placed in subdirectories under the `assets` directory. These assets are compiled using the `10up-scripts` package into the `dist` directory.

### `bin`

Any shell scripts used by the plugin are placed in the `bin` directory.

### `docs`

Plugin documentation including architecture & docs for hooks and filters is placed in the `docs` directory.

## Testing

The Plugin uses PHPUnit with Composer to test the PHP code. The Tests extend WordPress PHPUnit Test Case and must be installed via the bundled installer.

```bash
cd mu-plugins/tenup-plugin
bin/install-wp-tests.sh {test-database} {username} {password} {db_host} latest true
vendor/bin/phpunit --colors=always'
```

After the initial test installation you can speed up the test suite by specifying the `WP_TESTS_SKIP_INSTALL` flag.

```bash
 WP_TESTS_SKIP_INSTALL=1 vendor/bin/phpunit --colors=always
```
