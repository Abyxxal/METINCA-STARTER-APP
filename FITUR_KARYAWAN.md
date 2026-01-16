# 📚 Fitur Sistem Pelatihan untuk Karyawan

## ✅ Fitur yang Tersedia untuk Karyawan

### 1. 🏠 Dashboard (`/dashboard`)
Halaman utama karyawan dengan informasi:
- **Statistik Pelatihan**:
  - Pelatihan Aktif (assigned + started)
  - Pelatihan Selesai (verified_pass + verified_fail)
  - Dalam Proses (started)
  - Sertifikat yang Diperoleh (verified_pass)
- **Pelatihan Terbaru**: 3 pelatihan terakhir yang ditugaskan
- **Info Profil**: NIK, Email, Departemen, Divisi, Jabatan

---

### 2. 📖 Pelatihan Saya (`/my-training`)
Menampilkan daftar semua pelatihan yang ditugaskan kepada karyawan.

#### Fitur Filter & Search:
- **Filter Status**: 
  - Semua Status
  - Belum Dimulai (assigned)
  - Sedang Dikerjakan (started)
  - Menunggu Verifikasi (submitted)
  - Lulus (verified_pass)
  - Tidak Lulus (verified_fail)
- **Filter Level**: Level 1, 2, 3, 4
- **Search**: Cari berdasarkan judul ujian atau nama skill
- **Auto-submit**: Filter otomatis tanpa tombol submit

#### Status Badges:
- 🟡 **Belum Dimulai** - Ujian belum dikerjakan, bisa dimulai kapan saja
- 🔵 **Sedang Dikerjakan** - Ujian sudah dimulai tapi belum dikumpulkan
- ⚪ **Menunggu Verifikasi** - Ujian sudah dikumpulkan, menunggu admin memverifikasi
- 🟢 **Lulus (X%)** - Ujian sudah diverifikasi dan dinyatakan LULUS dengan nilai X%
- 🔴 **Tidak Lulus (X%)** - Ujian sudah diverifikasi dan dinyatakan TIDAK LULUS dengan nilai X%

#### Tombol Aksi:
- **Mulai** - Untuk ujian yang belum dimulai (status: assigned)
- **Lanjutkan** - Untuk ujian yang sedang dikerjakan (status: started)
- **Lihat Status** - Untuk ujian yang menunggu verifikasi (status: submitted)
- **Lihat Hasil** - Untuk ujian yang sudah diverifikasi (status: verified_pass/verified_fail)

---

### 3. ⏱️ Mengerjakan Ujian

#### A. Halaman Info Ujian (`/cbt/employee/exam/{id}`)
Sebelum mulai ujian, karyawan akan melihat:
- Judul ujian
- Skill yang diuji
- Target level
- Jumlah soal
- Durasi ujian
- Nilai lulus (KKM)
- Instruksi ujian
- Checkbox persetujuan

**Tombol**: 
- **Mulai Ujian** - Memulai ujian dan timer mulai berjalan
- **Kembali** - Kembali ke daftar pelatihan

#### B. Halaman Mengerjakan Ujian (`/cbt/employee/exam/{id}/take`)
Fitur saat mengerjakan ujian:
- **Timer Countdown**: Hitung mundur waktu yang tersisa
- **Navigation Soal**: Tombol nomor soal untuk navigasi cepat
- **Progress Tracker**: Jumlah soal yang sudah dijawab vs total soal
- **Auto-save**: Jawaban otomatis tersimpan setiap kali dipilih
- **Tipe Soal**:
  - Multiple Choice (Pilihan Ganda)
  - True/False (Benar/Salah)
  - Essay (Uraian)
- **Tombol Submit**: Untuk mengumpulkan ujian
- **Auto-submit**: Ujian otomatis dikumpulkan jika waktu habis

**Catatan Penting**:
- ⚠️ Jika menutup browser/tab, jawaban tetap tersimpan dan bisa dilanjutkan
- ⚠️ Jika waktu habis, ujian otomatis dikumpulkan
- ⚠️ Setelah submit, ujian tidak bisa dikerjakan lagi

