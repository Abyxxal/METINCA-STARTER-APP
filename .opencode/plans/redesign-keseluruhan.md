# Rencana: Redesign Keseluruhan Aplikasi (~52 File Blade)

## Keputusan Terkunci (dari sesi tanya-jawab)

| Aspek | Keputusan |
|---|---|
| Cakupan | Seluruh aplikasi (±59 view), **kecuali** `admin/employees/index.blade.php` (orphan dummy tanpa route — dilewati) |
| Arah visual | Konsisten tema existing (Mazer + Bootstrap 5.3), pola seragam ala redesign Matriks Kompetensi |
| Urutan | Dashboard → Master Data/Admin root → Modul CBT Admin → Halaman User → Public/Auth |
| Dialog | Semua `alert()`/`confirm()` native → SweetAlert2 (sudah termuat global) |
| Layout user | Split `app` vs `app-user` dipertahankan, hanya dirapikan |
| DataTables | Dipertahankan + tema CSS seragam + dark mode |

## Standar Desain (acuan: admin/cbt/matrix/index.blade.php)

- Kartu: header flex judul kiri / aksi kanan; dilarang header berwarna solid (`bg-primary text-white`)
- Tabel: `.table-responsive`; DataTables diberi tema bersama
- Empty state: lingkaran ikon + judul + subjudul
- KPI: chip ikon berwarna + nilai + label
- Dark mode: dilarang `bg-white`/`bg-light` hardcoded pada permukaan adaptif → pakai `bg-body`, `bg-*-subtle`, atau CSS var di bawah `html[data-bs-theme="dark"]`
- Ikon: Bootstrap Icons saja (buang FontAwesome/Iconly yang tercampur)

## Infrastruktur Bersama (baru)

**File:** `public/css/theme-overrides.css` — berisi:
1. Token warna level `--lvl-0..4-bg/fg` (light + dark)
2. Komponen `.empty-state`, `.kpi-card/.kpi-icon`, `.legend-chip`
3. Normalisasi `.card-header`
4. Tema lengkap jQuery DataTables (filter input, length select, paginate button, thead) theme-aware
5. Ukuran logo sidebar (dipindah dari inline hack layout)
Di-enqueue sekali di `layouts/app.blade.php` dan `layouts/app-user.blade.php`.

---

## Fase 0 — Fondasi Layout (2 file)

### layouts/app.blade.php
1. Hapus tag `</header>` duplikat (L367–368)
2. Footer "Sistem Informasi Universitas Darma Persada" / "Your Name" / link mati → branding PT Metinca
3. Sidebar logo `<a href="index.html">` (L41) → `{{ route('dashboard') }}`
4. Hapus komentar liar `<!-- Need: Apexcharts -->` sebelum `</body>`
5. Enqueue `theme-overrides.css`; hapus `<style>` hack logo inline (sudah pindah)

### layouts/app-user.blade.php
1. Samakan struktur topbar dengan app: elemen `<header class="topbar">`, burger `bi-justify`, pola JS event listener (bukan onclick inline)
2. Hapus badge notifikasi palsu hardcoded "3" + dua item notifikasi statis
3. Enqueue `theme-overrides.css`; hapus `<style>` hack logo inline

## Fase 1 — Dashboard (2 file)

### admin/dashboard.blade.php
1. Kartu KPI → komponen `.kpi-card` chip ikon
2. ApexCharts theme-aware: baca token dari CSS var saat render + re-render saat toggle dark (`document.documentElement.getAttribute('data-bs-theme')`); gridline/label jangan hardcode `#e7e7e7`/`#304758`
3. Empty state → komponen standar

### admin/machining/monitoring/index.blade.php
Stub 45 baris: KPI hardcoded "16" → markup kpi-card konsisten.

## Fase 2 — Admin Root (±13 file)

### admin/master-data.blade.php (3.909 baris — terparah)
1. Pindahkan blok `<style>` ilegal di tengah body (L111–119) ke head/scope
2. Override global `.nav-link.active { color:#6366f1 !important }` → scope ke container halaman atau hapus
3. Ikon tab hex `#6366f1`/`#8b5cf6` → kelas utilitas/var
4. Kotak `bg-white rounded border` dari template JS (L1427, L1527, L1879, L2332) → kelas theme-aware
5. Override `.modal-backdrop` global (L50–54) → scope/hapus

### admin/report-and-audit.blade.php (51 inline style)
1. Sel matrix sticky `#f8f9fa` (L35–42) + palet BS4 hex (L43–71) → token matriks
2. `<tbody style="background-color: white">` (L232) → hapus, pakai kelas tabel
3. KPI level cards inline div ×5 (L177–205) + legend bullet hex (L212–214) → komponen standar
4. Tema DataTables otomatis dari css bersama

### departments (index + show + 2 modal)
1. Header modal/kartu berwarna solid (`bg-primary/warning/danger/success text-white`) → header kartu standar + badge status bila perlu
2. Native `alert()` semua CRUD (index L159–230, show L286/296) → SweetAlert2
3. `bg-light` card-header/table-head → kelas theme-aware
4. show.blade konten demo hardcoded + hex → restyle saja (data demo dibiarkan)

### employees/modals (create + edit)
1. create: ~27 atribut `style=` pengganti Bootstrap → hapus, andalkan kelas form-control/form-label bawaan; gradient header inline → standar
2. edit: header `bg-warning text-dark` → standar; disabled input hex `#e9ecef` → hapus; mojibake glyph opsi status → teks bersih

