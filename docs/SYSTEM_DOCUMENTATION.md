# Master Data Management System - Dokumentasi Lengkap

## 📋 Ringkasan Sistem

Sistem Master Data Management adalah aplikasi web untuk mengelola data karyawan, departemen, divisi, dan posisi dalam perusahaan. Sistem ini dibangun dengan Laravel 12, Bootstrap 5, dan jQuery.

---

## 🏗️ Arsitektur Sistem

### Database Structure
```
Departments (1) ----> (Many) Divisions (1) ----> (Many) Positions (1) ----> (Many) Employees
```

**Tabel:**
- `departments` - Departemen (HR, Finance, IT, dll)
- `divisions` - Divisi dalam departemen (IT Support, IT Development, dll)
- `positions` - Posisi dalam divisi (Manager, Staff, Junior Staff, dll)
- `employees` (users) - Data karyawan dengan relationship ke semuanya
- `competencies` - Level kompetensi karyawan

---

## 🎯 Fitur Utama

### 1. **Data Karyawan (Employee Management)**

#### CRUD Operations:
- ✅ **Tambah Karyawan**: Form modal dengan upload foto
- ✅ **Edit Karyawan**: Pre-fill data dengan foto preview
- ✅ **Hapus Karyawan**: Konfirmasi via SweetAlert2
- ✅ **View Karyawan**: Tabel dengan filter departemen/divisi

#### Fields:
- NIK (Unique ID)
- Nama Lengkap
- Email
- Password (opsional saat edit)
- Department / Division / Position
- Status (Aktif/Non-Aktif)
- Foto Profile

#### Features:
- Dropdown divissi auto-populate berdasarkan department
- Dropdown posisi auto-populate berdasarkan divisi
- Upload foto dengan preview
- SweetAlert2 notification untuk success/error
- Responsive table dengan DataTables

---

### 2. **Data Departemen (Department Management)**

#### CRUD Operations:
- ✅ **Tambah Departemen**: Nested form dengan divisi dan posisi
- ✅ **Edit Departemen**: Full nested structure edit
- ✅ **Hapus Departemen**: Cascade delete (delete semua child)
- ✅ **View Departemen**: Tabel dengan employee count badge

#### Features:
- Nested structure: Department → Divisions → Positions
- Tambah multiple divisi per departemen
- Tambah multiple posisi per divisi
- Real-time employee count display
- Cascade delete safety

---

## 🔌 API Endpoints

### Employee Endpoints
```
GET    /api/employees           - List semua karyawan
GET    /api/employees/{nik}     - Detail karyawan
POST   /api/employees           - Tambah karyawan
PUT    /api/employees/{nik}     - Update karyawan
DELETE /api/employees/{nik}     - Hapus karyawan
```

### Department Endpoints
```
GET    /api/departments         - List semua departemen
POST   /api/departments         - Tambah departemen
PUT    /api/departments/{id}    - Update departemen
DELETE /api/departments/{id}    - Hapus departemen
```

### Division Endpoints
```
GET    /api/divisions           - List divisi (support ?department_id=X)
POST   /api/divisions           - Tambah divisi
PUT    /api/divisions/{id}      - Update divisi
DELETE /api/divisions/{id}      - Hapus divisi
```

### Position Endpoints
```
GET    /api/positions           - List posisi (support ?division_id=X)
POST   /api/positions           - Tambah posisi
PUT    /api/positions/{id}      - Update posisi
DELETE /api/positions/{id}      - Hapus posisi
```

---

## 🎨 Frontend Components

### Modal Forms
1. **modalTambahKaryawan** - Add employee form
   - Two-column layout
   - Auto-populate divisions & positions
   - Photo upload with preview

2. **modalEditKaryawan** - Edit employee form
   - Same structure as add form
   - Pre-filled data
   - Display existing photo

3. **modalTambahDepartemen** - Add department form
   - Nested divisions container
   - Nested positions per division
   - Dynamic field addition/removal

4. **modalEditDepartemen** - Edit department form
   - Full nested structure edit
   - Drag-drop ready (future enhancement)

---

## 🔄 Data Flow & Synchronization

### Add Employee Flow:
1. User click "Tambah Karyawan"
2. Form opens (empty)
3. User select department → AJAX load divisions
4. User select division → AJAX load positions
5. User fill data + upload photo
6. User click "Simpan Data"
7. AJAX POST to `/api/employees`
8. **Success**: Both tables reload via `loadBothTables()` + SweetAlert2
9. **Error**: Show validation error in modal

### Edit Employee Flow:
1. User click edit button in table
2. AJAX fetch employee data
3. AJAX fetch department options
4. Auto-load divisions for department
5. Auto-load positions for division
6. Pre-fill form with employee data
7. Show existing photo
8. User edit data + optional new photo
9. User click "Perbarui Data"
10. AJAX PUT to `/api/employees/{nik}`
11. Same success/error handling as add

