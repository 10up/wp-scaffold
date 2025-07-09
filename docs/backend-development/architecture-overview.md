# Architecture Overview

This document provides a high-level overview of the `wp-content` directory structure and its key components within this WP Scaffold project.

## Table of Contents
- [Directory Structure](#directory-structure)
- [Key Components](#key-components)
- [Customization Points](#customization-points)

## Directory Structure
- `wp-content/` – Main development folder for your WordPress site
  - `themes/` – Custom themes for frontend presentation
  - `mu-plugins/` – Must-use plugins, auto-loaded by WordPress
  - `plugins/` – Standard plugins (optional features and integrations)
  - `docs/` – Project documentation for developers
  - `phpstan/` – PHPStan static analysis configuration
  - `uploads/` – Media uploads (images, files, etc.)
  - `vendor/` – Composer-managed PHP dependencies
  - `package.json`, `composer.json` – Project-level dependency management

## Key Components
- **Themes:** Custom themes for your site’s frontend. Place new themes in the `themes/` directory.
- **MU-Plugins:** Core functionality that should always be loaded. Place these in `mu-plugins/`.
- **Plugins:** Optional features and integrations. Place these in `plugins/`.
- **Docs:** All developer documentation lives in `docs/`.
- **phpstan/** and `phpstan.neon`: Static analysis configuration for PHP code quality.

## Customization Points
- Add or modify themes in `themes/`.
- Add always-on functionality in `mu-plugins/`.
- Add optional plugins in `plugins/`.
- Update or add documentation in `docs/`.
- Adjust static analysis in `phpstan/` and `phpstan.neon`.

For more details, see the specific guides in this documentation directory.
