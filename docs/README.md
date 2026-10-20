# 📚 Dokumentasi Panduan Sistem E-Course & LMS SMK
### Mobile-First Edition — Soft Pastel Capsule & Bento UI

Dokumentasi ini merupakan sumber acuan tunggal (*single source of truth*) untuk perancangan, arsitektur teknis, dan implementasi antarmuka pada platform **E-Course & Learning Management System (LMS) SMK**. Seluruh spesifikasi dirancang dengan pendekatan **Mobile-First**, **Zero-Bloatware**, serta mengadopsi bahasa visual **Soft Pastel Capsule & Bento UI**.

---

## 📂 Indeks Berkas Dokumentasi

| Dokumen | Format / Lokasi | Fokus & Cakupan Utama |
| :--- | :--- | :--- |
| 📘 **[PRD (Product Requirement Document)](prd.md)** | `docs/prd.md` | **Spesifikasi Kebutuhan Produk & Arsitektur Menu Mobile-First**<br>• Filosofi *Zero-Bloat* & *3-Tap Rule*<br>• Alur Onboarding & Otentikasi Terpadu (Single Sign-On Role Detection)<br>• Struktur 4 Tab Navigasi Bawah (*Floating Pill*) untuk 6 Role Pengguna<br>• Detail alur presensi mandiri, pengumpulan tugas, penilaian kilat (*Quick Grader*), dan ekspor laporan. |
| 🎨 **[Design System & Google Stitch Guide](design.md)** | `docs/design.md` | **Panduan Desain Antarmuka & Prompt AI Stitch**<br>• Estetika *Warm Alabaster Canvas* (`#F9F8F6`) & *Deep Charcoal* (`#141416`)<br>• Token warna kartu hero gradasi pastel (Sage, Blossom, Sky, Amber, Lavender)<br>• Geometri serba kapsul (*Pill & Capsule*), *Weekly Capsule Date Selector*, dan *Floating Navigation Pill*<br>• 7 *Ready-to-use Prompts* bahasa Inggris untuk generator Google Stitch. |
| 📱 **[Interactive Mobile Prototype (HTML)](prototype.html)** | `docs/prototype.html` | **Prototipe Interaktif Mandiri (6 Roles & Auth Flow)**<br>• Studio Inspector & Phone Simulator langsung di browser (HTML + Tailwind)<br>• Simulasi lengkap 6 role (Siswa, Guru, Wali Kelas, Kepsek, Admin, Super Admin)<br>• Uji coba interaktif: Onboarding, Login, Kalender Kapsul, Kunci Bab, Presensi Sesi, Quick Grader, dan Floating Nav. |
| 🖼️ **[Visual Reference Asset](../public/design-reference.png)** | `public/design-reference.png` | **Acuan Visual Utama Antarmuka**<br>Tangkapan visual antarmuka seluler yang menjadi patokan visual layout hero card, date capsule, list kapsul, dan bottom navigation bar. |

---

## 💡 Intisari Produk (PRD Highlights)

### 1. Prinsip Utama: "Zero-Bloat" & KBM Efisien
Sistem dirancang khusus untuk kebutuhan riil KBM vokasi SMK tanpa fitur berlebih (*feature creep*):
1. **Aturan 3 Ketukan (*3-Tap Rule*)**: Presensi kelas atau pengumpulan tugas selesai maksimal dalam 3 ketukan dari layar utama.
2. **Tanpa Fitur Sampah (*No Feature Creep*)**: Tanpa forum media sosial internal atau gamifikasi yang tidak terpakai; murni fokus pada materi, tugas, kehadiran, dan penilaian.
3. **Navigasi 4-Tab Konsisten (*Floating Pill*)**: Masing-masing role memiliki tepat 4 menu inti di bilah bawah melayang yang mudah dijangkau ibu jari.

### 2. Matriks 6 Role Pengguna & Pemetaan 4 Tab

