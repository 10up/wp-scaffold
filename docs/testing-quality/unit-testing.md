# Unit Testing

This document provides guidance on adding unit testing to your WP Scaffold project.

## Introduction

The WP Scaffold does not include unit testing setup out of the box. This document provides recommendations for implementing unit testing in your project if needed.

## Recommended Approach

For WordPress projects, we recommend using PHPUnit for PHP unit testing. PHPUnit is a programmer-oriented testing framework for PHP that provides an easy way to write and run tests.

### Adding PHPUnit to Your Project

To add PHPUnit to your project:

1. Install PHPUnit and related tools as development dependencies:

```bash
composer require --dev phpunit/phpunit yoast/phpunit-polyfills brain/monkey
```

2. Create a basic PHPUnit configuration file:

```bash
vendor/bin/phpunit --generate-configuration
```

3. Adjust the generated phpunit.xml file to match your project structure.

## Recommended Resources

For more information on unit testing best practices for WordPress projects, refer to:

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [10up Engineering Best Practices - Testing](https://10up.github.io/Engineering-Best-Practices/tools/#testing)
- [Brain\Monkey Documentation](https://brain-wp.github.io/BrainMonkey/) (for mocking WordPress functions)

## Basic Example

Here's a simple example of a PHPUnit test for WordPress:

```php
<?php
// tests/unit/ExampleTest.php
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase {
    public function test_example() {
        $this->assertTrue(true);
    }

    public function test_string_contains() {
        $string = 'WordPress is awesome';
        $this->assertStringContainsString('WordPress', $string);
    }
}
```

## WordPress-Specific Testing

For WordPress-specific testing, you'll need to set up the WordPress testing environment:

```bash
# Install the WordPress test suite
wp scaffold plugin-tests your-plugin
cd wp-content/plugins/your-plugin
bash bin/install-wp-tests.sh wordpress_test root '' localhost latest
```

This is just a starting point. You can expand your unit testing suite based on your project's specific needs.
