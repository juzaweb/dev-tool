---
name: Create Juzaweb CRUD
description: Generates a full CRUD for a Juzaweb Module using `module:make-crud` and refactors it to the Service Pattern.
---

# Create Juzaweb CRUD with Service Pattern

This skill guides you through generating a CRUD entity and ensuring it adheres to the project's strict Service Pattern and PSR standards.

## Prerequisites

- Module Name (e.g., `Digital`)
- Model/Entity Name (e.g., `LicenseKey`)

## Steps

### 1. Create Migration

Run the following command to create the migration table:

```bash
php artisan module:make-migration create_[table_name]_table [Module]
```

- Open the generated migration file.
- Define your columns.
- **Rules**:
    - Use uuid for main table `$table->uuid('id')->primary()` and id for sub table `$table->id()`.
    - Always use `$table->datetimes()`.
    - If translatable, create a second migration for the translation table (refer to `modules/core/docs/modules/crud.md`).

### 2. Create Model

Generate the model:

```bash
php artisan module:make-model [Model] [Module]
```

- If using translations, ensure you create the `[Model]Translation` class as well.

### 3. Scaffold CRUD

Generate the initial CRUD boilerplate:

```bash
php artisan module:make-crud [Model] [Module]
```

_Note: This generates the Controller, Routes, and Views, but the Controller logic might be in the 'old style'. We must refactor it._

### 4. Create Service Class (OPTIONAL)

If logic complexity warrants, create a Service class for business logic.

```php
<?php

namespace Juzaweb\Modules\[Module]\Services;

use Juzaweb\Modules\Core\Services\BaseService;
use Juzaweb\Modules\[Module]\Models\[Model];
use Illuminate\Support\Facades\DB;

class [Model]Service extends BaseService
{
    public function __construct([Model] $model)
    {
        $this->model = $model;
    }

    public function create(array $data): [Model]
    {
        return $this->transaction(function () use ($data) {
            // Logic before save
            return $this->model->create($data);
        });
    }

    public function update(array $data, int $id): [Model]
    {
        return $this->transaction(function () use ($data, $id) {
            $model = $this->find($id);
            $model->update($data);
            return $model;
        });
    }
}
```

### 5. Refactor Controller

Open `modules/[Module]/src/Http/Controllers/[Model]Controller.php` and refactor strictly:

1.  **Inject Service**:
    ```php
    public function __construct(protected [Model]Service $service) {}
    ```
2.  **Use Service in Methods**:
    - `store()`: Call `$this->service->create($request->validated())`.
    - `update()`: Call `$this->service->update($request->validated(), $id)`.
3.  **Return JSON**:
    - Ensure methods return `$this->success()` or appropriate responses.

### 6. Verify FormRequest

Ensure a `FormRequest` was created (usually in `src/Http/Requests`). If not, create one and type-hint it in your Controller's `store` and `update` methods.

### 7. View Cleanup (Optional)

Check the generated views in `modules/[Module]/src/resources/views/[kebab-model]/`. Ensure they use the correct layouts and components.
