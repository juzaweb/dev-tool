---
name: Create Page Template for JuzaWeb Theme
description: Guide to create and register a custom page template in JuzaWeb CMS theme
---

# Create Page Template for JuzaWeb Theme

This skill guides you through creating and registering a custom page template in a JuzaWeb CMS theme.

## Overview

Page templates in JuzaWeb CMS allow you to create custom layouts for different types of pages. Each template consists of:
1. A Blade view file
2. Registration in the theme's ServiceProvider

## Steps to Create a Page Template

### Step 1: Create the Template View File

Create a new Blade template file in your theme's `src/resources/views/templates/` directory.

**File location pattern:**
```
themes/{theme-name}/src/resources/views/templates/{template-name}.blade.php
```

**Template structure:**
```blade
@extends('{theme-name}::layouts.main')

@section('content')
    <!-- Your custom page content here -->

    <!-- Example sections -->
    <section class="banner-section">
        <!-- Banner content -->
    </section>

    <section class="features-section">
        <!-- Features content -->
    </section>
@endsection
```

**Best practices:**
- Always extend the main layout: `@extends('{theme-name}::layouts.main')`
- Use semantic HTML5 sections
- Keep layout clean and reusable
- Use Blade components for repeated elements
- Use `asset()` helper for all asset paths
- Use `__()` helper for all translatable strings
- Follow the existing theme's design patterns

### Step 2: Register the Template in ThemeServiceProvider

Open your theme's `ThemeServiceProvider.php` file and register the template.

**File location:**
```
themes/{theme-name}/src/Providers/ThemeServiceProvider.php
```

**Steps:**

1. **Import the PageTemplate facade:**
```php
use Juzaweb\Modules\Core\Facades\PageTemplate;
```

2. **Create registration method:**
```php
/**
 * Register page templates
 */
protected function registerPageTemplates(): void
{
    PageTemplate::make(
        'template-key',  // Unique identifier (snake_case)
        function () {
            return [
                'label' => __('Template Display Name'),
                'description' => __('Template description for admin'),
            ];
        }
    );
}
```

3. **Call the method in boot():**
```php
public function boot(): void
{
    $this->registerPageTemplates();

    // ... other boot code
}
```

### Step 3: Complete Example

**Example template file:** `src/resources/views/templates/cms.blade.php`
```blade
@extends('digital-products::layouts.main')

@section('content')
    <section class="banner-section">
        <div class="container">
            <h1>{{ __('What is JuzaWeb CMS?') }}</h1>
            <p>{{ __('Your CMS description here') }}</p>
        </div>
    </section>
@endsection
```

**Registration in ThemeServiceProvider.php:**
```php
<?php

namespace Juzaweb\Themes\DigitalProducts\Providers;

use Juzaweb\Modules\Core\Facades\PageTemplate;
use Juzaweb\Modules\Core\Providers\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerPageTemplates();
    }

    protected function registerPageTemplates(): void
    {
        PageTemplate::make(
            'cms',
            function () {
                return [
                    'label' => __('CMS Page'),
                    'description' => __('JuzaWeb CMS introduction and features page'),
                ];
            }
        );
    }
}
```

### Step 4: Using the Template

After registration, the template will be available in the admin panel:

1. Go to **Admin Panel → Pages**
2. Create a new page or edit an existing one
3. In the **Template** dropdown, select your newly registered template
4. Save the page

The page will now use your custom template layout.

## Template Key Naming Convention

- Use `snake_case` for template keys
- Use descriptive names (e.g., `home`, `cms`, `contact`, `landing_page`)
- Avoid special characters
- Keep keys short but meaningful

## Advanced: Templates with Page Blocks

For more complex templates with customizable content blocks:

```php
PageTemplate::make(
    'home',
    function () {
        return [
            'label' => __('Home Page'),
            'description' => __('Homepage template with customizable blocks'),
            'blocks' => [
                'hero' => __('Hero Section'),
                'features' => __('Features Section'),
                'testimonials' => __('Testimonials Section'),
            ],
        ];
    }
);
```

Then register corresponding PageBlocks for each section using `PageBlock::make()`.

## Troubleshooting

**Template not appearing in dropdown:**
- Clear cache: `php artisan cache:clear`
- Check if `registerPageTemplates()` is called in `boot()`
- Verify PageTemplate facade is imported
- Check for PHP syntax errors

**Template not rendering:**
- Verify the view file exists in correct location
- Check the template key matches between registration and view filename
- Ensure the view extends the correct layout

## References

- Theme pattern: `themes/itech` (Public: https://github.com/juzaweb/itech)
- PageTemplate Facade: `modules/core/src/Facades/PageTemplate.php`
- Example implementation: `themes/digital-products/src/Providers/ThemeServiceProvider.php`