---

### 4. 📊 Melihat Hasil Ujian (`/cbt/employee/exam/{id}/result`)

Halaman hasil ujian menampilkan:

#### A. Status Ujian
**3 Kemungkinan Status**:

1. **⏳ MENUNGGU VERIFIKASI** (Status: submitted)
   - Icon: Hourglass (jam pasir)
   - Warna: Kuning/Warning
   - Informasi: 
     - "Ujian Anda sudah berhasil dikumpulkan dan sedang dalam proses verifikasi oleh admin"
     - Alert box dengan info bahwa admin akan segera memverifikasi
     - Nilai belum ditampilkan (karena belum diverifikasi)
   
2. **✅ LULUS** (Status: verified_pass)
   - Icon: Trophy (piala)
   - Warna: Hijau/Success
   - Informasi: "Selamat! Anda telah lulus ujian ini"
   - Nilai Anda ditampilkan dengan warna hijau
   - Nilai dibandingkan dengan KKM
   
3. **❌ TIDAK LULUS** (Status: verified_fail)
   - Icon: X Circle
   - Warna: Merah/Danger
   - Informasi: "Maaf, Anda belum lulus ujian ini"
   - Nilai Anda ditampilkan dengan warna merah
   - Nilai dibandingkan dengan KKM

#### B. Detail Ujian
- Nama Ujian
- Skill & Code
- Target Level
- **Status Ujian** (Belum Dimulai/Sedang Dikerjakan/Menunggu Verifikasi/Lulus/Tidak Lulus)
- Waktu Mulai
- Waktu Selesai
- Durasi Pengerjaan (dalam menit)
- Tanggal Verifikasi (jika sudah diverifikasi)
- Verifikator (nama admin yang memverifikasi)
- Catatan Admin (jika ada)

#### C. Tombol Aksi
- **Kembali ke Pelatihan Saya** - Kembali ke daftar pelatihan

---

### 5. 📋 Riwayat Pelatihan (`/training-history`)

Menampilkan semua ujian yang sudah dikerjakan/dikumpulkan (status: submitted, verified_pass, verified_fail)

#### Fitur Filter:
- **Filter Tahun**: 2023, 2024, 2025, 2026
- **Filter Level**: Level 1, 2, 3, 4
- **Search**: Cari berdasarkan judul ujian

#### Informasi yang Ditampilkan:
- Nama Pelatihan
- Kategori (Skill Code)
- Level
- Tanggal Selesai:
  - Jika sudah diverifikasi: tanggal verifikasi
  - Jika belum diverifikasi: tanggal dikumpulkan + label "(Dikumpulkan)"
- Nilai:
  - Jika sudah diverifikasi: nilai dengan progress bar
  - Jika belum diverifikasi: icon hourglass + "Menunggu"
- Status:
  - ⏳ Menunggu Verifikasi (submitted)
  - ✅ Lulus (verified_pass)
  - ❌ Tidak Lulus (verified_fail)
- Tombol **Lihat Detail** untuk melihat hasil lengkap

---

### 6. 🎯 Kompetensi Saya (`/my-competencies`)

Menampilkan semua skill/kompetensi yang sudah dikuasai (berdasarkan ujian yang lulus)

#### Tampilan per Skill:
- **Nama Skill** & Code
- **Level Tertinggi** yang dicapai
- **Progress Bar** (Level X dari 4)
- **Jumlah Sertifikat** untuk skill tersebut
- **Tanggal Terakhir** mendapat sertifikat
- **Lihat Detail** (collapse):
  - Riwayat semua ujian untuk skill tersebut
  - Judul ujian, level, nilai
  - Link ke hasil ujian

#### Ringkasan Kompetensi:
- Total Skill yang dikuasai
- Total Sertifikat yang diperoleh
- Level Tertinggi yang dicapai
- Terakhir Update

