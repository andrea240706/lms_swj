# Product Requirement Document (PRD) — Mobile-First Edition
## E-Course / Learning Management System (LMS) SMK

> **Fokus Dokumen**: Arsitektur Menu Mobile-First, Spesifikasi Fitur Inti (Zero-Bloat), & Alur Pengguna per Role  
> **Prinsip Produk**: Anti-Bloatware (Hanya fitur esensial KBM vokasi SMK, tanpa chat room, forum tak terpakai, atau gamifikasi berlebihan)  
> **Dokumen Desain Terkait**: `design.md` (Soft Pastel & Capsule Bento UI)

---

## 1. Filosofi Produk & Prinsip "Zero-Bloat"
Untuk memastikan sistem LMS SMK ini benar-benar digunakan secara efektif oleh siswa dan guru melalui perangkat seluler (*smartphone*), seluruh fitur dirancang dengan prinsip:
1. **Maksimal 3 Ketukan (*3-Tap Rule*)**: Siswa dapat melakukan presensi atau mengumpulkan tugas maksimal dalam 3 ketukan dari halaman utama.
2. **Bebas Fitur Sampah (*No Feature Creep*)**: Tidak ada fitur media sosial internal, papan peringkat (*leaderboard*) yang membingungkan, atau pengaturan profil yang rumit.
3. **Navigasi Bawah Konsisten (*4-Tab Floating Pill*)**: Setiap *role* memiliki tepat 4 menu utama di navigasi bawah (*bottom bar*) agar mudah dijangkau dengan satu ibu jari.

---

## 2. Alur Otentikasi & Onboarding (Semua Role)

### 2.1 Layar Onboarding (First-Time Visit)
* **Visual Header**: Ilustrasi/foto pelajar dengan aksen lengkungan warna pastel di latar belakang.
* **Konten Kartu Bawah**: Judul sambutan tegas (*"Siap Memulai Perjalanan Belajarmu!"*), sub-teks singkat mengenai kemudahan akses KBM dan presensi SMK.
* **Komponen Aksi Utama**: Tombol *Slide/Tap-to-Continue* berbentuk kapsul besar dengan ikon ceklis di sisi kiri dan panah ganda `>>>` di sisi kanan.

### 2.2 Layar Login Terpadu (Single Sign-On Role Detection)
* **Input Kredensial**:
  * Kolom **NISN / NIP / Username** (Otomatis mendeteksi apakah pengguna adalah Siswa, Guru, Wali Kelas, Kepsek, Admin, atau Super Admin).
  * Kolom **Kata Sandi** dengan fitur tampilkan/sembunyikan.
* **Fitur Pendukung Esensial**:
  * *Ingat Sesi Saya* (Agar siswa tidak perlu login berulang kali setiap jam pelajaran).
  * *Lupa Kata Sandi* (Mengarahkan ke instruksi kontak Operator/Admin Sekolah — tanpa alur email reset otomatis yang sering gagal karena email siswa tidak aktif).

---

## 3. Spesifikasi Menu & Fitur Detail Per Role (Mobile View)

Setiap *role* menggunakan struktur **4 Tab Navigasi Bawah (*Floating Pill Navigation*)** ditambah **Drawer Samping / Menu Profil** untuk keluar sesi.

---

### 3.1 Role: SISWA (Peserta Belajar)
Fokus utama: **Hadir tepat waktu, unduh modul praktik, dan kumpulkan tugas sebelum tenggat.**

