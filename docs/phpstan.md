# PHPStan in WordPress Projects

PHPStan is a static analysis tool for PHP that helps you catch bugs and improve code quality without even running your code. This guide will walk you through setting up and using PHPStan in your WordPress project, specifically tailored to the 10up Scaffold.

## Getting Started

PHPStan is already installed within the scaffold, so you only need to configure it for your new project.

## Configuring PHPStan

The configuration file for PHPStan is named `phpstan.neon`. It contains all the settings PHPStan will use when analyzing your code. Below is an example configuration file already included in the scaffold:

```neon
includes:
	- phpstan/default.neon

parameters:
	paths:
		- themes/10up-theme
		- mu-plugins/10up-plugin
		- mu-plugins/10up-plugin-loader.php
```

- **`paths`**: Directories or files to analyze.

## Running PHPStan

Once configured, you can run PHPStan with:

```bash
composer run static
```

This command will scan the files defined in your `phpstan.neon` file and output any issues it finds.

## Dealing with PHPStan Errors

When integrating PHPStan into an existing codebase, you may encounter a large number of errors. The scaffold is set up with a PHPStan level of 9, the highest in V1 of PHPStan. Here are two effective strategies to manage them:

### Lower the PHPStan Level

To start small, you can reduce the PHPStan level. This decreases the strictness and flags only the most severe issues. Here’s how to lower the level:

```neon
includes:
	- phpstan/default.neon

parameters:
	level: 4   <--- Add the level here.
	paths:
		- themes/10up-theme
		- mu-plugins/10up-plugin
		- mu-plugins/10up-plugin-loader.php
```

The recommended workflow is to start at level 4, fix all issues, and then gradually increase the level, addressing errors as you go.

See more about PHPStan levels here: [Levels of PHPStan](https://phpstan.org/user-guide/rule-levels).

### Create a Baseline

PHPStan allows you to create a "baseline" file to temporarily ignore existing errors. This is especially useful for legacy projects. You can generate a baseline with:

```bash
composer run static -- --generate-baseline
```

This creates a `phpstan-baseline.neon` file. Add it to your `phpstan.neon` configuration like this:

```neon
includes:
	- phpstan/default.neon
	- phpstan-baseline.neon
```

Commit the baseline file to your repository. Over time, as you address the issues in your codebase, you can update or remove the baseline.

## Analyzing Third-Party Code

When working with third-party code (e.g., vendor packages or dependencies), PHPStan may report errors in files you don’t control. To exclude these from analysis, use the `excludePaths` and `analyseAndScan` parameters in your configuration:

```neon
parameters:
	excludePaths:
		analyseAndScan:
			- path/to/files/to/ignore
			- path/to/more/files/to/ignore
```

This ensures PHPStan focuses only on your custom code.

If you find that PHPStan needs to know about functions in third-party code, but you don't want it to analyse it, you can use the use the `excludePaths` and `analyse` parameters in your configuration:

```neon
parameters:
	excludePaths:
		analyse:
			- path/to/files/to/not/analyse
			- path/to/more/files/to/not/analyse
```

## PHPStan in VIP Projects

When running PHPStan on a VIP based project, you'll want to make sure that PHPStan can scan the `mu-plugins` directory. Without this, you'll get errors regarding missing VIP functions.

To scan the `mu-plugins` directory, you'll want to update your `phpstan.neon` to add `mu-plugins` to the `excludes_analyse` parameter:

```neon
parameters:
	scanDirectories:
		- mu-plugins
```

If you decide to run PHPStan on CircleCI, you're then likely see an error with it being unable to scan the `mu-plugins` directory.
That's because the `mu-plugins` directory is git ignored and therefore doesn't exist in the CircleCI environment.

To get around this, we can clone the MU plugins repo before we run PHPStan and then remove it again afterwards. This looks something like:

```yaml
version: 2
jobs:
  test:
    docker:
      - image: cimg/php:8.3-node
    steps:
      - checkout
      - run:
          name: Composer install
          command: composer run setup
      - run:
          name: NPM install
          command: npm install

      # Your other build steps ...

      - run:
          name: Install VIP MU-Plugins
          command: git clone git@github.com:Automattic/vip-go-mu-plugins.git --recursive mu-plugins/

      - run:
          name: Analyse PHP
          command: composer run static

      - run:
          name: Remove VIP MU-Plugins
          command: rm -rf mu-plugins/
# The rest of your CircleCI config ...
```

With this in place, CircleCI should be able to run PHPStan properly again.
