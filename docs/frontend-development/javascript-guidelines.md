# JavaScript Guidelines

This document outlines the standards and best practices for writing JavaScript in a WP Scaffold project.

## Table of Contents
- [Introduction](#introduction)
- [Code Style](#code-style)
- [Project Structure](#project-structure)
- [Modern JavaScript Features](#modern-javascript-features)
- [React Best Practices](#react-best-practices)
- [Documentation](#documentation)
- [WordPress Integration](#wordpress-integration)

## Introduction

The WP Scaffold project uses modern JavaScript to create interactive and dynamic user experiences. These guidelines ensure consistency across the codebase and promote best practices for JavaScript development.

## Code Style

### ESLint Configuration

The project uses ESLint to enforce coding standards. The configuration extends the WordPress coding standards with some project-specific rules:

```json
{
  "extends": [
    "eslint:recommended",
    "plugin:@wordpress/eslint-plugin/recommended"
  ],
  "rules": {
    "no-console": ["error", { "allow": ["warn", "error"] }],
    "no-unused-vars": "warn",
    "react/prop-types": "error",
    "react/jsx-key": "error"
  }
}
```

To run the linter:

```bash
npm run lint:js
```

## Project Structure

### Directory Organization

The JavaScript files in the WP Scaffold themes are organized in the following structure:

```
wp-content/themes/[theme-name]/assets/js/
├── frontend/
│   ├── components/
│   ├── frontend.js
│   └── editor-style-overrides.js
└── block-editor/
    ├── blocks/
    ├── extensions/
    └── editor.js
```

Compiled JavaScript is output to the `dist/js/` directory during the build process.

### Module Organization

- Each component should be in its own directory
- Avoid barrel files (index.js) unless necessary (see: https://vercel.com/blog/how-we-optimized-package-imports-in-next-js)
- Group related functionality in modules
- Use named exports for better tree-shaking
- Keep files small and focused on a single responsibility

Example:

```javascript
// components/Button/Button.js
import React from 'react';

export const Button = ({ children, onClick, variant = 'primary' }) => {
  return (
    <button
      className={`button button--${variant}`}
      onClick={onClick}
    >
      {children}
    </button>
  );
};

// Usage in another file
import { Button } from './components/Button/Button';
```

## Modern JavaScript Features

### ES6+ Features

Use modern JavaScript features to write cleaner, more maintainable code:

#### Arrow Functions

```javascript
// Good
const multiply = (a, b) => a * b;

// Instead of
function multiply(a, b) {
  return a * b;
}
```

#### Destructuring

```javascript
// Good
const { title, content, author } = post;

// Instead of
const title = post.title;
const content = post.content;
const author = post.author;
```

#### Template Literals

```javascript
// Good
const greeting = `Hello, ${name}!`;

// Instead of
const greeting = 'Hello, ' + name + '!';
```

#### Spread Operator

```javascript
// Good
const newArray = [...oldArray, newItem];
const newObject = { ...oldObject, newProperty: value };

// Instead of
const newArray = oldArray.concat([newItem]);
const newObject = Object.assign({}, oldObject, { newProperty: value });
```

#### Optional Chaining

```javascript
// Good
const userName = user?.profile?.name;

// Instead of
const userName = user && user.profile && user.profile.name;
```

#### Nullish Coalescing

```javascript
// Good
const count = value ?? 0;

// Instead of
const count = value !== null && value !== undefined ? value : 0;
```

## React Best Practices

### Functional Components

Use functional components with hooks instead of class components:

```jsx
// Good
import React, { useState, useEffect } from 'react';

const UserProfile = ({ user }) => {
  if (!user) return <Error message="User not found" />;

  return (
    <div className="user-profile">
      <h2>{user.name}</h2>
      <p>{user.email}</p>
    </div>
  );
};
```

### Custom Hooks

Extract reusable logic into custom hooks:

```jsx
// hooks/useLocalStorage.js
import { useState, useEffect } from 'react';

export const useLocalStorage = (key, initialValue) => {
  const [value, setValue] = useState(() => {
    try {
      const item = window.localStorage.getItem(key);
      return item ? JSON.parse(item) : initialValue;
    } catch (error) {
      console.error('Error reading from localStorage:', error);
      return initialValue;
    }
  });

  useEffect(() => {
    try {
      window.localStorage.setItem(key, JSON.stringify(value));
    } catch (error) {
      console.error('Error writing to localStorage:', error);
    }
  }, [key, value]);

  return [value, setValue];
};

// Usage
const [theme, setTheme] = useLocalStorage('theme', 'light');
```

## Documentation

### JSDoc Comments

Use JSDoc comments to document functions and components:

```javascript
/**
 * Fetches user data from the API.
 *
 * @param {string} userId - The ID of the user to fetch.
 * @returns {Promise<Object>} The user data object.
 * @throws {Error} If the API request fails.
 */
const fetchUserData = async (userId) => {
  // Implementation
};

/**
 * Button component with different variants.
 *
 * @param {Object} props - Component props.
 * @param {string} [props.variant='primary'] - Button variant (primary, secondary, etc.).
 * @param {Function} props.onClick - Click handler function.
 * @param {React.ReactNode} props.children - Button content.
 * @returns {JSX.Element} The Button component.
 */
export const Button = ({ variant = 'primary', onClick, children }) => {
  // Implementation
};
```

## WordPress Integration

### Gutenberg Components

Use Gutenberg components when building block editor interfaces:

```jsx
import { Button, Panel, PanelBody, TextControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

const MyBlockEdit = ({ attributes, setAttributes }) => {
  const { title } = attributes;
  return (
    <Panel>
      <PanelBody title={__('Settings', 'text-domain')}>
        <TextControl
          label={__('Title', 'text-domain')}
          value={title}
          onChange={(value) => setAttributes({ title: value })}
        />
        <Button isPrimary onClick={() => console.log('Clicked')}>
          {__('Apply', 'text-domain')}
        </Button>
      </PanelBody>
    </Panel>
  );
};
```
