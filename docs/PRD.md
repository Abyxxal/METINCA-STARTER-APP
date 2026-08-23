# PRD — METINCA Competency & Training System

> **Product Requirements Document (As-Built)** — mendokumentasikan aplikasi yang sedang berjalan.
> Disusun dari kode aktual (routes, models, controllers, views). Referensi teknis terkait:
> - Design system: [`docs/DESIGN-SYSTEM.md`](DESIGN-SYSTEM.md)
> - Reference implementasi UI: `resources/views/admin/cbt/matrix/index.blade.php`

---

## 1. Ikhtisar Produk

**METINCA Competency & Training System** adalah aplikasi web internal PT Metinca Prima Industrial Works untuk mengelola siklus **pelatihan → ujian kompetensi (CBT) → verifikasi → approval manajer → sertifikasi level kompetensi** karyawan.

**Masalah yang diselesaikan:**
1. Standardisasi kompetensi antar divisi sulit dipantau tanpa sistem (dijawab dengan *Competency Matrix* dan standar `DivisionSkill`).
2. Pengujian kompetensi manual tidak terekam jejaknya (dijawab dengan CBT: penugasan, pengerjaan ter-timer, autosave jawaban, verifikasi ganda admin + manajer).
3. Sertifikasi level karyawan butuh validasi dua lapis (kuantitatif oleh supervisor/admin, kualitatif oleh manager).

**Bentuk produk:** aplikasi monolitik Laravel dengan panel admin Mazer, halaman self-service karyawan, dan company profile publik.

---

## 2. Peran & Hak Akses

| Peran | Nilai role | Middleware | Cakupan akses |
|---|---|---|---|
| **Admin / Supervisor** | `admin` (alias supervisor) | `is.admin` | Seluruh panel admin: master data, bank soal, paket ujian, penugasan, **verifikasi hasil**, matriks kompetensi, laporan |
| **Manager** | `manager` | `is.admin` + `is.manager` | Semua akses admin **kecuali** modul approval eksklusif: antrean persetujuan (`cbt/admin/approval`), assessment kualitatif, approve/reject level, riwayat approval |
| **Employee / Karyawan** | `user` | `is.user`, relasi wajib ke `employees.nik` | Self-service: ujian saya, daftar & kerjakan ujian, hasil, riwayat, kompetensi saya, profil |
| **Guest** | — | — | Company profile publik + registrasi/login |

Catatan hak akses penting:
- `IsAdmin` mengizinkan **admin DAN manager** masuk panel admin; `IsManager` hanya `manager`.
- Karyawan tanpa data `Employee` terhubung akan diarahkan kembali ke dashboard dengan pesan error.
- Login menggunakan **`email_or_nik`** (email akun atau NIK karyawan).

## 3. Modul & Fitur

### 3.1 Company Profile Publik
| Halaman | Route | Isi |
|---|---|---|
| Home | `home.main` (`/home`) | Landing perusahaan + modal "Metinca Apps" |
| Divisions | `/home/divisions` | Profil 4 divisi produksi (Investment Casting, Sand Casting, Valve, Permanent Mould) |
| Facilities | `/home/facilities` | Fasilitas pabrik per kategori |
| Products | `/home/products` | Katalog produk dengan filter |
| Gallery | `/home/gallery` | Galeri foto |

Halaman publik selalu light mode, desain marketing terpisah dari design system admin.

### 3.2 Autentikasi
- Login (`login.store`): email **atau** NIK + password; remember me.
- Registrasi (`register.store`) + verifikasi email (route Laravel standar).
- Reset password (kirim link → form reset).
- Logout via POST + konfirmasi Swal.

