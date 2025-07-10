# WP Scaffold Documentation

Welcome to the documentation for the WP Scaffold! This is your starting point for all project documentation. If you're new, start here.

## Table of Contents
- [Getting Started](#getting-started)
- [Installation](./installation.md)
- [Architecture Overview](./backend-development/architecture-overview.md)
- [Contributing Guidelines](./contributing-community/contributing-guidelines.md)
- [Troubleshooting Guide](./troubleshooting-faq/troubleshooting-guide.md)
- [Development Guides](./development-guides/README.md)
- [Frontend Development](./frontend-development/README.md)
- [Backend Development](./backend-development/README.md)
- [Testing & Quality Assurance](./testing-quality/README.md)
- [Troubleshooting & FAQ](./troubleshooting-faq/README.md)
- [Contributing & Community](./contributing-community/README.md)

---

# Getting Started

Welcome to the WP Scaffold! This guide will help you get up and running quickly using Local WP, the recommended local development tool for this project.

## Table of Contents
- [Prerequisites](#prerequisites)
- [Quick Start with Local WP](#quick-start-with-local-wp)
- [Project Structure Overview](#project-structure-overview)
- [Next Steps](#next-steps)

## Prerequisites
- Local WP (https://localwp.com/)
- Git
- Composer (for PHP dependencies)
- Node.js & npm (for JS dependencies)

## Quick Start with Local WP
1. **Open Local WP and create a new site:**

   - Choose 'Custom' setup.
   - Set the site path to the cloned repository directory.
   - Set PHP version, web server, and database as needed (see [installation guide](./installation.md)).

2. **Delete the default `wp-content` directory:**

   ```bash
   rm -rf wp-content
   ```

3. **Clone the repository into the `wp-content`:**

   ```bash
   git clone <your-repo-url> wp-content
   ```

4. **Install dependencies:**
   - Open a terminal in the `wp-content` directory.
   - Run:
     ```bash
     composer install
     npm install
     ```

5. **Start your site in Local WP.**

6. **Log in to WordPress and begin development!**

## Project Structure Overview
- `wp-content/` – Main development folder (themes, plugins, mu-plugins, docs)
- See [Architecture Overview](./backend-development/architecture-overview.md) for more details.

## Next Steps
- Read the [Installation Guide](./installation.md) for detailed setup
- Explore the [Architecture Overview](./backend-development/architecture-overview.md)
- Review [Contributing Guidelines](./contributing-community/contributing-guidelines.md) if you want to contribute

For more details, see the full documentation in this directory.
