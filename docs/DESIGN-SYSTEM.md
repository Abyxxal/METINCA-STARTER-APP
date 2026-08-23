# METINCA Admin — Design System

> Dokumen referensi resmi design language aplikasi (hasil redesign menyeluruh).
> **Single source of truth komponen**: `public/css/theme-overrides.css`
> **Reference implementation halaman**: `resources/views/admin/cbt/matrix/index.blade.php`

---

## 1. Fondasi Teknologi

| Lapisan | Pilihan | Catatan |
|---|---|---|
| Template admin | **Mazer** di atas **Bootstrap 5.3.x** | Jangan campur versi CSS vs JS |
| Ikon | **Bootstrap Icons saja** | FontAwesome & emoji sebagai ikon **dilarang** |
| Dialog | **SweetAlert2** | Native `alert()` / `confirm()` **dilarang** |
| Tabel data | jQuery **DataTables** | Sudah di-theme via overrides |
| Chart | **ApexCharts** | Wajib dark-aware (lihat §5) |
| Dark mode | `html[data-bs-theme="dark"]` | Semua warna lewat CSS variables Bootstrap |

## 2. Struktur Layout

| Layout | Pengguna | Karakter |
|---|---|---|
| `layouts/app` | Admin / Supervisor / Manager | Panel admin |
| `layouts/app-user` | Employee | Panel user + ikon iconly tersedia |
| `layouts/home` | Halaman publik company profile | Selalu light mode; gradient adalah branding — **di luar sistem ini** |

Konvensi Blade:
- Script halaman → `@push('scripts')`
- Style halaman → `@push('styles')`
- Footer: `2026 © PT Metinca`; logout via form + Swal

## 3. Token Warna Semantik

**Dilarang menulis hex mentah di markup/komponen.** Gunakan variabel Bootstrap yang otomatis dark-aware:

| Semantik | Token | Catatan |
|---|---|---|
| Surface utama | `--bs-body-bg`, `--bs-secondary-bg` | Latar kartu/KPI/tile |
| Surface lembut | `--bs-tertiary-bg` / class `bg-body-tertiary` | Pengganti `bg-white` & `bg-light` |
| Border | `--bs-border-color` | Semua garis & pemisah |
| Teks sekunder | `--bs-secondary-color` | Label kecil, keterangan |
| Primary | `#0d6efd` (dark: `#6ea8fe`) | Aksen, hover, aktif |
| Success | **`#198754`** (dark: `#75b798`) | Bukan legacy `#28a745` |
| Danger | `#dc3545` (dark: `#ea868f`) | Error, hapus |
| Warning | `#b58105` (dark: `#ffda6a`) | Perhatian |
| Info | `#0aa2c0` (dark: `#6edcff`) | Informasi |

### Level Kompetensi (global)

Didefinisikan di `theme-overrides.css :root` + override dark:

```
--lvl-0-bg/fg  abu    (belum ada)
--lvl-1-bg/fg  cyan   (Novice)
--lvl-2-bg/fg  kuning (Competent)
--lvl-3-bg/fg  biru   (Proficient)
--lvl-4-bg/fg  hijau  (Expert)
```

### Pola Token Ter-Scope (per halaman)

Halaman dengan style privat memakai wrapper id, memetakan token privat ke variabel BS — **bukan** mendefinisikan hex di `:root` global:

```css
#qaScope {
    --surface: var(--bs-body-bg);
    --line: var(--bs-border-color);
    --primary-soft: rgba(var(--bs-primary-rgb), .12);
}
html[data-bs-theme="dark"] #qaScope { /* penyesuaian shadow dsb */ }
```

## 4. Komponen Shared (`public/css/theme-overrides.css`)

Dimuat **setelah** app.css/app-dark.css di kedua layout admin.

### `.empty-state` — kondisi kosong
Ikon lingkaran 72px + judul + subjudul, border dashed, bg tertiary. Varian `.empty-state.warning`.
> **Wajib** untuk tabel/list yang bisa kosong — jangan biarkan area blank.

```blade
<div class="empty-state">
    <i class="bi bi-inbox"></i>
    <h5>Judul</h5>
    <p class="text-muted mb-0">Penjelasan singkat.</p>
</div>
```

### `.kpi-card` + `.kpi-icon` — kartu statistik
Flex: chip ikon + nilai + label.