#### Tab 1: Beranda (`Home`)
* **Top Bar**: Tombol menu cepat (kiri), sapaan nama & kelas rombel (misal: *XII RPL 1*), serta foto profil (kanan).
* **Hero Card — Kelas Saat Ini (*Today's Active Class*)**:
  * Kartu bergradasi pastel (*Sage Green* atau *Soft Pink*) yang menampilkan mata pelajaran yang sedang berlangsung saat ini.
  * Indikator status langsung: *"Sesi Aktif • Berakhir dalam 45 menit"* atau *"Presensi Dibuka"*.
  * Progres Bab (contoh: *Bab 06/10*) dan tombol bulat hitam (`Play/Masuk`) untuk langsung membuka materi & presensi.
* **Kalender Kapsul Mingguan (*Class Schedule*)**:
  * Pemilih bulan di kanan atas dan deretan 7 hari berbentuk kapsul vertikal (*Sun 17 - Sat 23*). Hari aktif berwarna hitam pekat (`#141416`).
* **Garis Waktu Harian (*Timeline KBM*)**:
  * Daftar jam pelajaran vertikal (`07:30`, `09:15`, `11:00`) dengan kartu kapsul mata pelajaran, ruang lab/kelas, dan ikon panah akses cepat.

#### Tab 2: Materi (`Kelas`)
* **Filter Mata Pelajaran**: Kapsul geser horizontal (*Semua, Produktif RPL, Matematika, B. Inggris*).
* **Kartu Sorotan Materi Terakhir (*Featured Subject Card*)**:
  * Menampilkan jam jadwal, nama mapel, tanggal, label tipe kelas (*Praktik Lab / Teori*), dan nama guru pengampu.
* **Daftar Modul & Bab (*Last Lessons List*)**:
  * Daftar kartu kapsul putih dengan ikon bulat pastel di kiri, judul bab, nama guru, serta status kunci:
    * **Terbuka**: Dapat diketuk untuk membaca ringkasan materi dan mengunduh file PDF/Video.
    * **Terkunci**: Ditandai ikon gembok jika guru belum membuka bab tersebut.

#### Tab 3: Tugas (`Tugas`)
* **Tab Filter Status**: *Perlu Dikumpulkan (Aktif)*, *Menunggu Nilai*, dan *Selesai (Dinilai)*.
* **Kartu Tugas**:
  * Menampilkan judul tugas, mata pelajaran, dan label tenggat waktu (*Due Date*).
* **Lembar Kerja Pengumpulan (*Bottom Sheet / Detail View*)**:
  * Instruksi singkat dari guru dan unduhan lembar kerja (*Jobsheet*).
  * Area unggah file tunggal (*PDF / ZIP / Gambar*) dengan batas ukuran jelas.
  * Status Nilai & Umpan Balik: Jika sudah dinilai, menampilkan skor angka (`0-100`), komentar guru, dan tombol **Unggah Revisi** (hanya muncul jika guru mengaktifkan izin revisi).

#### Tab 4: Presensi & Nilai (`Rekap`)
* **Kartu Aksi Presensi Mandiri**:
  * Muncul paling atas jika ada guru yang sedang membuka gerbang waktu presensi. Siswa cukup menekan tombol **"Konfirmasi Hadir Sekarang"** dalam batas waktu hitung mundur (*timer*).
* **Ringkasan Kehadiran Semester**:
  * 4 Kotak metrik ringkas: Total **Hadir**, **Izin**, **Sakit**, dan **Alpa**.
* **Daftar Transkrip Nilai Mapel**:
  * Rata-rata nilai tugas per mata pelajaran secara transparan.

---

### 3.2 Role: GURU MATA PELAJARAN (Fasilitator KBM)
Fokus utama: **Buka sesi & presensi kelas dengan cepat, atur kunci modul, dan nilai tugas siswa dari HP.**

#### Tab 1: Beranda Mengajar (`Home`)
* **Hero Card — Jadwal Mengajar Saat Ini**:
  * Menampilkan kelas yang sedang/akan diajar (misal: *XI TKJ 2 — Administrasi Server*), jam sesi, serta tombol cepat **"Buka Presensi (15 Menit)"**.
* **Kalender Kapsul & Timeline Mengajar**:
  * Jadwal mengajar harian guru lintas kelas/rombel pada hari tersebut.
* **Widget Antrean Penilaian (*Needs Grading*)**:
  * Kartu peringatan ringkas berisi jumlah pengumpulan tugas baru yang belum diberi nilai.

#### Tab 2: Kelola Materi (`Materi`)
* **Pemilih Kelas & Mapel**: Dropdown kapsul untuk memilih mata pelajaran yang diampu.
* **Daftar Bab Materi**:
  * Setiap kartu bab memiliki **Sakelar Kunci/Buka (*Lock/Unlock Toggle*)** langsung di daftar agar guru bisa membuka materi tepat saat masuk kelas.
* **Form Tambah/Sunting Materi**:
  * Input Judul Bab, Pilih Kelas Penerima (bisa lebih dari satu rombel paralel), Teks Ringkasan, dan Unggah Modul PDF/Tautan Video.

#### Tab 3: Tugas & Penilaian (`Nilai`)
* **Daftar Tugas per Kelas**: Menampilkan progres pengumpulan (contoh: *28/32 Siswa Mengumpulkan*).
* **Antarmuka Penilaian Cepat (*Quick Grader*)**:
  * Daftar nama siswa yang sudah mengumpulkan tugas.
  * Ketuk siswa -> Lihat/Unduh berkas -> Input angka nilai (`0-100`), ketik catatan feedback singkat, dan centang opsi *"Izinkan Kumpul Ulang (Revisi)"* bila nilai di bawah KKM.

#### Tab 4: Sesi & Presensi (`Presensi`)
* **Kontrol Gerbang Presensi**:
  * Buat sesi pertemuan baru, atur durasi timer presensi mandiri (10/15/30 menit).
* **Daftar Hadir Siswa Manual**:
  * Daftar seluruh siswa di kelas tersebut dengan pemilih status cepat (**H**adir / **I**zin / **S**akit / **A**lpa) untuk mengubah status siswa yang terkendala perangkat.

---

### 3.3 Role: GURU WALI KELAS (Pembina Rombel)
Fokus utama: **Deteksi dini siswa bermasalah (alpa/nilai rendah) dan rekap perwalian satu rombel.**

#### Tab 1: Dasbor Perwalian (`Home`)
* **Hero Card — Identitas Rombel Binaan**:
  * Menampilkan nama kelas binaan (misal: *Wali Kelas X DKV 1*), persentase kehadiran kelas hari ini, dan total siswa.
* **Peringatan Dini (*Early Warning System*)**:
  * Daftar kartu siswa yang memerlukan perhatian khusus (contoh: *Alpa $\ge 3$ hari* atau *Rata-rata nilai tugas $< 75$*).

#### Tab 2: Presensi Harian Rombel (`Absensi`)
* **Kalender Kapsul Harian**: Memilih tanggal pemeriksaan.
* **Verifikasi Kehadiran Perwalian**:
  * Melihat dan menginput presensi apel pagi/perwalian serta memantau rekap kehadiran siswa di setiap jam mata pelajaran hari itu.

#### Tab 3: Buku Nilai Rombel (`Leger`)
* **Daftar Siswa Binaan**:
  * Pencarian cepat nama siswa di kelas perwalian.
* **Kartu Rapor Bayangan Siswa**:
  * Saat nama siswa diketuk, menampilkan rincian nilai tugas dari seluruh mata pelajaran yang diikuti siswa tersebut beserta persentase kehadirannya.

#### Tab 4: Rekap Bulanan (`Laporan`)
* **Ringkasan Kehadiran Bulanan Kelas**: Statistik akumulatif Hadir, Sakit, Izin, Alpa satu kelas penuh.
* **Unduh Ringkasan Kelas**: Tombol unduh PDF/Excel rekap kelas untuk bahan rapat orang tua/pembinaan.

---

### 3.4 Role: KEPALA SEKOLAH (Eksekutif & Monitoring)
Fokus utama: **Pemantauan kinerja KBM satu sekolah secara makro dan unduh laporan resmi (Read-Only).**

#### Tab 1: Pantauan Eksekutif (`Home`)
* **Hero Card — Indikator KBM Hari Ini**:
  * Persentase sesi kelas yang terlaksana hari ini (contoh: *94% Kelas Aktif*), tingkat kehadiran siswa nasional sekolah, dan ketertiban pengisian materi guru.
* **Grafik Ringkas Kehadiran Mingguan**: Visualisasi tren kehadiran 7 hari terakhir per tingkat (Kelas X, XI, XII).

#### Tab 2: Analitik Jurusan (`Jurusan`)
* **Daftar Kompetensi Keahlian (RPL, TKJ, DKV, Akuntansi)**:
  * Menampilkan rata-rata nilai akademik dan tingkat kehadiran masing-masing jurusan.

#### Tab 3: Kinerja Guru (`KBM`)
* **Monitoring Keaktifan Pengajar**:
  * Daftar ringkas ketertiban guru dalam mengunggah materi ajar dan persentase tugas siswa yang sudah dinilai tepat waktu.

#### Tab 4: Pusat Unduh Laporan (`Ekspor`)
* **Generator Dokumen Resmi**:
  * Pilih periode semester/bulan -> Unduh **Laporan Kehadiran Sekolah (PDF/Excel)** atau **Leger Nilai Akademik Sekolah (PDF/Excel)** sekali ketuk.

---

### 3.5 Role: ADMIN (Operator Sekolah)
Fokus utama: **Operasional harian: koreksi absen, plotting jadwal/guru, dan kelola data siswa.**

#### Tab 1: Dasbor Operator (`Home`)
* **Hero Card — Status Operasional Harian**:
  * Total siswa terdaftar, total guru aktif, dan sesi KBM berjalan hari ini.
* **Aksi Cepat Operator**: Tombol pintas *Reset Sandi Pengguna*, *Import Siswa CSV*, dan *Koreksi Presensi*.

#### Tab 2: Direktori Pengguna (`Pengguna`)
* **Pencarian Siswa & Guru**: Filter berdasarkan Rombel atau Role.
* **Aksi Kartu Pengguna**:
  * Tambah siswa baru, pindah rombel kelas, atau tekan tombol **Reset Kata Sandi Instan** (menampilkan sandi sementara untuk diberikan ke siswa/guru).

#### Tab 3: Plotting Kelas & Mapel (`Plotting`)
* **Daftar Rombongan Belajar**:
  * Menetapkan **Wali Kelas** untuk setiap rombel.
  * Menambahkan pasangan **Mata Pelajaran + Guru Pengampu** ke dalam rombel terkait.

#### Tab 4: Koreksi & Moderasi (`Moderasi`)
* **Koreksi Presensi (*Attendance Adjustment*)**:
  * Mengubah status kehadiran siswa pada tanggal lampau (misal: mengubah status *Alpa* menjadi *Sakit* setelah surat dokter diserahkan ke TU).
* **Moderasi Materi**: Meninjau daftar materi yang diunggah guru bila diperlukan penertiban konten.

---

### 3.6 Role: SUPER ADMIN (Otoritas Sistem & Master Data)
Fokus utama: **Konfigurasi tahun ajaran, struktur data master sekolah, manajemen akun pejabat, dan audit log.**

#### Tab 1: Pusat Sistem (`Home`)
* **Hero Card — Status Tahun Ajaran Aktif**:
  * Menampilkan Tahun Ajaran berjalan (misal: *2026/2027 — Semester Ganjil*), kesehatan layanan, dan ringkasan total entitas database.
* **Pengaturan Parameter Global**:
  * Sakelar batas ukuran unggahan berkas (misal: Maks 10 MB), format file yang diizinkan, dan penguncian semester saat masa rapor.

#### Tab 2: Data Master (`Master`)
* **Manajemen Entitas Inti**:
  * Kelola **Jurusan / Program Keahlian** (Kode & Nama Jurusan).
  * Kelola **Tingkat & Ruang Kelas** (X, XI, XII).
  * Kelola **Master Mata Pelajaran** (Muatan Nasional, Kewilayahan, Produktif Kejuruan).

#### Tab 3: Akun Pejabat & Role (`Akses`)
* **Kelola Akun Khusus**:
  * Pembuatan dan penonaktifan akun **Admin Operator**, **Kepala Sekolah**, dan **Guru**.
* **Matriks Hak Akses Role**: Mengatur izin modul untuk ke-6 role pengguna.

#### Tab 4: Audit Log (`Log`)
* **Rekam Jejak Keamanan (*System Trail*)**:
  * Daftar kronologis aktivitas kritikal (siapa mengubah nilai, siapa mengoreksi presensi, dan riwayat login pejabat sekolah) lengkap dengan stempel waktu.