#### Empty State:
Jika belum ada kompetensi, akan ada:
- Pesan "Belum Ada Kompetensi"
- Tombol untuk ke "Pelatihan Saya"

---

### 7. 👤 Profil Saya (`/my-profile`)

#### A. Kartu Profil (Sidebar Kiri)
- Foto profil atau icon placeholder
- Nama lengkap
- Jabatan
- Email
- NIK
- Departemen
- Divisi

#### B. Informasi Pribadi (Konten Utama)
Form yang menampilkan:
- Nama Lengkap (disabled)
- Email (disabled)
- NIK (disabled)
- Departemen (disabled)
- Divisi (disabled)
- Jabatan (disabled)

#### C. Ubah Password
Form untuk mengganti password:
- Password Lama
- Password Baru
- Konfirmasi Password
- Tombol **Simpan Perubahan**

#### D. Statistik Pelatihan
- Pelatihan Aktif
- Pelatihan Selesai
- Jumlah Sertifikat

---

## 🔄 Flow Lengkap Mengerjakan Ujian

### Scenario 1: Ujian Baru (Belum Dikerjakan)
1. ✅ Karyawan login → Dashboard
2. ✅ Klik menu "Pelatihan Saya" → Daftar Pelatihan
3. ✅ Cari ujian dengan status "Belum Dimulai" (badge kuning)
4. ✅ Klik tombol **Mulai** → Halaman Info Ujian
5. ✅ Baca instruksi, centang persetujuan → Klik **Mulai Ujian**
6. ✅ Timer mulai berjalan → Kerjakan soal
7. ✅ Jawaban auto-save setiap dipilih
8. ✅ Selesai → Klik **Submit Ujian**
9. ✅ Konfirmasi submit → Ujian dikumpulkan
10. ✅ Redirect ke Halaman Hasil → Status: **MENUNGGU VERIFIKASI**

### Scenario 2: Lanjutkan Ujian yang Sedang Dikerjakan
1. ✅ Karyawan login → Pelatihan Saya
2. ✅ Cari ujian dengan status "Sedang Dikerjakan" (badge biru)
3. ✅ Klik tombol **Lanjutkan** → Halaman Mengerjakan Ujian
4. ✅ Timer lanjut dari waktu tersisa
5. ✅ Jawaban sebelumnya sudah tersimpan
6. ✅ Lanjutkan mengerjakan → Submit

### Scenario 3: Melihat Status Menunggu Verifikasi
1. ✅ Karyawan login → Pelatihan Saya
2. ✅ Cari ujian dengan status "Menunggu Verifikasi" (badge abu-abu)
3. ✅ Klik tombol **Lihat Status** → Halaman Hasil
4. ✅ Tampil: 
   - Icon hourglass kuning
   - Text "MENUNGGU VERIFIKASI"
   - Alert info tentang proses verifikasi
   - Nilai belum ditampilkan
5. ✅ Bisa cek berkala untuk melihat hasil setelah admin verifikasi

### Scenario 4: Admin Sudah Verifikasi - LULUS
1. ✅ Admin memverifikasi ujian → Set status "verified_pass" + nilai
2. ✅ Karyawan login → Pelatihan Saya
3. ✅ Status berubah menjadi "Lulus (90%)" dengan badge hijau
4. ✅ Klik tombol **Lihat Hasil** → Halaman Hasil
5. ✅ Tampil:
   - Icon trophy hijau
   - Text "LULUS"
   - Nilai dengan warna hijau
   - Perbandingan nilai dengan KKM
   - Detail lengkap ujian
   - Nama verifikator
   - Catatan admin (jika ada)

### Scenario 5: Admin Sudah Verifikasi - TIDAK LULUS
1. ✅ Admin memverifikasi ujian → Set status "verified_fail" + nilai
2. ✅ Karyawan login → Pelatihan Saya
3. ✅ Status berubah menjadi "Tidak Lulus (65%)" dengan badge merah
4. ✅ Klik tombol **Lihat Hasil** → Halaman Hasil
5. ✅ Tampil:
   - Icon X circle merah
   - Text "TIDAK LULUS"
   - Nilai dengan warna merah
   - Perbandingan nilai dengan KKM
   - Catatan admin tentang area yang perlu diperbaiki

