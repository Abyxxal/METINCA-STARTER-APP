# Division Feature - Test Checklist

## Perubahan yang dilakukan:

### 1. Standardisasi Naming Convention
**Lama (Inconsistent):**
- `.input-nama-divisi`, `.input-deskripsi-divisi` (add mode)
- `.input-edit-nama-divisi`, `.input-edit-deskripsi-divisi` (edit mode)
- `.input-new-nama-divisi`, `.input-new-deskripsi-divisi` (edit new)
- `.edit-divisi-item`, `.edit-divisi-item-new` (edit containers)
- `.btn-hapus-divisi-baru`, `.btn-cancel-divisi-baru`, `.btn-hapus-divisi-edit`

**Baru (Consistent):**
- `.divisi-nama`, `.divisi-deskripsi` (all modes)
- `.divisi-new` (untuk divisi belum disimpan)
- `.divisi-existing` (untuk divisi sudah di DB)
- `.btn-remove-divisi` (remove unsaved)
- `.btn-delete-divisi` (delete saved)
- `data-temp-id` (untuk divisi baru)
- `data-id` (untuk divisi existing)

### 2. Event Delegation Handlers
Semua handler menggunakan:
```javascript
$(document).on('click', '#selectorId', function(e) { ... })
```

Bukan:
```javascript
$('#selectorId').on('click', function(e) { ... })
```

### 3. Added Console Logging
Untuk debugging, semua handler sekarang log ke console dengan `.preventDefault()` di awal.

---

## Test Cases untuk Diverifikasi

### Add Departemen Modal
- [ ] Buka modal "Tambah Departemen"
- [ ] Masukkan nama departemen
- [ ] Klik "Tambah Divisi" → row baru muncul
- [ ] Masukkan nama divisi
- [ ] Klik X pada divisi → row hilang
- [ ] Tambah divisi lagi, masukkan data
- [ ] Klik "Simpan" → seharusnya departemen dan divisi tercipta

### Edit Departemen Modal - Load Data
- [ ] Buka master-data page
- [ ] Lihat list departemen di tabel
- [ ] Klik tombol "Edit" pada salah satu departemen
- [ ] Modal edit muncul dengan nama departemen
- [ ] **PENTING:** Container divisi harus menampilkan divisi yang sudah ada (jika ada)
- [ ] Check browser console → seharusnya tidak ada error

### Edit Departemen Modal - Tambah Divisi Baru
- [ ] Klik "Tambah Divisi Baru" button
- [ ] Row input baru muncul
- [ ] Masukkan nama divisi
- [ ] Klik X → row hilang
- [ ] Tambah lagi beberapa divisi
- [ ] Check console: `console.log('Divisi list:', newDivisiList)` harus show data
- [ ] Klik "Update" → divisi baru seharusnya tersimpan

### Edit Departemen Modal - Hapus Divisi Existing
- [ ] Buka edit departemen yang punya divisi
- [ ] Lihat list divisi yang ada
- [ ] Klik tombol trash pada salah satu divisi
- [ ] Konfirmasi dialog muncul
- [ ] Klik "Ya, Hapus"
- [ ] Divisi hilang dari list dan dari database

### Network Check (F12 DevTools)
- [ ] Tab "Network" → klik Tambah Divisi
- [ ] POST `/api/divisions` should succeed (status 201 atau 200)
- [ ] DELETE `/api/divisions/{id}` should succeed (status 200)
- [ ] GET `/api/divisions?department_id=X` should have divisions

### Console Check (F12 DevTools)
- [ ] Tidak ada error merah
- [ ] Console.log messages should appear:
  - `'Loading divisions for department: X'`
  - `'Divisions response:', {success, data}`
  - `'Tambah divisi edit button clicked'`
  - `'Delete divisi button clicked'`
  - `'Divisi list:', [...]`

---

## Debugging Tips

Jika button tidak merespons:
1. Open F12 → Console
2. Type: `$('#btnTambahDivisiEdit').length` → should return 1
3. Type: `$('#editContainerDivisi').length` → should return 1
4. Click button dan lihat console messages

Jika data tidak terkirim:
1. Open F12 → Network tab
2. Click button
3. Check POST request payload
4. Verify `department_id` ada dan benar

---

## Status Akhir
✅ Semua handler sudah direbuild dengan naming konsisten
✅ Event delegation sudah diterapkan di semua handler
✅ Console logging sudah ditambahkan untuk debugging
✅ Selector sudah standardisasi di add dan edit modal

**SIAP UNTUK TESTING DI BROWSER**