```blade
<a href="{{ route('...') }}" class="kpi-card">   {{-- varian klikabel --}}
    <i class="bi bi-people-fill kpi-icon kpi-primary"></i>
    <div>
        <div class="kpi-value" id="total-x">{{ $nilai }}</div>
        <div class="kpi-label">Label KPI</div>
    </div>
</a>
```

Varian chip: `kpi-primary / info / success / warning / danger / secondary` — bg soft `rgba(warna,.12)` dengan override dark masing-masing.
Ikon **selalu langsung pada `<i>`** (jangan dibungkus `<span>`) agar glyph ter-center sempurna.

### `.quick-tile` — tile Quick Access
Flex column center; `height:100%` + `min-height:112px` → tinggi seragam antar tile, label panjang tetap aman (wrap tanpa merusak grid). Hover/focus identik pola `a.kpi-card`.

```blade
<a href="..." class="quick-tile">
    <i class="bi bi-database-fill kpi-icon kpi-primary"></i>
    <span class="quick-tile-label">Master Data</span>
</a>
```

### `.card-header` — normalisasi
`display:flex; flex-wrap; gap:.5rem 1rem; justify-content:space-between`.
> **Anti-pattern: header kartu solid berwarna** (`bg-primary text-white`, `bg-light-*` sebagai header).

### `.legend-chip` — filter interaktif
Pill dengan border token; state `.active` = fill primary. Sudah mendukung `focus-visible`.

### DataTables
Input filter, select length, paginate, dan head tabel sudah mengikuti token tema — tidak perlu styling manual per halaman.

## 5. Pola Halaman

### Spacing
```blade
<section class="row gy-4 gx-4">      {{-- antar grup vertikal/horizontal --}}
    <div class="col-12">
        <div class="row g-3"> ... KPI cards ... </div>   {{-- baris KPI --}}
    </div>
</section>
```
Jangan andalkan margin bawaan Mazer (`.card{margin-bottom:2.2rem}`) sebagai satu-satunya pemisah — grup non-card (mis. strip KPI) akan menempel.

### Chart dark-aware (ApexCharts)
```js
function chartTheme() {
    var dark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    return { bar: dark ? '#6ea8fe' : '#435ebe', label: ..., gridBorder: ..., rowColors: ... };
}
// + MutationObserver pada atribut data-bs-theme → chart.updateOptions(buildChartOptions())
```

### Badge status
- Soft badge Mazer (`bg-light-info`, `bg-light-success`, dst) untuk **semantik status** — sudah theme-aware.
- `bg-*` solid untuk state kuat (danger, approved).
- Badge ikon-saja wajib punya `title` + `aria-label`; ikon dekoratif `aria-hidden="true"`.

### Aksesibilitas
- Focus ring jangan dihapus; gunakan `focus-visible`.
- Target sentuh ≥ 44×44px (tile min-height 112px ✓).
- Kontras teks mengikuti pasangan token di atas.

## 6. Aturan SweetAlert2

Konstanta global (terdefinisi di **kedua layout**, setelah CDN Swal):

```js
window.SWAL_BTN = { danger: '#dc3545', success: '#198754', cancel: '#6c757d' };
```

Semua konfirmasi wajib:
```js
Swal.fire({
    icon: 'warning',
    title: 'Yakin?',
    showCancelButton: true,
    confirmButtonColor: SWAL_BTN.danger,
    cancelButtonColor: SWAL_BTN.cancel
}).then(r => { if (r.isConfirmed) { /* aksi */ } });
```

**Swal tidak blocking** (beda dengan `confirm()`):
- Flow berbasis timer/interval → wajib guard-flag + `clearInterval()` sebelum menampilkan Swal.
- Submit form pasca-konfirmasi → `e.preventDefault()` selalu dipanggil, submit ulang lewat `form.requestSubmit(btn)` di dalam `.then()` (menghormati `formaction`).

## 7. Pengecualian Hardcode yang Disengaja

| Konteks | Alasan |
|---|---|
| Dokumen cetak (`*.print.blade.php`) | Fixed light untuk kertas, tidak ikut dark mode |
| Config chart JS (ApexCharts) | Library butuh nilai konkret; sudah dark-aware via ternary |
| `SWAL_BTN` di layout | Single source of truth warna Swal |
| Halaman publik `home/*` dan `layouts/home` | Branding marketing, selalu light |

