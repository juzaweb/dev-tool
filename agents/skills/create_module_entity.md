---
name: Create Juzaweb Module Entity
description: Generates a full stack entity (Model, Migration, Service, Controller, Request) for a Juzaweb Module, adhering to the Service Pattern.
---

# Create Juzaweb Module Entity

This skill guides you through creating a new entity within an existing Juzaweb Module.

## Prerequisites
- Module Name (e.g., `DigitalProducts`)
- Entity Name (e.g., `LicenseKey`)
- Table Name (e.g., `dp_license_keys`)

## Steps

### 1. Verify Module Structure
Ensure the module exists in `modules/[Module]`.
structure should look like:
- `src/Http/Controllers`
- `src/Models`
- `src/Services`
- `database/migrations`

### 2. Create Migration
Run command to create migration:
```bash
php artisan make:migration create_[table_name]_table --path=modules/[Module]/database/migrations
```
*Note: Use `Blueprint $table` to define columns. Always include `$table->id()`, `$table->timestamps()`, and if needed `$table->softDeletes()`.*

### 3. Create Model
Create file: `modules/[Module]/src/Models/[Entity].php`.

```php
<?php

namespace Juzaweb\Modules\[Module]\Models;

use Juzaweb\CMS\Models\Model;
use Juzaweb\CMS\Traits\ResourceModel;

class [Entity] extends Model
{
    use ResourceModel;

    protected $table = '[table_name]';

    protected $fillable = [
        'column1',
        'column2',
        // ...
    ];
}
```

### 4. Create Service (CRITICAL)
**Rule**: Business logic MUST reside here, not in the Controller.
Create file: `modules/[Module]/src/Services/[Entity]Service.php`.

```php
<?php

namespace Juzaweb\Modules\[Module]\Services;

use Juzaweb\Modules\Core\Services\BaseService;
use Juzaweb\Modules\[Module]\Models\[Entity];
use Illuminate\Support\Facades\DB;
use Exception;

class [Entity]Service extends BaseService
{
    public function __construct([Entity] $model)
    {
        $this->model = $model;
    }

    /**
     * Create a new resource.
     */
    public function create(array $data): [Entity]
    {
        return $this->transaction(function () use ($data) {
            // Business Logic here
            return $this->model->create($data);
        });
    }

    /**
     * Update a resource.
     */
    public function update(array $data, int $id): [Entity]
    {
        return $this->transaction(function () use ($data, $id) {
            $model = $this->find($id);
            $model->update($data);
            return $model;
        });
    }
}
```

### 5. Create Request (Validation)
Create file: `modules/[Module]/src/Http/Requests/[Entity]Request.php`.

```php
<?php

namespace Juzaweb\Modules\[Module]\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class [Entity]Request extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // Add other rules
        ];
    }
}
```

### 6. Create Controller
Create file: `modules/[Module]/src/Http/Controllers/[Entity]Controller.php`.

```php
<?php

namespace Juzaweb\Modules\[Module]\Http\Controllers;

use Juzaweb\CMS\Http\Controllers\BackendController;
use Juzaweb\Modules\[Module]\Http\Requests\[Entity]Request;
use Juzaweb\Modules\[Module]\Services\[Entity]Service;
use Illuminate\Http\JsonResponse;

class [Entity]Controller extends BackendController
{
    public function __construct(protected [Entity]Service $service)
    {
    }

    public function index()
    {
        // Use DataTable or standard view
        return view('[module]::[entity].index', [
            'title' => '[Entity Name]'
        ]);
    }

    public function store([Entity]Request $request): JsonResponse
    {
        $this->service->create($request->validated());
        return $this->success(['message' => trans('cms::app.created_successfully')]);
    }

    public function update([Entity]Request $request, int $id): JsonResponse
    {
        $this->service->update($request->validated(), $id);
        return $this->success(['message' => trans('cms::app.updated_successfully')]);
    }
}
```

### 7. Register Route
Add to `modules/[Module]/src/routes/admin.php`:

```php
use Juzaweb\Modules\[Module]\Http\Controllers\[Entity]Controller;

Route::admin('[kebab-case-entity]', [Entity]Controller::class);
```