| Role | Identitas & Fokus Utama | Tab 1 | Tab 2 | Tab 3 | Tab 4 |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 🎓 **Siswa** | Hadir tepat waktu, akses modul KBM, kumpulkan tugas | **Home**<br>(Hero Kelas Aktif, Kalender Kapsul, Timeline) | **Kelas**<br>(Filter Mapel, Modul Buka/Kunci) | **Tugas**<br>(Filter Status, Upload Lembar Kerja) | **Rekap**<br>(Presensi Kilat, Ringkasan Hadir, Nilai) |
| 👨‍🏫 **Guru Mapel** | Buka sesi presensi, atur kunci materi, nilai tugas siswa | **Home**<br>(Hero Jadwal, Tombol Presensi 15m, Antrean Nilai) | **Materi**<br>(Toggle Buka/Kunci Bab, Form Upload) | **Nilai**<br>(Quick Grader, Input Nilai 0-100, Izin Revisi) | **Presensi**<br>(Timer Mandiri & Override Hadir Manual) |
| 📋 **Wali Kelas** | Deteksi dini kendala siswa, pantau absensi & nilai rombel | **Home**<br>(Rombel Binaan, *Early Warning System*) | **Absensi**<br>(Presensi Apel & Monitor Kehadiran Harian) | **Leger**<br>(Daftar Siswa Binaan, Rapor Bayangan) | **Laporan**<br>(Rekap Bulanan, Unduh Rekap PDF/Excel) |
| 🏛️ **Kepala Sekolah** | Pemantauan kinerja KBM makro sekolah & unduh laporan (*Read-Only*) | **Home**<br>(% Sesi Terlaksana, Grafik Mingguan X-XII) | **Jurusan**<br>(Analitik Akademik & Hadir per Jurusan) | **KBM**<br>(Monitoring Keaktifan Unggah & Penilaian Guru) | **Ekspor**<br>(Laporan Resmi Hadir & Leger PDF/Excel) |
| ⚙️ **Admin (Operator)** | Operasional harian: plotting jadwal, koreksi presensi, reset sandi | **Home**<br>(Statistik Harian, Aksi Pintas Cepat) | **Pengguna**<br>(Direktori Guru/Siswa, Reset Sandi Instan) | **Plotting**<br>(Penetapan Wali Kelas & Guru-Mapel) | **Moderasi**<br>(Koreksi Absen Lampau & Tinjau Materi) |
| 🛡️ **Super Admin** | Konfigurasi sistem, master data sekolah, audit log keamanan | **Home**<br>(Tahun Ajaran Aktif, Parameter Global) | **Master**<br>(Jurusan, Tingkat/Kelas, Master Mapel) | **Akses**<br>(Akun Pejabat & Matriks Izin Role) | **Log**<br>(Audit Trail Keamanan & Riwayat Login) |

---

## 🎨 Intisari Desain Visual (Design System Highlights)

### 1. Karakteristik Estetika
* **Warm Alabaster Canvas**: Latar belakang bernuansa krem lembut (`#F9F8F6`) dengan efek pendaran halus *Peach Glow* (`#FFF1E6`) di sudut kanan atas.
* **Deep Charcoal Elements (`#141416`)**: Warna kontras tajam untuk tab aktif, tombol aksi utama (*Play / Action*), dan teks primer.
* **Pill & Capsule Geometry**: Komponen serba kapsul membulat (*border-radius 9999px* untuk selector/navigasi dan *24px–28px* untuk kartu bento).
* **Floating Glassmorphic Bottom Bar**: Bilah navigasi melayang (`bottom: 20px`, tinggi `68px`) dengan latar transparan blur dan kapsul hitam pekat yang melebar pada tab aktif.

### 2. Aksen Pastel per Role Pengguna
Setiap role memiliki kartu hero dengan aksen gradasi warna pastel yang khas dan harmonis:
* 🎓 **Siswa / Produktif**: *Pastel Sage* (`#C8DFC6` $\rightarrow$ `#EBF2D5`)
* 👨‍🏫 **Guru Mapel**: *Pastel Blossom* (`#FBD2E0` $\rightarrow$ `#FDF0F5`)
* 📋 **Wali Kelas**: *Pastel Sky* (`#D0E8F8` $\rightarrow$ `#EAF5FE`)
* 🏛️ **Kepala Sekolah**: *Pastel Amber* (`#FDE4C3` $\rightarrow$ `#FEF5E7`)
* ⚙️ **Admin & Super Admin**: *Pastel Lavender* (`#E2D9F3` $\rightarrow$ `#F3EFFB`)

### 3. Google Stitch AI Prompting
Berkas `docs/design.md` menyediakan serangkaian prompt siap pakai dalam bahasa Inggris untuk menghasilkan mock-up visual UI dengan fidelitas tinggi menggunakan **Google Stitch**:
* `Style Prompt`: Standar gaya global (Warm alabaster, charcoal accents, capsule lists).
* `Screen Prompts`:
  * Layar Onboarding & Login
  * Layar Siswa (Home, Kelas, Tugas)
  * Layar Guru Mapel (Dashboard & Quick Grader)
  * Layar Wali Kelas (Early Warning & Leger)
  * Layar Kepala Sekolah (Pantauan Eksekutif & Ekspor)
  * Layar Admin & Super Admin (Master Data & Audit Log)

---

## 🚀 Panduan Pengembang & Tim Desain

1. **Memulai Implementasi UI**:
   * Periksa `docs/design.md` untuk menyelaraskan token CSS, radius kapsul, palet warna hex, dan tipografi `'Plus Jakarta Sans'`.
   * Gunakan prompt Google Stitch yang tersedia di `docs/design.md` jika membutuhkan mock-up visual tambahan untuk komponen spesifik.
2. **Membuat Halaman & Rute Baru**:
   * Rujuk `docs/prd.md` Bagian 3 untuk memastikan halaman baru mematuhi batasan **4-Tab Floating Pill** untuk role terkait.
   * Pastikan hierarki alur tidak melebihi aturan 3 ketukan (*3-Tap Rule*).
3. **Aset Gambar Acuan**:
   * Berkas visual referensi dapat diakses pada `public/design-reference.png`.

---
*Terakhir diperbarui: 20 Oktober 2026 — Tim Pengembang & Desain LMS SMK*