### 3.3 Dashboard Role-Aware (`/dashboard`)
Satu controller memilih konten berdasar role:
- **Admin & Manager**: 4 kartu KPI klikabel (Total Karyawan Aktif; slot kedua role-aware — *Pending Persetujuan* untuk manager / *Total Soal Aktif* untuk supervisor; Pending Verifikasi; Ujian Aktif bulan ini), grafik rata-rata nilai per skill (ApexCharts dark-aware, garis KKM 70%), aktivitas terakhir (5 sesi), quick access tiles (manager melihat tile *Riwayat Persetujuan*), overview passing rate per skill.
- **Karyawan**: statistik pelatihan aktif/selesai/berjalan/sertifikat + 3 sesi terakhir.
- **Realtime**: channel broadcast `admin.dashboard` (event `DashboardStatsUpdated`) memperbarui angka KPI tanpa reload (Pusher/Reverb).

### 3.4 Master Data (halaman SPA-tab `master-data` + API `api/*`)
- **Departemen** → **Divisi** (per departemen) → **Posisi** (per divisi): CRUD penuh via AJAX.
- **Karyawan**: CRUD + reset password + **import Excel massal** (`employee-import`, unduh template) + hubungkan akun User.
- **Skill**: CRUD kompetensi teknis (kode, nama, level 1–4).
- Dropdown bertingkat tersedia via `api/dropdowns/*`.
- Manajemen akun aplikasi (`admin/users`): ubah role user.

### 3.5 CBT — Bank Soal (`cbt/admin/questions`)
- Soal dikelompokkan dalam **set soal** (`question_set_id`): buat set, kelola isi set (edit-set/show-set), hapus set.
- Tipe soal: **pilihan ganda** (4 opsi + kunci) dan **esai**.
- Setiap soal punya **bobot** dan skill pemilik; status aktif/nonaktif.

### 3.6 CBT — Paket Ujian (`cbt/admin/exams`)
- Paket ujian terikat **skill** + **target level (1–4)** + **KKM/passing score (%)** + durasi menit.
- Pemilihan soal dari bank per skill (AJAX `questions-by-skill`); soal disimpan sebagai **snapshot beserta bobot** saat dibuat (`attachQuestionsWithSnapshot`) sehingga perubahan bank soal tidak merusak paket yang berjalan.
- **Publish/unpublish** (`toggle-publish`); hanya paket published yang terlihat karyawan.

### 3.7 CBT — Penugasan Sesi (`cbt/admin/sessions`)
- Assign ujian ke karyawan: individual atau **massal per divisi** (`bulk-assign`).
- Setiap sesi memiliki `scheduled_start_at` (belum bisa dikerjakan sebelum jadwal) dan `deadline_at` (terlewat = terkunci) serta indikator status deadline (label + class alert).
- Pembatalan sesi (`cancel`). Monitoring seluruh sesi di halaman index; halaman **Pending** khusus antrean verifikasi.

### 3.8 CBT — Verifikasi Supervisor/Admin
- Antrean `cbt/admin/sessions/pending`: sesi berstatus *submitted*.
- Detail hasil per karyawan: PG dinilai otomatis; **esai dikoreksi manual** (skor 0–bobot per soal + catatan verifikator).
- Aksi `verify` → `verified_pass` (*Lulus - Menunggu Approval*) atau `verified_fail`.
- Catatan admin (`admin_notes`) tersimpan di sesi.

### 3.9 CBT — Approval Manajer (eksklusif manager)
- Antrean `cbt/admin/approval` → halaman **Validasi Kompetensi Karyawan** (`assessment`): hasil kuantitatif (skor ring vs KKM) + **penilaian kualitatif**:
  - **5 kriteria**: Pemahaman & penerapan SOP; Penerapan kompetensi di tempat kerja; Kemandirian; Problem solving; Kesiapan level berikutnya.
  - Skala tiap kriteria: *Memenuhi* = 2, *Perlu Perbaikan* = 1, *Tidak Memenuhi* = 0 → **maksimum 10**.
  - Metode: interview / observasi / keduanya.
  - **Threshold approval: skor ≥ 7.**
- Keputusan: **Setujui** → level kompetensi naik ke target level ujian & sesi `approved`; **Tolak** → `rejected` + catatan manajer.
- Riwayat: `approval-history`.

### 3.10 Kompetensi

