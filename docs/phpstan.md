# PHPStan

PHPStan is a Static Analysis tool for PHP. In short, it will analyze your code and flag any potential issues.



## How do I run PHPStan?

PHPStan automatically runs on commit via a lint-staged hook (see `.lintstagedrc.json`). It can also be run on the entire codebase by running `composer run static`.



## How do I set up PHPStan in a new wp-scaffold project?

You need to do a couple of things when setting up PHPStan on a new project.

Firstly, in `phpstan/constants.php` you need to update the names to match the convention used within your project. These constants are, by default, defined in the following locations:

* [/mu-plugins/10up-plugin/plugin.php](https://github.com/10up/wp-scaffold/blob/trunk/mu-plugins/10up-plugin/plugin.php)
* [/themes/10up-theme/functions.php](https://github.com/10up/wp-scaffold/blob/trunk/themes/10up-theme/functions.php)

If you have any other custom constants defined, you'll also need to define them here.

Once that's done, you can open `phpstan.neon` in your editor. This is where you'll tell PHPStan which files to analyze. You'll most likely need to update the paths/names on the ones in there. But, if you have additional plugins or themes in your repo, you'll also want to add the paths to those.

Once that's done, check that everything is working by running `composer run static`



## I'm getting a bunch of PHPStan errors; what should I do?

By default, all PHPStan errors have been fixed within the WP-Scaffold repo. Therefore, hopefully, these errors are coming from your code. I

PHPStan errors are pretty good at letting you know what the issue is. My initial suggestion would be to look through the errors and try and fix the problems. This will ultimately lead to a cleaner codebase in the long run.

If you're stuck, here are some helpful debugging tips:

* Check which file is causing the error.
	* Is it a file that should be analyzed, or is it some third-party code you can't control? If so, see "Third-party code is causing me errors" below.
* Look at the error that's being thrown.
	* Are your PHPDoc types matching the Parameter types?

If you're spinning your wheels on an error, reach out to your Director of Engineering or the engineering channel for help.



## Third-party code is causing me errors.

If you have some third-party code within your plugins/themes that you don't want to fix but are causing errors, don't fret. A minor adjustment to the `phpstan.neon` file will allow us to disable analysis on the file without causing errors elsewhere.

We have a choice of how we want to handle the errors. We can:

1. Disable scanning and analysis on the files:

	* This means that the files won't be scanned at all by PHPStan.
	* Useful for test directories etc.
	* This means that PHPStan will throw an error if you call a function defined within one of those files.

2. Allow scanning but disable analysis on the files:

	* This means PHPStan will scan the files but won't raise any errors inside them.

	* Useful for third-party code that you need to call within your code.

### Disable Scanning and Analysis

To disable scanning and analysis, you'll want to update your `phpstan.neon` to look like the below:

```
includes:
	- phpstan/default.neon

parameters:
	paths:
		- themes/10up-theme
		- mu-plugins/10up-plugin
		- mu-plugins/10up-plugin-loader.php
  excludePaths:   <--- Added this line and below
	    analyseAndScan:
		    - path/to/files/to/ignore  <--- Path to the files to ignore

```

### Allow Scanning but Disable Analysis

To allow scanning but disable analysis, you'll want to update your `phpstan.neon` to look like the below:

```
includes:
	- phpstan/default.neon

parameters:
	paths:
		- themes/10up-theme
		- mu-plugins/10up-plugin
		- mu-plugins/10up-plugin-loader.php
  excludePaths:   <--- Added this line and below
	    analyse:
		    - path/to/files/to/ignore  <--- Path to the files to ignore
```

**Note:** The config parser uses [`fnmatch`](https://www.php.net/manual/en/function.fnmatch.php) under the hood, so you can pass in any patterns it supports, such as wildcards. E.G. `- path/*/tests`



## I'm running an older version of wp-scaffold and want to use PHPStan.

Nothing stops you from using PHPStan with an older version of the WP-Scaffold or any other project structure. To get PHPStan installed, you'll want to run the following from the repo root:

```shell
composer require --dev phpstan/extension-installer szepeviktor/phpstan-wordpress
```

This will install PHPStan, the WordPress extension and the extension installer for you.

Next, you'll want to copy the entire `phpstan` directory from the `WP-Scaffold` repo and drop it into your repo root.

Finally, copy over the `phpstan.neon` file from the  `WP-Scaffold` repo and drop it into your repo root.

At this point, PHPStan is essentially set up and you can follow the instructions in "How do I set up PHPStan in a new wp-scaffold project?".

The one issue you may run into with PHPStan on older projects is a lot of initial errors. There are a couple of ways around this, but be sure to discuss the best approach with your Director of Engineering.

### Lower the PHPStan level

We can drop the PHPStan level, which will decrease its strictness. Essentially, it will only flag more severe errors.

When introducing PHPStan to an existing codebase, the usual workflow is to get the number of errors reported on level 0 to zero and merge that into the main branch.

When the developers feel like it, they can try raising the level by one, reviewing the list of errors, and fixing all of them.

You would then work through the levels until you get to a position you're happy with (the scaffold is currently running on level 6).

The level can be set in your `phpstan.neon` file like below. See the PHPStan documentation on [Rule Levels](https://phpstan.org/user-guide/rule-levels) for more info on what each one looks for.

```
includes:
	- phpstan/default.neon

parameters:
  level: 6   <--- Add the level of the path here.
	paths:
		- themes/10up-theme
		- mu-plugins/10up-plugin
		- mu-plugins/10up-plugin-loader.php
```



### Create a baseline

PHPStan also allows you to create a "baseline". This is essentially a list of errors you don't want to report on subsequent runs. It will enable you to jump in directly at the level you want to work at for new code whilst silencing errors from legacy code whilst you clean it up.

The baseline can be created by running the following:

```shell
composer run static -- --generate-baseline
```

The baseline will be saved to `phpstan-baseline.neon`. This should then be imported into your `phpstan.neon` file, as below:

```
includes:
	- phpstan/default.neon
	- phpstan-baseline.neon  <--- Added this line

parameters:
	paths:
		- themes/10up-theme
		- mu-plugins/10up-plugin
		- mu-plugins/10up-plugin-loader.php
```

The changes should be committed to the repo.

You can create a new baseline file as you clean up old errors. Eventually, you should get to 0 errors and be able to delete the baseline file.
