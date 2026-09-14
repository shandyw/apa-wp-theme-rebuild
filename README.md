# ApacheCorp WordPress Theme Rebuild

A modernization of the WordPress theme used for **ApacheCorp.com**, focused on PHP 8+ compatibility, preserving existing content architecture, reducing technical debt, and rebuilding only the ACF blocks that are actually needed.

## Project Overview

This project involved rebuilding the existing ApacheCorp.com WordPress theme while maintaining the site's current content, structure, and visual design.

Rather than starting over with a new content model or migrating existing pages, the goal was to modernize the underlying codebase while preserving the WordPress editing experience already in use.

A key part of the rebuild was reviewing the existing ACF implementation and recreating only the flexible content blocks and components still required by the site.

The result is a cleaner, more maintainable WordPress theme designed to run reliably on modern PHP versions.

---

## Primary Goals

- Upgrade the WordPress codebase for **PHP 8+ compatibility**
- Maintain compatibility with **PHP 8.2+ without notices or warnings**
- Preserve existing WordPress content
- Preserve existing ACF fields, field names, and field keys
- Avoid unnecessary content migrations
- Rebuild only the ACF modules actively needed by the site
- Maintain the existing visual design
- Improve front-end performance
- Reduce unnecessary plugin dependencies
- Create a more maintainable theme architecture
- Establish a cleaner internal design system
- Improve security and output handling
- Keep the WordPress editing experience familiar for content editors

---

## ACF Strategy

The existing site relies heavily on **Advanced Custom Fields**.

One of the main goals of the rebuild was to preserve the existing ACF content model wherever possible.

Existing field names and field keys are maintained so that previously entered content continues to work without requiring a large-scale migration.

Instead of recreating every historical module, the rebuild focuses on the ACF blocks and flexible content layouts that are still actively used.

This approach reduces:

- Legacy code
- Unused templates
- CSS overhead
- JavaScript overhead
- Maintenance complexity

while preserving the content editors already rely on.

---

## PHP 8 Modernization

The legacy theme was reviewed and rebuilt with modern PHP behavior in mind.

The theme is designed to avoid common PHP 8 compatibility issues, including:

- Deprecated PHP functionality
- Dynamic properties
- Undefined array keys
- Undefined variables
- Invalid parameter handling
- PHP notices and warnings
- Unsafe assumptions about returned data
- Legacy patterns that no longer behave consistently under modern PHP

The project targets clean operation under **PHP 8.2+**.

---

## Theme Development Principles

### Preserve Existing Content

Existing WordPress structures are treated as part of the site's API.

Unless explicitly required, the project does not change:

- Post IDs
- Post slugs
- Taxonomies
- ACF field names
- ACF field keys
- Existing content structure

This minimizes migration risk and protects existing editorial content.

### Reusable PHP Components

Templates favor reusable PHP partials and components rather than repeating markup across multiple templates.

This helps keep:

- Markup consistent
- Accessibility improvements centralized
- Maintenance simpler
- Modules easier to update

### Secure Data Handling

WordPress security practices are used throughout the theme.

Output is escaped using the appropriate WordPress escaping functions, including:

```php
esc_html()
esc_attr()
esc_url()
wp_kses_post()
```

Input is sanitized and validated before being stored or processed.

### Progressive Enhancement

JavaScript is treated as an enhancement rather than a dependency for basic content access.

Where practical:

- Core content remains usable without JavaScript
- Interactions build on semantic HTML
- Accessibility is maintained before enhancement
- JavaScript behavior remains modular and focused

---

## CSS Architecture

The theme uses a structured CSS architecture organized around:

```text
tokens/
base/
components/
modules/
utilities/
```

### Tokens

Global design values such as:

- Colors
- Typography
- Spacing
- Breakpoints
- Border treatments
- Layout values

### Base

Foundational styles including:

- Resets
- Typography defaults
- Document styles
- Global element behavior

### Components

Reusable UI patterns shared throughout the site.

Examples may include:

- Buttons
- Cards
- Navigation elements
- Form controls
- Media components

### Modules

Styles associated with specific ACF flexible content modules or larger page sections.

### Utilities

Small, reusable helper classes for layout and presentation.

This structure reduces duplication and creates a clearer design system inside the WordPress theme.

---

## Performance

Performance improvements are approached primarily by reducing unnecessary work rather than adding additional optimization plugins.

Areas of focus include:

- Removing unused legacy code
- Rebuilding only necessary ACF modules
- Reducing plugin dependency
- Limiting unnecessary JavaScript
- Organizing CSS by responsibility
- Reusing components
- Avoiding duplicated markup and assets
- Loading functionality only where it is needed

---

## Plugin Philosophy

The project intentionally avoids introducing plugins when functionality can reasonably be handled within the theme or existing application architecture.

New plugins should only be introduced when they provide a clear benefit that outweighs:

- Additional maintenance
- Performance overhead
- Security surface
- Update dependencies
- Long-term compatibility concerns

---

## Editor Experience

Although the underlying codebase has been significantly modernized, the WordPress editing experience is intended to remain familiar.

Editors should be able to continue working with existing pages and ACF fields without learning an entirely new content system.

Templates and ACF modules are designed to remain:

- Predictable
- Reusable
- Editor-friendly
- Compatible with existing content

---

## Development Priorities

When making changes to this project, prioritize the following:

1. Preserve existing content compatibility.
2. Maintain PHP 8.2+ compatibility.
3. Reuse existing ACF fields whenever possible.
4. Avoid unnecessary migrations.
5. Prefer reusable components over duplicated templates.
6. Escape output and sanitize input.
7. Avoid deprecated WordPress and PHP patterns.
8. Keep JavaScript progressively enhanced.
9. Avoid unnecessary plugin dependencies.
10. Maintain the existing design unless a design change is intentional.

---

## Technology

- WordPress
- PHP 8+
- Advanced Custom Fields / ACF Extended
- JavaScript
- HTML5
- CSS
- Custom WordPress Theme Development

---

## Project Goal

The purpose of this rebuild is not to redesign ApacheCorp.com from scratch.

It is to modernize the foundation beneath the existing site.

By preserving the site's content architecture while replacing legacy theme code, unnecessary ACF modules, and outdated PHP patterns, the project provides a cleaner foundation that is easier to maintain, safer to upgrade, and better prepared for future development.