### user-management + employee-import
1. employee-import: Font Awesome → Bootstrap Icons; `h1.h3` → pola page-heading + breadcrumb; header `bg-light` → standar
2. user-management: empty state → komponen standar

### Prototipe (settings, material-management, evaluation-and-exam) — RESTYLE SAJA
1. Inline `<style>` light-only (#e9ecef/#f8f9fa) → kelas theme-aware
2. Tombol aksi solid campur → outline konsisten
3. Data demo hardcoded DIBIARKAN (perubahan fungsional di luar lingkup)
4. Tema DataTables otomatis

## Fase 3 — Modul CBT Admin (16 file)

### questions ×6
1. `confirm()` native → SweetAlert2 (index L164, edit-set L367/L534, show-set L69/L144)
2. `alert()` validasi create (L350) → Swal toast/error
3. Header dinamis JS `bg-light` (create L199, edit L287, edit-set L156/L444, show-set L87) → kelas theme-aware
4. edit L118 header `bg-primary text-white` → header kartu standar
5. show L47 toggle baris opsi `bg-light`/`bg-success text-white` → varian subtle

### sessions ×6
1. show: kotak jawaban esai `bg-white p-3` (L132) → theme-aware; header `bg-light-info/warning/primary` (L127/L290/L328) → subtle utilities BS 5.3; header modal approve/reject solid (L360/L389) → standar + ikon; unicode ✓/✗ dalam badge (L104/L301) → `bi bi-check/x`
2. create: sticky thead `bg-light` (L99/L191) → `bg-body`; inline max-height style rapikan
3. index: confirm cancel (L217) → Swal; info box modal `bg-light` (L265) → subtle
4. pending/pending-approval: empty state → komponen standar; thead `table-light` → theme-aware
5. qualitative-assessment.blade.php (rework besar):
   - Buang design system privat light-only (`:root --bg/--surface` L5–22 + `body{background:var(--bg)!important}` L24–26) → adopsi token global
   - Modal buatan tangan `.qa-modal-overlay` putih (L880–960) → modal Bootstrap standar
   - `alert()/confirm()` approve/reject (L1030–1052) → Swal
   - Breadcrumb/header custom → pola page-heading + kartu standar

### exams ×4
1. show: KPI box `text-center p-3 bg-light rounded` ×4 (L53–71) → `.kpi-card`
2. create/edit: badge template JS + inline width hacks → kelas
3. index: sudah dekat referensi — cukup konsistensi kecil

### competencies ×2 + division-skills ×1
1. Pindahkan modal yang dideklarasikan di dalam `<tbody>` loop (competencies L129–169, division-skills L141–199) keluar tabel — perbaikan validitas HTML
2. division-skills: badge divisi aktif `bg-white text-primary` (L61) → theme-aware; thead `table-light` → adaptif; badge kode `bg-light-primary` → subtle utility
3. competencies/edit: tombol edit `btn-warning` solid → outline; confirm reset (L117) → Swal
4. Empty state minimal (competencies L73–75) → komponen standar

## Fase 4 — Halaman User (12 file, split layout dipertahankan)

### user root ×5 (app-user)
1. employee-dashboard: kartu `stats-icon` lama → `.kpi-card`; Iconly → BI
2. my-training/training-history/my-competencies/my-profile: badge `bg-light-*` → subtle utilities; progress bar inline height → kelas; `card bg-light` (my-competencies L75) → kartu standar; FA timeline icons (my-profile L165+) → BI; header `bg-light` → standar

### user/cbt ×7
1. dashboard: header banner `bg-warning text-dark` (L29) → kartu warning subtle; mini-cards `bg-light` (L209) → theme-aware; empty states plain → komponen standar
2. take-exam: inline `<style>` hex (#198754/#0d6efd) → var; **native alert/confirm pada alur kritis** (time-up auto-submit L258, incomplete-submit L358/L419/L427) → Swal dengan callback identik (hati-hati, high-stakes)
3. result: panel skor `bg-light rounded p-4` (L93/L104) → theme-aware; header `bg-light-info` (L217) → subtle
4. exam-info: 5 tile info `bg-light` (L47–78) → komponen konsisten
5. exam-preview: header `bg-primary text-white` (L30) → standar
6. history/competencies: polish empty state → komponen standar

## Fase 5 — Public & Auth (7 file, sentuhan ringan)

### layouts/home.blade.php
1. Sinkron versi Bootstrap (CSS 5.3.0 vs JS 5.3.8) → satu versi
2. Buang salah satu lib ikon (pertahankan Bootstrap Icons)
3. Modal "Metinca Apps" ~467 baris: ~20 gradien inline → kelas CSS; link `href="#"` mati dibiarkan (konten publik, bukan lingkup)

### home ×5
main/divisions/facilities/products/galleries: FA→BI konsisten; gradien header divisions inline (L79/L217/L335/L453) → kelas; sisanya kosmetik ringan

### auth ×2
Standalone diterima. Minor: link `/home` hardcoded register (L116) → helper url jika route ada.

---

## Verifikasi

- Setelah tiap fase: `php artisan view:clear && php artisan view:cache` (kompilasi semua blade — menangkap sintaks error tanpa render browser, sesuai preferensi user)
- Verifikasi akhir sama, plus laporan ringkas per fase
- Spot-check visual manual oleh user; smoke test HTTP opsional hanya bila diminta

## Di Luar Lingkup

- Perubahan fungsional: route, controller, query, penghapusan data demo
- Integrasi orphan `admin/employees/index.blade.php`
- Penyatuan split layout user
- Migrasi jQuery DataTables → plain table
- Dokumen cetak (sudah selesai sebelumnya)
