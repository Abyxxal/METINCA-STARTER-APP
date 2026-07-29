# LAPORAN PERUBAHAN — REALTIME DASHBOARD

## Tujuan
Dashboard admin otomatis update secara realtime tanpa refresh manual ketika ada perubahan data.

## File yang Diubah (4 file)

### 1. `app/Events/DashboardStatsUpdated.php`
- **Sebelum**: Hanya mengirim `pending_verification` dan `pending_approval`
- **Sesudah**: Mengirim 5 field statistik:
  - `total_employees` — jumlah karyawan aktif
  - `total_questions` — jumlah set soal aktif
  - `pending_verification` — ujian menunggu verifikasi
  - `pending_approval` — ujian menunggu approval manager
  - `active_exams_this_month` — ujian aktif bulan ini

### 2. `resources/views/admin/dashboard.blade.php`
- **Tambah ID** pada 4 kartu statistik untuk target JS:
  - `id="total-employees"` pada jumlah karyawan
  - `id="total-questions"` pada jumlah set soal
  - `id="pending-verification"` pada jumlah pending verifikasi
  - `id="active-exams"` pada jumlah ujian aktif
- **Tambah JS Pusher listener** di `@push('scripts')`:
  - Subscribe ke channel `admin.dashboard`
  - Bind event `App\Events\DashboardStatsUpdated`
  - Update keempat angka card secara realtime

### 3. `app/Http/Controllers/Admin/CBT/QuestionController.php`
- **Tambah use** `App\Events\DashboardStatsUpdated`
- **Tambah dispatch** di 5 method:
  - `store()` — setelah tambah soal baru (2 jalur: multiple & single)
  - `destroy()` — setelah hapus soal
  - `updateSet()` — setelah update set soal
  - `destroySet()` — setelah hapus set soal

### 4. `app/Http/Controllers/Admin/CBT/ExamController.php`
- **Tambah use** `App\Events\DashboardStatsUpdated`
- **Tambah dispatch** di 4 method:
  - `store()` — setelah buat ujian baru
  - `update()` — setelah update ujian
  - `destroy()` — setelah hapus ujian
  - `togglePublish()` — setelah publish/unpublish ujian

### 5. `app/Http/Controllers/Admin/CBT/ExamSessionController.php`
- **Tambah dispatch** di method:
  - `cancel()` — setelah batalkan penugasan ujian

### 6. `app/Http/Controllers/Admin/EmployeeImportController.php`
- **Tambah use** `App\Events\DashboardStatsUpdated`, `App\Events\EmployeeDataUpdated`
- **Tambah dispatch** di method:
  - `import()` — setelah import data karyawan

## Event sudah di-dispatch dari (total 21 titik)

| Controller | Method | Trigger |
|---|---|---|
| MasterDataController | create, update, delete | Tambah/edit/hapus karyawan |
| EmployeeImportController | import | Import Excel karyawan |
| QuestionController | store, destroy, updateSet, destroySet | Tambah/hapus/ubah set soal |
| ExamController | store, update, destroy, togglePublish | Tambah/hapus/ubah ujian |
| ExamSessionController | store, bulkAssign, cancel | Assign/batalkan sesi ujian |
| ExamSessionController | verify, approveLevel, rejectLevel | Verifikasi/approve/reject |
| Employee\ExamController | submitExam | Karyawan submit ujian |

## Arsitektur Real-time

```
User Action (create/update/delete)
       ↓
Controller → DashboardStatsUpdated::dispatch()
       ↓
Pusher/Reverb broadcast ke channel 'admin.dashboard'
       ↓
Dashboard browser → Pusher JS listener → update card angka
```

## Cara Kerja
1. Admin/tim melakukan aksi CRUD di sistem (tambah karyawan, buat ujian, verifikasi, dll)
2. Controller memanggil `DashboardStatsUpdated::dispatch()` setelah aksi berhasil
3. Event mengumpulkan data statistik terbaru
4. Pusher menyiarkan data ke semua browser admin yang sedang membuka dashboard
5. JavaScript listener memperbarui angka card tanpa refresh halaman