## 8. Anti-Pattern (Larangan)

1. ❌ Hex mentah di blade/CSS komponen → pakai token (kecuali daftar §7)
2. ❌ `bg-white` / `bg-light` → pakai `bg-body-tertiary` / `bg-body-tertiary` setara token
3. ❌ Header kartu solid berwarna (`bg-primary text-white`)
4. ❌ Native `alert()` / `confirm()`
5. ❌ Font Awesome / emoji sebagai ikon
6. ❌ Inline `style="color/background..."` → buat class di theme-overrides atau pakai utility BS
7. ❌ Dua hijau berbeda — success hanya `#198754`
8. ❌ Mengandalkan margin bawaan untuk spacing antar section
9. ❌ Ikon dalam chip dibungkus elemen lain (break centering) — kelas langsung di `<i>`
10. ❌ Tabel/list kosong tanpa `.empty-state`

---

## 9. Galeri Snippet Siap Pakai

> Semua snippet di bawah adalah **kode asli yang dipakai di aplikasi** — salin apa adanya untuk hasil visual identik.

### 9.1 Kerangka Halaman Admin

```blade
@extends('layouts.app')

@section('title', 'Judul Halaman')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Judul Halaman</h3>
                <p class="text-subtitle text-muted">Deskripsi singkat halaman.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Judul Halaman</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <section class="row gy-4 gx-4">
        {{-- grup-grup konten di sini --}}
    </section>
</div>
@endsection

@push('styles')
<style>/* style ter-scope halaman (§9.11) */</style>
@endpush

@push('scripts')
<script>/* js halaman */</script>
@endpush
```

### 9.2 Baris KPI (kartu klikabel)

```blade
<div class="col-12">
    <div class="row g-3">
        <div class="col-6 col-lg-3">
            <a href="{{ route('master-data') }}" class="kpi-card" aria-label="Total karyawan: {{ $total }}">
                <i class="bi bi-people-fill kpi-icon kpi-primary"></i>
                <div>
                    <div class="kpi-value">{{ $total }}</div>
                    <div class="kpi-label">Total Karyawan Aktif</div>
                </div>
            </a>
        </div>
        {{-- ulangi dengan kpi-success / kpi-warning / kpi-danger / kpi-info --}}
    </div>
</div>
```

Varian non-klikabel: ganti `<a>` menjadi `<div>` (tanpa `href`).

### 9.3 Kartu Header Standar

```blade
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Judul Kartu</h4>
        <a href="{{ route('...') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah
        </a>
    </div>
    <div class="card-body">
        {{-- konten --}}
    </div>
</div>
```

Header **tanpa** kelas warna — normalisasi flex dari theme-overrides yang menata judul vs aksi.

### 9.4 Grid Quick Tile

```blade
<div class="row g-3">
    <div class="col-6">
        <a href="{{ route('master-data') }}" class="quick-tile">
            <i class="bi bi-database-fill kpi-icon kpi-primary"></i>
            <span class="quick-tile-label">Master Data</span>
        </a>
    </div>
    {{-- 3 tile lainnya; tinggi seragam otomatis --}}
</div>
```

### 9.5 Empty State

```blade
{{-- standar --}}
<div class="empty-state">
    <i class="bi bi-inbox"></i>
    <h5>Belum Ada Data</h5>
    <p class="text-muted mb-0">Data akan tampil setelah ditambahkan.</p>
</div>

{{-- varian peringatan --}}
<div class="empty-state warning"> ... </div>

{{-- di dalam <tbody> tabel --}}
<tr><td colspan="99" class="p-0"><div class="empty-state m-3"> ... </div></td></tr>
```

### 9.6 Badge Status

```blade
{{-- soft badge (semantik status, dark-aware) --}}
<span class="badge bg-light-info">Menunggu Verifikasi</span>

{{-- state kuat --}}
<span class="badge bg-success">Disetujui</span>
<span class="badge bg-danger">TIDAK LULUS</span>

{{-- ikon-saja: wajib label aksesibilitas --}}
<span class="badge bg-success" title="Lulus" aria-label="Lulus">
    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
</span>
```

### 9.7 Konfirmasi Hapus (Swal)

