# WP-Framework Structure

This document outlines the structure, purpose, and usage of the WP-Framework within a WP Scaffold project.

## Table of Contents
- [Introduction](#introduction)
- [Purpose and Benefits](#purpose-and-benefits)
- [Framework Components](#framework-components)
- [Versioning](#versioning)
- [Integration with WP Scaffold](#integration-with-wordpress-scaffold)
- [Extending the Framework](#extending-the-framework)
- [Best Practices](#best-practices)
- [Troubleshooting](#troubleshooting)

## Introduction

WP-Framework is a Composer package that extracts the most reusable parts of the WP Scaffold into a standalone, versioned dependency. Rather than being a component within the scaffold, it serves as the foundation that the scaffold builds upon, providing a centralized home for shared logic that was previously duplicated across projects.

The framework follows modern PHP practices and is designed to work seamlessly with WordPress while providing additional structure and organization that the core WordPress codebase doesn't inherently provide. By moving this shared functionality into a separate package, the scaffold becomes leaner and more maintainable, while ensuring consistency across projects.

## Purpose and Benefits

### Core Purpose

The WP-Framework was created to address specific challenges in maintaining and updating the WP Scaffold:

1. **Centralize Shared Logic**: Provide a single source of truth for functionality that was previously duplicated across projects
2. **Simplify Maintenance**: Make it easier to maintain and improve shared code without requiring manual updates to multiple projects
3. **Ensure Consistency**: Standardize approaches to common WordPress development tasks across all projects
4. **Enable Versioned Updates**: Allow projects to receive improvements through controlled, versioned updates
5. **Reduce Technical Debt**: Prevent divergence of implementations across different projects over time

### Key Benefits

Using the WP-Framework in your WP Scaffold project offers several advantages:

- **Easier Updates**: Improvements to shared functionality can be rolled out across multiple projects via Composer updates
- **Reduced Boilerplate**: Common WordPress patterns are abstracted into reusable components
- **Leaner Scaffold**: The scaffold itself becomes more focused and maintainable
- **Improved Architecture**: Clear separation of concerns and dependency management
- **Consistent Structure**: Standardized approach helps team members move between projects more easily
- **Modular Design**: The 90% foundation that most projects need, while still allowing customization where necessary
- **Stable Foundation**: Strict semantic versioning ensures updates won't unexpectedly break existing projects

## Framework Components

The WP-Framework extracts the most reusable parts of the WP Scaffold into a centralized package. These components represent the shared functionality that many WordPress projects need:

### Module Loader

The module loader is a core part of the framework that makes it easy to register and configure classes:

- **Class Registration**: Simplified registration of classes with WordPress hooks
- **Dependency Management**: Handles dependencies between components
- **Initialization**: Controls the order and timing of component initialization

### Abstract Classes and Base Implementations

The framework provides abstract classes and base implementations for common WordPress patterns:

- **Post Type Registration**: Base classes for creating custom post types
- **Taxonomy Registration**: Base classes for creating custom taxonomies
- **Admin Pages**: Base implementations for admin interfaces
- **Meta Boxes**: Reusable patterns for meta box creation
- **REST API Endpoints**: Base classes for creating REST API endpoints
- **Asset Management**: Traits like `GetAssetInfo` for simplified asset handling

## Versioning

The WP-Framework follows strict [Semantic Versioning (SemVer)](https://semver.org/) to ensure stability and backwards compatibility. This strict versioning approach is a cornerstone of the framework's design, providing confidence that updates won't unexpectedly break existing projects.

### Version Format

Versions follow the format: `MAJOR.MINOR.PATCH`

- **MAJOR (X.0.0)**: Incremented for incompatible API changes that may require code modifications
- **MINOR (x.Y.0)**: Incremented for backward-compatible functionality additions
- **PATCH (x.y.Z)**: Incremented for backward-compatible bug fixes and minor improvements

### Compatibility Guarantees

- **PATCH Updates**: Always safe to apply, contain only bug fixes and minor improvements
- **MINOR Updates**: Safe to apply, add new features without breaking existing code
- **MAJOR Updates**: May include breaking changes, but these will be clearly communicated in advance

This versioning approach ensures that projects won't suddenly break due to a framework update. You can confidently upgrade within the same major version, knowing there will be no unexpected changes.

### Deprecation Policy

Before removing or significantly changing functionality:

1. The feature is marked as deprecated in a minor release
2. Deprecation notices are added to the documentation and codebase
3. The feature continues to work for at least one major version cycle
4. The feature is removed only in a major version update

This policy gives developers ample time to adapt their code to any upcoming changes.

## Integration with WP Scaffold

The WP-Framework was introduced to the WP Scaffold in PR #268 as a significant architectural improvement. Rather than being embedded within the scaffold, it's now included as a Composer dependency.

### Changes to the Scaffold

The WP Scaffold has been updated to:

1. **Require WP-Framework**: The framework is now included as a Composer dependency
2. **Remove Duplicated Code**: Much of the shared functionality has been removed from the scaffold and is now provided by the framework
3. **Leverage Framework Components**: The scaffold now builds upon the foundation provided by the framework

This change makes the scaffold lighter and more focused while ensuring projects can still benefit from improvements made over time.

### Installation and Updates

For new projects using the scaffold, the WP-Framework is automatically included via Composer:

```json
{
  "require": {
    "10up/wp-framework": "^1.0"
  }
}
```

For existing projects, updating to use the framework is completely optional. If updating would cause undue delays or waste engineering time, it's perfectly fine to leave things as they are. However, if you're planning a significant update or refactor, it might be worth considering the new approach to take advantage of future improvements.

### Usage in Scaffold Components

Scaffold components can use the framework by:

1. Extending framework base classes
2. Using framework utilities and helpers

## Best Practices

The WP-Framework is designed to provide a solid 90% foundation for most WordPress projects while still allowing flexibility for project-specific needs. When working with the framework, consider these best practices:

### For New Projects

- **Embrace the Framework**: Take advantage of the framework's components and patterns
- **Follow Versioning**: Use semantic versioning in your `composer.json` to control updates
- **Extend, Don't Modify**: Extend framework classes rather than modifying core framework files
- **Leverage Composer**: Use Composer for dependency management and updates
- **Stay Current**: Regularly update to the latest minor and patch versions for improvements and bug fixes

### For Existing Projects

- **Optional Migration**: Updating to use the framework is completely optional
- **Consider ROI**: Evaluate whether migrating to the framework makes sense for your project timeline and resources
- **Incremental Adoption**: Consider adopting the framework during significant updates or refactors
- **Maintain Compatibility**: If updating, ensure your custom code remains compatible with the framework

### General Guidelines

- **Customize Where Needed**: The framework provides a 90% foundation, but you can still customize the remaining 10%
- **Respect Versioning**: Pay attention to major version changes which may contain breaking changes
- **Contribute Improvements**: If you develop enhancements that could benefit others, consider contributing them back
- **Document Extensions**: Document any custom extensions or modifications to the framework

## Troubleshooting

Here are solutions to common issues you might encounter when working with the WP-Framework:

### Composer Dependency Issues

If you're having trouble with the framework as a Composer dependency:

1. **Version Constraints**: Ensure your `composer.json` has the correct version constraint (e.g., `"10up/wp-framework": "^1.0"`)
2. **Composer Cache**: Try clearing the Composer cache with `composer clear-cache`
3. **Composer Update**: Run `composer update` to ensure you have the latest compatible version
4. **Composer Diagnostics**: Use `composer why 10up/wp-framework` to see why the package is being included

### Migration Challenges

When migrating an existing project to use the framework:

1. **Namespace Conflicts**: Check for namespace conflicts between your code and the framework
2. **Duplicate Functionality**: Identify and remove code that duplicates framework functionality
3. **Dependency Injection**: Adapt your code to use the framework's dependency injection patterns
4. **Incremental Approach**: Consider migrating one component at a time rather than all at once

### Version Compatibility

If you encounter version compatibility issues:

1. **Semver Understanding**: Remember that only major version changes (e.g., 1.x to 2.x) might contain breaking changes
2. **Changelog Review**: Always review the changelog before upgrading to a new major version
3. **Deprecation Notices**: Pay attention to deprecation notices in your logs to prepare for future changes
4. **Testing Environment**: Test framework updates in a development environment before applying to production

### Custom Extensions

If you're extending the framework and encountering issues:

1. **Extension Points**: Make sure you're using the intended extension points rather than modifying core files
2. **Hook Priority**: Check that your hooks have the correct priority if they're not executing as expected
3. **Inheritance**: When extending framework classes, ensure you're calling parent methods when required
4. **Documentation**: Consult the framework documentation for the recommended approach to customization

---

For more detailed information about specific framework components, refer to the corresponding documentation files in this directory.