| Fitur | Route | Deskripsi |
|---|---|---|
| Matriks Kompetensi | `cbt/admin/competency-matrix` | Grid divisi × skill: level aktual tiap karyawan vs standar; filter legend interaktif; halaman cetak terpisah |
| Standar Divisi-Skill | `cbt/admin/division-skills` | CRUD standar **level minimum** yang disyaratkan per skill untuk tiap divisi |
| Kompetensi Karyawan | `cbt/admin/employee-competencies` | CRUD manual level kompetensi karyawan (koreksi/penyesuaian di luar ujian), tercatat di `EmployeeCompetencyHistory` |
| Riwayat Kompetensi | `cbt/admin/competency-history/print` | Dokumen cetak riwayat perubahan level |

### 3.11 Self-Service Karyawan (`cbt/*` + `user.*`)

| Fitur | Route | Deskripsi |
|---|---|---|
| Ujian Saya | `cbt/my-exams` | Dashboard: ujian pending (kartu dengan jadwal/deadline/sisa waktu), 10 ujian terakhir (tabel status), ringkasan kompetensi |
| Info & Registrasi | `cbt/exam/{exam}/info`, `register` | Detail paket published + daftar mandiri |
| Persiapan & Pengerjaan | `cbt/session/{s}`, `/take` | Halaman pre-start → layar ujian: timer hitung mundur, **autosave jawaban** (`save-answer`), navigasi soal, auto-submit saat waktu habis |
| Hasil | `cbt/session/{s}/result` | Skor, rincian benar/salah PG, skor esai |
| Riwayat | `cbt/history` | Filter status + paginasi 20/baris + statistik pribadi |
| Kompetensi Saya | `cbt/my-competencies`, `my-competencies` | Dua tampilan: kelola kompetensi tersimpan & sertifikat dari sesi approved (grouped per skill) |
| Pelatihan & Profil | `my-training`, `training-history`, `my-profile` | Daftar pelatihan, riwayat, profil karyawan |

### 3.12 Halaman Pendukung / Katalog Modul

Halaman-halaman berikut ada di panel admin dan bersifat **informatif** (peta modul/monitoring ringkas), bukan alur kerja transaksional:

| Halaman | Isi |
|---|---|
| `report-and-audit` | Ringkasan laporan & audit kompetensi (legend level L0–L4) |
| `evaluation-and-exam` | Katalog modul evaluasi & ujian |
| `material-management` | Katalog manajemen materi (grid media) |
| `machining/monitoring` | Monitoring mesin (informasi) |
| `settings` | Pengaturan aplikasi: peran, ekspor audit log, dsb. |

## 4. Alur Bisnis End-to-End

### 4.1 State Machine Sesi Ujian

```
                    ┌────────────── (bulk-assign / assign) ──────────────┐
                    ▼                                                     │
  ┌──────────┐   start    ┌─────────┐   submit    ┌───────────┐          │
  │ assigned ├───────────►│ started ├────────────►│ submitted │◄─────────┘
  └──────────┘            └────┬────┘             └─────┬─────┘
       ▲ deadline lewat        │ auto-submit            │ verify
       │ = terkunci            ▼                        ▼
  ┌─────────┐           (tetap started)      ┌───────────────────────┐
  │ cancel  │                               │ verified_pass         │
  └─────────┘                               │ verified_fail         │
                                            └───────────┬───────────┘
                                                        │ manager decision
                                          ┌─────────────┴─────────────┐
                                          ▼                           ▼
                                   ┌────────────┐              ┌──────────┐
                                   │  approved  │              │ rejected │
                                   └─────┬──────┘              └──────────┘
                                         │ level kompetensi naik ke target_level
                                         ▼
                              EmployeeCompetency + History tercatat
```

Label status yang tampil di UI: Ditugaskan → Sedang Berlangsung → Menunggu Verifikasi → *Lulus - Menunggu Approval* → **Lulus - Disetujui** / Tidak Lulus / Ditolak.

### 4.2 Aturan Penilaian