```js
document.querySelectorAll('.form-delete').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Hapus data ini?',
            text: 'Tindakan ini tidak dapat dibatalkan.',
            showCancelButton: true,
            confirmButtonColor: SWAL_BTN.danger,
            cancelButtonColor: SWAL_BTN.cancel,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(function (r) {
            if (r.isConfirmed) form.submit();
        });
    });
});
```

Pesan sukses / gagal setelah aksi:
```js
Swal.fire({ icon: 'success', title: 'Berhasil', text: msg, timer: 1500, showConfirmButton: false });
Swal.fire({ icon: 'error', title: 'Gagal', text: msg, confirmButtonColor: SWAL_BTN.danger });
```

### 9.8 Submit Form Pasca-Konfirmasi (pola non-blocking)

`preventDefault()` **selalu** dipanggil; submit ulang hanya saat dikonfirmasi — aman karena Swal tidak memblokir event loop:

```js
document.getElementById('frmUtama').addEventListener('submit', function (e) {
    e.preventDefault();
    var form = this;
    Swal.fire({
        icon: 'question',
        title: 'Simpan perubahan?',
        showCancelButton: true,
        confirmButtonColor: SWAL_BTN.success,
        cancelButtonColor: SWAL_BTN.cancel,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal'
    }).then(function (r) {
        if (r.isConfirmed) form.requestSubmit();
    });
});
```

### 9.9 Auto-Submit Timer dengan Guard (kasus halaman ujian)

Wajib guard-flag + `clearInterval` sebelum menampilkan Swal agar interval tidak menembak ulang saat dialog terbuka:

```js
var autoSubmitted = false;
var tick = setInterval(function () {
    if (sisaDetik <= 0 && !autoSubmitted) {
        autoSubmitted = true;
        clearInterval(tick);
        Swal.fire({
            icon: 'info',
            title: 'Waktu Habis',
            text: 'Jawaban Anda akan dikirim otomatis.',
            timer: 3000,
            showConfirmButton: false
        }).then(function () { document.getElementById('frmUjian').submit(); });
    }
}, 1000);
```

### 9.10 ApexCharts Dark-Aware (template)

```js
function chartTheme() {
    var dark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    return {
        bar: dark ? '#6ea8fe' : '#435ebe',
        label: dark ? '#ced4da' : '#304758',
        gridBorder: dark ? '#495057' : '#e7e7e7',
        rowColors: dark ? ['rgba(255,255,255,.04)', 'transparent'] : ['#f3f3f3', 'transparent']
    };
}

var chart = new ApexCharts(el, { /* options */ colors: [chartTheme().bar], /* ... */ });
chart.render();

// re-render otomatis saat toggle dark/light
new MutationObserver(function () {
    chart.updateOptions(buildOptions());
}).observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });
```

Warna chart adalah pengecualian hardcode yang disengaja (§7) — sudah dark-aware via ternary.

### 9.11 Skeleton Token Ter-Scope (halaman dengan style privat)

Letakkan di `@push('styles')`; petakan semua token privat ke variabel Bootstrap agar dark mode otomatis:

```css
#namaHalamanScope {
    --surface: var(--bs-body-bg);
    --surface-soft: var(--bs-tertiary-bg);
    --text: var(--bs-emphasis-color);
    --muted: var(--bs-secondary-color);
    --line: var(--bs-border-color);
    --primary-soft: rgba(var(--bs-primary-rgb), .12);
    --shadow: 0 10px 30px rgba(15, 23, 42, .08);
}
html[data-bs-theme="dark"] #namaHalamanScope {
    --shadow: 0 10px 30px rgba(0, 0, 0, .35);
}
```

Contoh penerapan penuh: `admin/cbt/sessions/qualitative-assessment.blade.php` (`#qaScope`).

### 9.12 Legend Chip (filter interaktif)

```blade
<button type="button" class="legend-chip active" data-filter="semua">Semua</button>
<button type="button" class="legend-chip" data-filter="lulus">
    <span class="legend-dot" style="background: var(--lvl-4-bg);"></span> Expert
</button>
```

State `.active` = fill primary; sudah mendukung `focus-visible`.

---

*Terakhir diperbarui: Agustus 2026 — Fase 0–5 + galeri snippet §9.*
