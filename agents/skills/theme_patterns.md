---
name: Juzaweb Theme & Facade Patterns
description: Instructions and patterns for working with Juzaweb CMS Themes, Menus, Sidebars, and Facades.
---

# Juzaweb Theme & Facade Patterns

This skill captures key patterns for registering theme components and using Core Facades in Juzaweb CMS.

## The "Callback Returns Array" Pattern (Critical)

In Juzaweb CMS, many registration methods (`make`) accept a logic Closure. **Crucially**, this Closure must **return an array** of configuration. It does **not** operate on a passed-by-reference Builder object.

### Correct Usage

```php
use Juzaweb\Modules\Core\Facades\Menu;
use Juzaweb\Modules\Core\Facades\Sidebar;

Menu::make('posts', function () {
    // CORRECT: Return an array
    return [
        'title' => 'Posts',
        'url' => 'posts',
        'priority' => 10,
    ];
});

Sidebar::make('main', function () {
    return [
        'label' => 'Main Sidebar',
        'description' => 'Default sidebar',
    ];
});
```

### Incorrect Usage (Do Not Use)

```php
// WRONG: Assuming Builder Pattern
Menu::make('posts', function ($menu) {
    $menu->setTitle('Posts'); // This will FAIL
});
```

### Applied Facades

This pattern applies to:
- `Juzaweb\Modules\Core\Facades\Menu` (Admin Menu)
- `Juzaweb\Modules\Core\Facades\NavMenu` (Frontend Menu Locations)
- `Juzaweb\Modules\Core\Facades\MenuBox` (Admin Menu Editor Widgets)
- `Juzaweb\Modules\Core\Facades\Sidebar` (Widget Areas)
- `Juzaweb\Modules\Core\Facades\Widget` (Custom Widgets)
- `Juzaweb\Modules\Core\Facades\PageTemplate` (Custom Page Templates)
- `Juzaweb\Modules\Core\Facades\PageBlock` (Page Builder Blocks)
- `Juzaweb\Modules\Core\Facades\Thumbnail::defaults` (Default Thumbnails)

## Helper Facades

### Sitemap (`Juzaweb\Modules\Core\Facades\Sitemap`)

Used to register sitemap providers.

```php
use Juzaweb\Modules\Core\Facades\Sitemap;

Sitemap::register('key', \Path\To\Model::class);
```
Model must implement `Juzaweb\Modules\Core\Contracts\Sitemapable`.

### Breadcrumb (`Juzaweb\Modules\Core\Facades\Breadcrumb`)

Used to manage breadcrumbs.

```php
use Juzaweb\Modules\Core\Facades\Breadcrumb;

Breadcrumb::add('Title', '/url');
```

## Documentation Location

- Core Documentation: `modules/core/docs/`
- Theme Documentation: `modules/core/docs/themes/`
- Helper Documentation: `modules/core/docs/the-basics/helpers.md`
