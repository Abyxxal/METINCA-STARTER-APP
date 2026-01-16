# 🏗️ Database Schema Rewrite - Nested Hierarchy

## 📋 Overview

Database telah distruktur ulang untuk mendukung **Nested Hierarchy UI**:
```
Department > Division > Position > Employee
```

## 🎯 Key Changes

### ✅ Completed

1. **Positions sekarang CHILD dari Divisions** (bukan Department)
   - Sebelum: `positions.department_id`
   - Sesudah: `positions.division_id` ✨

2. **Removed "level" columns**
   - Tidak perlu seniority level tracking di database

3. **Removed "employee_count" columns**
   - Gunakan Eloquent `withCount()` instead

4. **Simplified Status Handling**
   - Enum dengan 3 nilai: `Aktif`, `Non-Aktif`, `Cuti`

---

## 📊 Migration Files

### 1️⃣ `2026_01_08_000001_create_departments_table.php`
```
Columns:
- id (PK)
- name (Unique)
- description (Nullable)
- timestamps
```

### 2️⃣ `2026_01_08_000002_create_divisions_table.php`
```
Columns:
- id (PK)
- department_id (FK → departments, onDelete CASCADE)
- name
- timestamps
```

### 3️⃣ `2026_01_08_000003_create_positions_table.php`
```
Columns:
- id (PK)
- division_id (FK → divisions, onDelete CASCADE) ⭐ CHANGED FROM department_id
- name
- timestamps
```

### 4️⃣ `2026_01_08_000004_create_employees_table.php`
```
Columns:
- id (PK)
- nik (Unique)
- name
- email (Unique)
- phone (Nullable)
- department_id (FK)
- division_id (FK)
- position_id (FK)
- status (Enum: Aktif, Non-Aktif, Cuti)
- join_date (Nullable)
- timestamps
```

---

## 🔄 Relationships

### Department Model
```php
- hasMany('divisions')
- hasMany('employees')
```

### Division Model
```php
- belongsTo('department')
- hasMany('positions') ⭐ NEW
- hasMany('skills')
```

### Position Model
```php
- belongsTo('division') ⭐ CHANGED FROM department
- hasMany('employees')
```

### Employee Model
```php
- belongsTo('department')
- belongsTo('division')
- belongsTo('position')
- hasMany('competencies')
```

---

## 🌱 DatabaseSeeder

Seeder membuat struktur ini:

```
Department: Information Technology
├── Division: Backend Engineering
│   └── Position: Senior Backend Developer
│       └── Employee: Budi Santoso (E001)
└── Admin & User Accounts
```

---

## ⚡ Cara Menjalankan

### Step 1: Run Migrations
```bash
php artisan migrate
```

### Step 2: Run Seeders
```bash
php artisan db:seed
```

### Step 3: Check Status
```bash
php artisan migrate:status
```

---

## 🚀 Next Steps untuk Controller

Update `MasterDataController` untuk:

1. **Positions endpoint** - filter by `division_id` (bukan `department_id`)
   ```php
   public function getPositionsByDivision(Request $request) // Renamed from getPositionsByDepartment
   ```

2. **Position CRUD** - gunakan `division_id` saat validate
   ```php
   'division_id' => 'required|exists:divisions,id' // Changed from department_id
   ```

3. **Employee filtering** - sekarang bisa filter by division dengan proper hierarchy

---

## 📝 Notes

- Foreign keys menggunakan `onDelete('cascade')` untuk auto-cleanup
- Employees menggunakan `onDelete('restrict')` untuk prevent accidental deletion
- Semua timestamps menggunakan default Laravel timestamps

---

**Created:** January 8, 2026  
**Laravel Version:** 12  
**Database:** MySQL
