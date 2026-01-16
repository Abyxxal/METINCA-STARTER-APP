# Service Layer Pattern - Best Practices

## 📁 Struktur Directory

```
app/
├── Http/
│   └── Controllers/           # Thin controllers (HTTP only)
│       └── CBT/
│           └── EmployeeCompetencyController.php
├── Services/                  # Business logic
│   └── EmployeeCompetencyService.php
├── Models/                    # Eloquent models (data access)
│   ├── Employee.php
│   └── EmployeeCompetency.php
└── Repositories/              # (Optional) Complex queries
    └── EmployeeRepository.php
```

## 🎯 Prinsip Service Layer

### 1. **Controller (Thin)**
- Handle HTTP request/response
- Validasi input
- Return view/JSON
- Call service untuk business logic

### 2. **Service (Fat)**
- Business logic
- Data processing
- Validasi bisnis
- Transaction management
- Return data/model

### 3. **Repository (Optional)**
- Complex queries
- Database abstraction
- Reusable query logic

## ✅ Contoh Implementasi

### Service Class
```php
<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeCompetencyService
{
    /**
     * Update competency level
     * 
     * Business logic:
     * - Validate level range
     * - Validate skill exists
     * - Check division permission
     * - Create/update record
     * - Log changes
     */
    public function updateCompetencyLevel(Employee $employee, int $skillId, int $level, ?string $notes = null)
    {
        // Validation logic
        if ($level < 0 || $level > 4) {
            throw new \InvalidArgumentException('Invalid level');
        }

        DB::beginTransaction();
        try {
            // Business logic here
            $competency = EmployeeCompetency::updateOrCreate([...]);
            
            DB::commit();
            return $competency;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

### Controller Usage
```php
<?php

namespace App\Http\Controllers\CBT;

use App\Services\EmployeeCompetencyService;

class EmployeeCompetencyController extends Controller
{
    protected $competencyService;

    public function __construct(EmployeeCompetencyService $competencyService)
    {
        $this->competencyService = $competencyService;
    }

    public function update(Request $request, Employee $employee)
    {
        // Validate input
        $validated = $request->validate([...]);

        try {
            // Call service
            $competency = $this->competencyService->updateCompetencyLevel(
                $employee,
                $validated['skill_id'],
                $validated['level'],
                $validated['notes']
            );

            // Return response
            return back()->with('success', 'Berhasil diubah');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
```

## 📌 Keuntungan

1. **Testability** ✅
   - Service dapat di-unit test tanpa HTTP
   - Mock dependencies mudah

2. **Reusability** ✅
   - Logic bisa dipanggil dari berbagai controller
   - Bisa dipanggil dari console command, job, event listener

3. **Maintainability** ✅
   - Kode terstruktur dan mudah di-maintain
   - Single Responsibility Principle

4. **Scalability** ✅
   - Mudah menambah fitur baru
   - Tidak mengganggu kode lama

## 🔄 Migration dari Controller ke Service

### Before (Fat Controller)
```php
public function update(Request $request, Employee $employee)
{
    $validated = $request->validate([...]);

    DB::beginTransaction();
    try {
        // Business logic di controller ❌
        $competency = EmployeeCompetency::updateOrCreate([...]);
        // More logic...
        DB::commit();
        return back()->with('success', 'OK');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}
```

### After (Thin Controller + Service)
```php
// Controller
public function update(Request $request, Employee $employee)
{
    $validated = $request->validate([...]);

    try {
        $competency = $this->competencyService->updateCompetencyLevel(
            $employee,
            $validated['skill_id'],
            $validated['level']
        );
        return back()->with('success', 'OK');
    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}

// Service
public function updateCompetencyLevel(Employee $employee, int $skillId, int $level)
{
    DB::beginTransaction();
    try {
        // Business logic di service ✅
        $competency = EmployeeCompetency::updateOrCreate([...]);
        // More logic...
        DB::commit();
        return $competency;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

## 📝 Aturan Praktis

### ✅ DO (Lakukan)
- Controller hanya handle HTTP
- Service handle business logic
- Service return data/model
- Use dependency injection
- Throw exception dari service
- Catch exception di controller

### ❌ DON'T (Jangan)
- Business logic di controller
- Direct model query di controller (kecuali simple read)
- Return view dari service
- Access request di service
- Hardcode values di service

## 🎓 Kapan Pakai Service Layer?

### Pakai Service Layer jika:
- ✅ Business logic kompleks
- ✅ Logic perlu dipakai di berbagai tempat
- ✅ Butuh transaction management
- ✅ Perlu validasi bisnis
- ✅ Logic perlu di-test terpisah

### Tidak Perlu Service jika:
- ❌ Simple CRUD tanpa logic
- ❌ Hanya read data dan tampilkan
- ❌ Logic sangat sederhana (1-2 baris)

## 📚 Referensi
- Repository Pattern
- Domain-Driven Design (DDD)
- Clean Architecture
- SOLID Principles
