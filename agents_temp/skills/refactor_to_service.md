---
name: Refactor Controller Logic to Service
description: Instructions to refactor fat controllers into thin controllers using the Service pattern.
---

# Refactor Controller Logic to Service

This skill guides you through moving business logic from a Controller to a Service class, a critical requirement for this project.

## Steps

### 1. Identify Logic to Move
Look for complex logic in Controller methods (`store`, `update`, or custom actions).
- Database transactions.
- Multiple model updates.
- External API calls.
- Conditional branches based on business rules.

### 2. Locate/Create Service
Check if a Service exists for the primary model.
- Path: `modules/[Module]/src/Services/[Entity]Service.php`.
- If not, create it extending `Juzaweb\Modules\Core\Services\BaseService`.

### 3. Move Logic
**In Service Class:**
Create a public method with a descriptive name (e.g., `processPayment`, `syncAgencies`).

```php
public function [methodName](array $data, ?int $id = null): [ReturnType]
{
    return $this->transaction(function () use ($data, $id) {
        // ... pasted logic ...
        // Replace $request->input('...') with $data['...']
        // Use $this->model for the primary model
        // Throw exceptions for errors: throw new \Exception('Error message');
    });
}
```

### 4. Clean Up Controller
**In Controller Class:**
Inject the Service in `__construct`.

```php
public function __construct(protected [Entity]Service $service) {}

public function [action]([Entity]Request $request)
{
    $this->service->[methodName]($request->validated());
    
    return $this->success([
        'message' => trans('cms::app.saved_successfully')
    ]);
}
```

### 5. Handle Exceptions
Ensure the Controller catches exceptions if not handled globally, or rely on the global exception handler (Juzaweb usually handles standard exceptions).

### 6. Verify Imports
- Remove unused Model imports in Controller.
- 5. Ensure Service imports necessary Models/Facades.
