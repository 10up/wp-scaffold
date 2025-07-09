# Installation Guide

This guide provides step-by-step instructions for installing and configuring a WP Scaffold project using Local WP.

## Table of Contents
- [System Requirements](#system-requirements)
- [Cloning the Repository](#cloning-the-repository)
- [Setting Up with Local WP](#setting-up-with-local-wp)
- [Installing Dependencies](#installing-dependencies)
- [Database Setup](#database-setup)
- [Troubleshooting](#troubleshooting)

## System Requirements
- Local WP (https://localwp.com/)
- Git
- Composer (for PHP dependencies)
- Node.js & npm (for JS dependencies)

## Cloning the Repository
```bash
git clone <your-repo-url>
```

## Setting Up with Local WP
1. Open Local WP and create a new site.
2. Choose 'Custom' setup and set the site path to your cloned repository.
3. Set PHP version, web server, and database as needed.
4. Ensure the `wp-content` directory in Local WP points to your project’s `wp-content` folder.

## Installing Dependencies
Open a terminal in the `wp-content` directory and run:
```bash
composer install
npm install
```

## Database Setup
- Local WP will create a database for your site automatically.
- If you have a database dump (e.g., `local.sql`), import it using Local WP’s database tools or via the command line.
- Update your site’s settings in the WordPress admin as needed.

## Troubleshooting
If you encounter issues, see the [Troubleshooting Guide](./troubleshooting-faq/troubleshooting-guide.md) or check the logs in the `wp-content` directory if available.