### Scenario 6: Melihat History Ujian
1. ✅ Karyawan login → Menu "Riwayat Pelatihan"
2. ✅ Tampil semua ujian yang sudah dikerjakan:
   - Yang menunggu verifikasi (icon hourglass)
   - Yang sudah lulus (badge hijau + nilai)
   - Yang tidak lulus (badge merah + nilai)
3. ✅ Filter berdasarkan tahun/level
4. ✅ Klik **Lihat Detail** → Halaman Hasil

### Scenario 7: Melihat Kompetensi yang Dikuasai
1. ✅ Karyawan login → Menu "Kompetensi Saya"
2. ✅ Tampil card untuk setiap skill yang sudah dikuasai
3. ✅ Masing-masing card menampilkan:
   - Level tertinggi yang dicapai
   - Jumlah sertifikat
   - Progress bar
4. ✅ Klik **Lihat Detail** → Expand untuk melihat riwayat ujian

---

## 📱 Status & Badge Guide

### Status Ujian:
| Status | Badge Color | Icon | Arti | Aksi Karyawan |
|--------|-------------|------|------|---------------|
| `assigned` | Kuning | 🕐 | Belum Dimulai | Klik **Mulai** untuk memulai ujian |
| `started` | Biru | ▶️ | Sedang Dikerjakan | Klik **Lanjutkan** untuk melanjutkan |
| `submitted` | Abu-abu | ⏳ | Menunggu Verifikasi Admin | Klik **Lihat Status** untuk cek |
| `verified_pass` | Hijau | ✅ | Lulus - Terverifikasi | Klik **Lihat Hasil** untuk detail |
| `verified_fail` | Merah | ❌ | Tidak Lulus - Terverifikasi | Klik **Lihat Hasil** untuk detail |

### Penjelasan Detail:

#### 1. Belum Dimulai (assigned)
- Admin sudah menugaskan ujian ke karyawan
- Karyawan bisa mulai kapan saja sebelum deadline
- Timer belum berjalan
- Belum ada jawaban yang tersimpan

#### 2. Sedang Dikerjakan (started)
- Karyawan sudah klik "Mulai Ujian"
- Timer sudah berjalan
- Jawaban sudah mulai tersimpan
- Bisa ditutup dan dilanjutkan nanti (jawaban tetap tersimpan)
- Waktu terus berjalan sampai submit atau expired

#### 3. Menunggu Verifikasi (submitted)
- Karyawan sudah submit ujian
- Sistem sudah hitung nilai otomatis (untuk multiple choice & true/false)
- Essay question masih perlu dinilai manual oleh admin
- Admin perlu review dan verifikasi final
- Karyawan **TIDAK BISA** melihat nilai sampai admin verifikasi
- Karyawan bisa cek status verifikasi berkala

#### 4. Lulus (verified_pass)
- Admin sudah verifikasi dan nilai >= KKM (passing score)
- Nilai sudah final dan ditampilkan ke karyawan
- Karyawan mendapat sertifikat untuk skill tersebut
- Level kompetensi bertambah
- Bisa dilihat di "Riwayat Pelatihan" dan "Kompetensi Saya"

#### 5. Tidak Lulus (verified_fail)
- Admin sudah verifikasi dan nilai < KKM
- Nilai sudah final dan ditampilkan ke karyawan
- Karyawan perlu mengulang ujian (jika admin assign ulang)
- Admin bisa memberikan catatan untuk improvement

---

## 🎓 Tips untuk Karyawan

