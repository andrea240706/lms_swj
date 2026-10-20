# Mobile-First Design System & Google Stitch Guide
## E-Course / LMS SMK — "Soft Pastel Capsule & Bento UI"

> **Referensi Visual**: Berdasarkan gambar referensi *Mobile E-Course UI* (Warm Ivory Canvas, Charcoal Pill Controls, Soft Pastel Hero Cards, & Capsule Lists).  
> **Target Viewport Utama**: Mobile-First (`375px` – `430px` width, rasio `9:19.5`), dengan pembungkus responsif terpusat pada layar desktop.

---

## 1. Karakteristik Visual Utama (Berdasarkan Gambar Referensi)
Desain ini meninggalkan tampilan tabel kaku khas dashboard lama dan beralih ke estetika **Editorial Mobile Learning** yang hangat, bersih, dan sangat ramah ibu jari:
1. **Kanvas Hangat (*Warm Alabaster Canvas*)**: Latar belakang tidak menggunakan putih menyilaukan maupun abu-abu dingin, melainkan krem gading sangat lembut (`#F9F8F6`) dengan pendaran gradasi *peach/cream* halus di sudut atas.
2. **Kontras Hitam Arang (*Deep Charcoal Accents*)**: Elemen aktif seperti hari yang dipilih pada kalender, tombol aksi utama (*Play/Action*), dan bilah navigasi bawah menggunakan warna hitam arang pekat (`#141416`) sehingga menciptakan fokus visual yang tajam.
3. **Kartu Hero Pastel Bergradasi**: Kartu sorotan utama (*Today's Class* / Ringkasan Role) menggunakan warna pastel lembut seperti **Sage Green** (`#D4E4D2` ke `#E9F0CE`) atau **Blush Pink** (`#F9D5E5` ke `#FCE8F1`) dengan sudut ekstra membulat (`28px`).
4. **Komponen Serba Kapsul (*Pill & Capsule Geometry*)**:
   * Pemilih tanggal mingguan berbentuk kapsul vertikal (`border-radius: 9999px`).
   * Daftar pelajaran/item berbentuk kapsul horisontal putih bersih (`border-radius: 24px`) dengan ikon berlatar bulat pastel di sisi kiri.
   * Tombol *Onboarding Slide* dan *Bottom Navigation* berbentuk kapsul penuh.

---

## 2. Palet Warna (Color Tokens)

### 2.1 Permukaan & Latar (Surfaces)
| Token | Hex Code | Penggunaan |
| :--- | :--- | :--- |
| `--bg-canvas` | `#F9F8F6` | Latar belakang utama layar mobile |
| `--bg-glow` | `#FFF1E6` | Efek *radial blur* lembut di bagian kanan atas layar |
| `--surface-card` | `#FFFFFF` | Kartu daftar pelajaran, timeline item, dan lembaran putih |
| `--surface-muted` | `#F1EFEA` | Track tombol *slide*, kapsul non-aktif, atau input field |
| `--surface-dark` | `#141416` | Bilah navigasi bawah aktif, kapsul tanggal aktif, tombol bulat utama |

### 2.2 Warna Aksen Kartu Pastel (Hero & Subject Accents)
Setiap *role* atau kategori mata pelajaran menggunakan variasi kartu pastel yang tetap satu keluarga warna:
* **Pastel Sage (Siswa / Produktif)**: Linear Gradient `135deg`, `#C8DFC6` $\rightarrow$ `#EBF2D5`
* **Pastel Blossom (Materi / Guru Mapel)**: Linear Gradient `135deg`, `#FBD2E0` $\rightarrow$ `#FDF0F5`
* **Pastel Sky (Wali Kelas / Presensi)**: Linear Gradient `135deg`, `#D0E8F8` $\rightarrow$ `#EAF5FE`
* **Pastel Amber (Kepsek / Analitik)**: Linear Gradient `135deg`, `#FDE4C3` $\rightarrow$ `#FEF5E7`
* **Pastel Lavender (Admin & Super Admin)**: Linear Gradient `135deg`, `#E2D9F3` $\rightarrow$ `#F3EFFB`

### 2.3 Tipografi & Warna Teks
* **Font Family**: `'Plus Jakarta Sans'`, sans-serif (Bulat, modern, keterbacaan tinggi di layar kecil).
* **Text Primary**: `#141416` (Judul, angka tanggal, nama pelajaran).
* **Text Secondary**: `#6E6E77` (Jam pelajaran, nama guru, sub-teks).
* **Text On-Dark**: `#FFFFFF` (Teks di dalam navigasi hitam atau kapsul aktif).

---

## 3. Spesifikasi Komponen Kunci (Component Anatomy)

### 3.1 Top Header Bar
* Tinggi `56px`, menyatu dengan latar belakang.
* **Kiri**: Tombol bulat putih/transparan (`44px x 44px`, border tipis `#E6E4DF`) berisi ikon menu garis dua/tiga atau panah kembali `<-`.
* **Tengah** (Opsional pada sub-halaman): Judul halaman (`18px`, `font-weight: 700`).
* **Kanan**: Avatar pengguna bulat (`44px x 44px`) atau pemilih *Role/Semester*.

### 3.2 Hero Spotlight Card (`Today's Class` / `Role Overview`)
* **Radius**: `28px`, padding `20px`, posisi `relative`, `overflow: hidden`.
* **Elemen Dalam**:
  * *Status Pill* di bagian atas: Titik hijau/ikon ceklis + teks waktu (misal: `Live • 08:00 - 09:30` atau `12 PM - 03 AM` di dalam kapsul putih `#FFFFFF`).
  * *Judul Utama*: `22px`, Bold (`#141416`).
  * *Metadata*: Ikon kalender/batch + teks kecil (`13px`).
  * *Progress Bar / Action*: Garis progres hitam dengan thumb bulat, serta tombol bulat hitam (`44px x 44px`) dengan ikon `Play` atau panah di kiri bawah, bersebelahan dengan ilustrasi 3D/vektor tematik di kanan bawah.

### 3.3 Weekly Capsule Date Selector (`Class Schedule`)
* Baris 7 kolom (`grid-template-columns: repeat(7, 1fr)`), jarak `8px`.
* Setiap item berbentuk kapsul vertikal (`padding: 12px 4px`, `border-radius: 999px`).
* **Status Normal**: Latar `#FFFFFF`, border `1px solid #ECEAE4`, teks hari `11px` (`#6E6E77`), angka tanggal `15px` Bold (`#141416`).
* **Status Aktif**: Latar `#141416`, teks putih `#FFFFFF`, dengan 4 titik indikator kecil di bawah angka tanggal.

### 3.4 Capsule Lesson / Data List (`Last Lessons`)
* Setiap baris adalah kartu putih (`#FFFFFF`) dengan `border-radius: 24px`, padding `12px 16px`, bayangan sangat lembut (`0 4px 20px rgba(20, 20, 22, 0.04)`).
* **Kiri**: Lingkaran (`48px x 48px`) berwarna pastel lembut sesuai kategori, memuat ikon garis (`stroke-width: 1.75px`).
* **Tengah**: Judul item (`15px` Bold), sub-teks waktu, dan tumpukan avatar kecil (*avatar group* `+5`) atau badge status.
* **Kanan**: Kapsul metadata kecil (tanggal & jam) dengan latar `#F9F8F6`.

### 3.5 Floating Bottom Navigation Pill
* Melayang di bagian bawah layar (`bottom: 20px`, kiri-kanan `24px`), tinggi `68px`.
* **Wadah Luar**: Kapsul putih/krem transparan (`rgba(255, 255, 255, 0.92)`), `backdrop-filter: blur(16px)`, `border-radius: 999px`, padding `8px`, bayangan `0 12px 32px rgba(20, 20, 22, 0.08)`.
* **Tab Aktif**: Berbentuk kapsul hitam pekat (`#141416`) yang menampilkan **Ikon + Teks Label** (contoh: `[ 🏠 Home ]`).
* **3 Tab Lainnya**: Hanya menampilkan ikon garis berwarna `#141416` di dalam lingkaran transparan, yang akan berubah menjadi kapsul hitam saat diketuk.

---

## 4. Panduan Prompt Google Stitch (Siap Pakai per Layar & Role)

Salin dan tempelkan *prompt* bahasa Inggris di bawah ini ke **Google Stitch** untuk menghasilkan desain UI per layar yang 100% konsisten dengan gambar referensi Anda.

### 4.1 Prompt Dasar Gaya Visual (Sertakan di Awal atau Sebagai System Style)
> **Style Prompt**:  
> *"Mobile app UI design, warm alabaster background (#F9F8F6) with subtle soft peach top-right radial glow, Plus Jakarta Sans typography, deep charcoal black (#141416) primary accents, soft pastel rounded hero cards (28px radius) in sage green (#D4E4D2) and blush pink (#FBD2E0), vertical pill-shaped date selector, white capsule list items (24px radius) with circular pastel icons, and a floating glassmorphic bottom navigation bar featuring an expanding black pill (#141416) for the active tab. Clean, modern vocational high school LMS, zero clutter, dribbble high-craft aesthetic."*

---

### 4.2 Prompt Layar 1: Onboarding & Login Page
> **Google Stitch Prompt — Onboarding & Login**:  
> *"Design a two-screen mobile onboarding and login flow for a vocational school LMS following a warm pastel aesthetic (#F9F8F6 canvas). Screen 1 (Onboarding): Top half features a friendly Indonesian high school student holding a notebook with concentric pastel rainbow arcs behind them; bottom half has a white rounded sheet (32px top radius) with bold title 'Siap Memulai Perjalanan Belajarmu!', a short subtitle, and a full-width gray capsule slide button (#F1EFEA) containing a white circular checkmark button on the left and 'Masuk ke Akun >>>' on the right. Screen 2 (Login): Clean header with back circle button, pastel sage badge 'LMS SMK Vokasi', bold heading 'Masuk ke Ruang Belajar', two rounded capsule input fields for 'NISN / NIP / Username' and 'Kata Sandi', a subtle 'Ingat Sesi Saya' toggle, and a full-width deep charcoal (#141416) pill button 'Masuk Sekarang'."*

---

### 4.3 Prompt Layar 2: Role Siswa (Home, Kelas, & Tugas)
> **Google Stitch Prompt — Student Dashboard & Lessons**:  
> *"Design a mobile LMS student dashboard screen on a #F9F8F6 background. Top bar: left circular menu button, right student avatar. Section 1 'Kelas Hari Ini': A sage green pastel gradient card (28px radius) showing a green live dot 'Sesi Aktif • 45 menit lagi', bold title 'Pemrograman Web & Perangkat Bergerak', subtitle 'Kelas: XII RPL 1 • Bab 06/10', a black progress bar, a round black play button (#141416) on bottom-left, and a 3D coding illustration on bottom-right. Section 2 'Jadwal Kelas': Month dropdown 'Okt 2026' and a 7-day vertical capsule calendar where Sunday 17 is highlighted in solid black (#141416). Section 3 'Timeline': Vertical time slots (08:00, 10:00, 12:00) with horizontal white pill cards showing subject name, time, and small pastel icon. Bottom: Floating white pill navigation bar with active black capsule 'Home', plus icons for Lessons, Tasks, and Attendance."*

---

### 4.4 Prompt Layar 3: Role Guru Mapel (Dashboard Mengajar & Penilaian Cepat)
> **Google Stitch Prompt — Teacher Mobile UI**:  
> *"Design a mobile LMS teacher screen matching the soft pastel capsule design system (#F9F8F6 canvas, #141416 accents). Top bar with teacher profile. Hero card in soft blush pink pastel (#FBD2E0) with a white pill badge '08:00 - 10:30 WIB • Lab RPL 2', bold title 'Basis Data Lanjutan (XI RPL 1)', and a solid black pill button 'Buka Presensi 15 Menit'. Below, a section titled 'Antrean Penilaian Tugas' with white capsule cards (24px radius); each card has a pastel circular icon on the left, assignment title 'Praktikum SQL Join', student submission counter badge '28/32 Terkumpul', and a quick 'Nilai' action pill. Include the signature floating bottom navigation bar with active black pill."*

---

### 4.5 Prompt Layar 4: Role Guru Wali Kelas (Monitoring Rombel & Peringatan Dini)
> **Google Stitch Prompt — Homeroom Teacher Mobile UI**:  
> *"Design a mobile homeroom teacher monitoring screen on a #F9F8F6 canvas. Hero card in soft pastel sky blue (#D0E8F8) showing white pill 'Wali Kelas • X DKV 1', bold title 'Kehadiran Hari Ini: 96%', and 3 mini stat pills (34 Hadir, 1 Sakit, 1 Alpa). Below, section 'Perhatian Khusus (Early Warning)' featuring white capsule cards with soft coral/amber circular icons highlighting students with 3+ absences or grades below KKM 75, complete with a quick 'Lihat Rapor' pill button. Bottom floating pill navigation bar."*

---

### 4.6 Prompt Layar 5: Role Kepala Sekolah (Pantauan Eksekutif & Unduh Laporan)
> **Google Stitch Prompt — Principal Executive Mobile UI**:  
> *"Design a mobile executive dashboard for a school principal using a warm pastel bento aesthetic (#F9F8F6). Hero card in soft pastel amber/peach (#FDE4C3) displaying a white pill 'Pantauan KBM Sekolah', bold title '94.8% Kelas Terlaksana', and a clean mini bar chart of weekly attendance across Grade X, XI, and XII. Below, section 'Ekspor Dokumen Resmi' with white capsule cards for 'Laporan Kehadiran Bulanan (PDF)' and 'Leger Nilai Akademik Sekolah (Excel)', each having a circular black download button (#141416). Floating bottom navigation bar."*

---

### 4.7 Prompt Layar 6: Role Admin & Super Admin (Operator & Master Data)
> **Google Stitch Prompt — Admin & Super Admin Mobile UI**:  
> *"Design a mobile school operator and super admin screen in the soft pastel capsule style (#F9F8F6 background). Hero card in soft pastel lavender (#E2D9F3) with a white pill badge 'Tahun Ajaran 2026/2027 • Ganjil', bold title 'Pusat Kendali & Data Master', and quick action pills for 'Reset Sandi' and 'Koreksi Absen'. Below, a list of white capsule cards (24px radius) for managing 'Plotting Guru & Wali Kelas', 'Direktori Siswa (1.240 Aktif)', and 'Audit Log Sistem', with clean line icons in pastel circles. Bottom floating pill navigation bar."*