**Kuantitatif (otomatis + koreksi esai):**
1. Jawaban PG dinilai otomatis terhadap kunci; esai diberi skor manual verifikator (0–bobot soal).
2. Skor akhir sesi 0–100 = agregat terbobot (bobot soal di-snapshot saat paket dibuat).
3. Lulus jika `score ≥ exam.passing_score` (KKM).

**Kualitatif (manajer):**
- 5 kriteria × skala 0–2 → maksimum 10.
- **Layak disetujui bila skor ≥ 7**, didukung metode interview/observasi.

**Sertifikasi:** sesi `approved` menaikkan `EmployeeCompetency.level` karyawan pada skill terkait hingga `exam.target_level`, dengan jejak `EmployeeCompetencyHistory`.

## 5. Model Data (15 entitas)

| Model | Peran | Relasi kunci |
|---|---|---|
| `User` | Akun aplikasi + role (`admin`/`manager`/`user`) | HasOne `Employee` |
| `Employee` | Data karyawan: NIK, status Aktif | BelongsTo Department/Division/Position; HasMany competencies & examSessions |
| `Department` | Departemen induk | HasMany Division |
| `Division` | Divisi kerja | BelongsTo Department; HasMany Position, DivisionSkill |
| `Position` | Posisi dalam divisi | BelongsTo Division |
| `Skill` | Kompetensi teknis (kode/nama) | HasMany Question, Exam; BelongsToMany Division |
| `DivisionSkill` | Standar level minimum skill per divisi | BelongsTo Division, Skill |
| `Question` | Soal PG/esai + bobot, `question_set_id` | BelongsTo Skill |
| `Exam` | Paket ujian: skill, target_level, KKM, durasi, publish | BelongsToMany Question (pivot `exam_question`: snapshot bobot); HasMany Session |
| `ExamSession` | Penugasan/pengerjaan: status, score, jadwal, deadline, keputusan manajer | BelongsTo Employee(NIK), Exam |
| `ExamAnswer` / `ExamQuestion` | Jawaban karyawan & baris soal dalam paket | BelongsTo ExamSession/Exam |
| `ManagerAssessment` | Penilaian kualitatif: metode, 5 kriteria, skor | BelongsTo ExamSession, User(pembuat) |
| `EmployeeCompetency` | Level kompetensi aktual per skill per karyawan | BelongsTo Employee, Skill; `verified_by` |
| `EmployeeCompetencyHistory` | Jejak audit perubahan level | Terkait competency |

## 6. Non-Fungsional

| Aspek | Implementasi |
|---|---|
| Stack | Laravel 12 · PHP 8.5 · MySQL · Vite/Mazer (Bootstrap 5.3) |
| Realtime | Broadcasting Laravel Reverb/Pusher: channel `admin.dashboard`, event `DashboardStatsUpdated` (KPI live) |
| Keamanan | Middleware `auth`, `is.admin`, `is.manager`, `is.user`; CSRF di semua form POST; session cookie terenkripsi; Sanctum tersedia (`sanctum/csrf-cookie`) |
| UI/UX | Design system internal — wajib baca [`docs/DESIGN-SYSTEM.md`](DESIGN-SYSTEM.md); dark mode `data-bs-theme`; SweetAlert2 untuk seluruh dialog |
| Aksesibilitas | Ikon dekoratif `aria-hidden`, tombol ikon-saja berlabel, focus-visible ring |

## 7. Status & Catatan (As-Built)

- Halaman §3.12 masih **katalog/informasi** — belum menjadi alur transaksional.
- Konstanta `STATUS_PENDING_APPROVAL` terdefinisi pada model namun jalur utama approval menggunakan pasangan `verified_pass → approved/rejected`.
- Halaman dummy orphan sudah dihapus (sebelumnya `admin/employees/index`).
- Sebagian halaman publik memakai gaya marketing sendiri dan sengaja di luar design system admin.

---
*Dokumen ini menggambarkan kondisi aplikasi per Agustus 2026. Perubahan fitur wajib memperbarui dokumen ini.*