### Delete Employee Flow:
1. User click delete button
2. SweetAlert2 confirmation dialog
3. Show employee preview (photo, name, dept)
4. User confirm
5. AJAX DELETE to `/api/employees/{nik}`
6. Both tables auto-reload
7. SweetAlert2 success notification

---

## 🔧 JavaScript Functions

### Global Functions (Window Scope)
```javascript
window.loadKaryawanTable()          // Load & render employee table
window.renderKaryawanTable()        // Render table rows dynamically
window.loadDepartemenTable()        // Load & render department table
window.loadBothTables()             // Sync load both tables
window.editDept(id, name)           // Open edit department modal
window.hapusDept(id, name)          // Delete department with confirm
window.updateDept()                 // Save department changes
window.tambahFieldDivisiTambah()   // Add division field in form
window.tambahFieldDivisiEdit()     // Add division field in edit form
window.tambahFieldPositionTambah() // Add position field in form
window.tambahFieldPositionEdit()   // Add position field in edit form
```

### Helper Functions
```javascript
loadDivisionsForEdit()              // Load divisions for edit modal
loadPositionsForEdit()              // Load positions for edit modal
autoCloseAlert()                    // Auto-hide SweetAlert after delay
debounce()                          // Debounce search input
```

---

## 📱 UI/UX Features

### Notifications
- **SweetAlert2**: Major success/error (save, delete)
- **Alert HTML**: Inline alerts in modals
- **Console Logging**: Detailed debug logs (development)

### Loading States
- Disable button during AJAX
- Show "Menyimpan..." text
- Show "Loading..." in modals

### Validation
- Client-side: Check empty fields before submit
- Server-side: Laravel validation rules
- Display field-specific errors

### Responsive Design
- Bootstrap 5 grid system
- Mobile-friendly tables
- Modal responsive sizing
- Auto-stack dropdowns on mobile

---

## 🐛 Known Issues & Solutions

### Issue 1: CORS Error (FIXED)
**Problem**: CDN language file blocked by CORS
**Solution**: Use local language file at `public/assets/datatables/i18n/id.json`
**Status**: ✅ FIXED

### Issue 2: DataTables Sorting on Dynamic Data
**Current**: Tables rendered dynamically via AJAX
**Note**: DataTables might not sort/search correctly
**Future**: Consider server-side processing for large datasets

---

## 🚀 Deployment Checklist

- [ ] Run migrations: `php artisan migrate`
- [ ] Run seeders: `php artisan db:seed`
- [ ] Clear cache: `php artisan view:clear`
- [ ] Build assets: `npm run build`
- [ ] Set environment variables in `.env`
- [ ] Verify API routes accessible
- [ ] Test CRUD operations
- [ ] Check photo upload directory permissions
- [ ] Verify DataTables language file accessible
- [ ] Test on different browsers

---

## 📊 Database Relationships

```
User (employees)
├── belongsTo: Department
├── belongsTo: Division
├── belongsTo: Position
└── hasMany: Competencies

Department
└── hasMany: Divisions
    └── hasMany: Positions
        └── hasMany: Employees

Division
├── belongsTo: Department
└── hasMany: Positions
    └── hasMany: Employees

Position
├── belongsTo: Division
└── hasMany: Employees (as position)
```

---

## 🔐 Security Considerations

- CSRF Token validation on all forms
- Photo upload restrictions (size, type)
- Employee data encrypted in storage
- Password hashing for users
- Authorization checks on API endpoints
- Input validation on all forms

---

## 📝 File Structure

```
resources/views/
├── master-data.blade.php       (Main page - all modals & logic)
├── departments/
│   └── modals/
│       ├── edit-department.blade.php
│       └── detail-department.blade.php
└── employees/
    └── modals/
        └── edit-employee.blade.php

routes/
├── api.php                      (API endpoints for AJAX)
└── web.php                      (Web routes)

public/assets/datatables/i18n/
└── id.json                      (Language file - FIXED)
```

---

## 🎓 Usage Tips

1. **Always use Ctrl+Shift+R** for hard refresh (clear cache)
2. **Check console (F12)** for debug logs
3. **Use Firefox DevTools** for better AJAX inspection
4. **Test add/edit/delete** for each entity type
5. **Verify database** after operations with query
6. **Monitor API response** in Network tab

---

## 📞 Support & Maintenance

**Last Updated**: January 11, 2026
**Status**: ✅ Production Ready
**Known Issues**: None currently

For issues, check:
1. Browser console (F12) for errors
2. Laravel logs: `storage/logs/`
3. API response in Network tab
4. Database state with raw query

---

Generated: 2026-01-11
System Version: 1.0.0