### Saat Mengerjakan Ujian:
✅ Pastikan koneksi internet stabil
✅ Perhatikan timer countdown
✅ Jawaban otomatis tersimpan, tidak perlu save manual
✅ Jika harus menutup browser, jawaban tetap tersimpan
✅ Gunakan tombol navigasi soal untuk pindah soal
✅ Pastikan semua soal sudah dijawab sebelum submit
✅ Jika waktu habis, ujian otomatis dikumpulkan

### Setelah Submit Ujian:
✅ Status berubah menjadi "Menunggu Verifikasi"
✅ Cek berkala di halaman "Pelatihan Saya" atau "Riwayat Pelatihan"
✅ Setelah diverifikasi, akan muncul hasil (Lulus/Tidak Lulus)
✅ Baca catatan admin jika ada untuk improvement

### Tracking Progress:
✅ Cek dashboard untuk statistik keseluruhan
✅ Gunakan filter di "Pelatihan Saya" untuk fokus ke status tertentu
✅ Lihat "Riwayat Pelatihan" untuk tracking semua ujian
✅ Cek "Kompetensi Saya" untuk melihat skill yang sudah dikuasai

---

## 🔐 Keamanan & Integritas Ujian

### Proteksi yang Diterapkan:
- ✅ Timer server-side (tidak bisa dimanipulasi)
- ✅ Auto-submit saat waktu habis
- ✅ Jawaban tersimpan di database, bukan localStorage
- ✅ Tidak bisa submit ulang setelah dikumpulkan
- ✅ Tidak bisa edit jawaban setelah submit
- ✅ Verifikasi manual oleh admin untuk memastikan keadilan

---

## 🆘 FAQ (Frequently Asked Questions)

**Q: Apa yang terjadi jika koneksi internet terputus saat mengerjakan ujian?**
A: Jawaban yang sudah dipilih akan tersimpan karena menggunakan auto-save. Setelah koneksi kembali, karyawan bisa lanjutkan ujian dari soal terakhir. Timer terus berjalan di server.

**Q: Berapa lama admin memverifikasi ujian?**
A: Tergantung kebijakan perusahaan. Biasanya 1-3 hari kerja. Status akan otomatis berubah setelah admin verifikasi.

**Q: Apakah bisa mengulang ujian jika tidak lulus?**
A: Tergantung kebijakan. Admin bisa assign ulang ujian yang sama atau berbeda untuk level yang sama.

**Q: Apakah nilai yang tampil di "Menunggu Verifikasi" adalah nilai final?**
A: Tidak. Nilai belum ditampilkan saat status "Menunggu Verifikasi". Nilai final hanya tampil setelah admin memverifikasi.

**Q: Bagaimana jika tidak sempat menyelesaikan ujian dalam waktu yang ditentukan?**
A: Ujian akan otomatis dikumpulkan saat waktu habis. Soal yang belum dijawab akan dianggap salah.

**Q: Apakah bisa melihat jawaban yang salah setelah ujian?**
A: Tergantung kebijakan admin. Saat ini sistem fokus menampilkan nilai final, bukan pembahasan soal.

**Q: Berapa lama jawaban tersimpan di sistem?**
A: Jawaban tersimpan permanent di database untuk keperluan audit dan tracking progress karyawan.

---

## 🎯 Kesimpulan

Sistem pelatihan ini dirancang untuk memberikan pengalaman yang jelas dan transparan bagi karyawan dalam:
- ✅ Melihat pelatihan yang ditugaskan
- ✅ Mengerjakan ujian dengan lancar
- ✅ Tracking status ujian (belum dimulai, sedang dikerjakan, menunggu verifikasi, lulus/tidak lulus)
- ✅ Melihat hasil setelah verifikasi admin
- ✅ Tracking kompetensi yang sudah dikuasai
- ✅ Melihat riwayat semua pelatihan

**Status "Menunggu Verifikasi"** adalah fitur penting yang memberikan transparansi kepada karyawan bahwa ujian mereka sudah diterima sistem dan sedang dalam proses review oleh admin, sehingga karyawan tidak perlu bertanya-tanya apakah ujian mereka berhasil dikumpulkan atau tidak.
