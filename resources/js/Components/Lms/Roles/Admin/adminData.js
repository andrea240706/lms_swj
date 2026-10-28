/**
 * Data Model Admin Operator Sekolah (Operasional Akademik)
 * LMS SMK SWJ
 */

export const adminStats = {
    totalSiswa: 1240,
    totalGuru: 68,
    totalWaliKelas: 42,
    totalRombel: 42,
    totalMapel: 58,
    jadwalTerverifikasi: 168,
    konflikJadwal: 0,
    tahunAjaranAktif: '2026/2027 • Ganjil',
    terakhirSinkronDapodik: '28 Okt 2026, 06:30 WIB',
    statusSinkron: 'Tersinkron Penuh',
};

export const initialUsersList = [
    // SISWA
    { id: 1, role: 'siswa', name: 'Nadia Putri Maharani', identifier: '008492104', rombel: 'XII RPL 1', email: 'nadia.putri@smkswj.sch.id', status: 'Aktif', sandiStatus: 'Default', lastLogin: 'Hari ini, 07:12' },
    { id: 2, role: 'siswa', name: 'Fajar Nugraha Pratama', identifier: '008492105', rombel: 'XII RPL 1', email: 'fajar.nugraha@smkswj.sch.id', status: 'Aktif', sandiStatus: 'Kustom', lastLogin: 'Hari ini, 07:25' },
    { id: 3, role: 'siswa', name: 'Aldiansyah Putra', identifier: '009112001', rombel: 'X DKV 1', email: 'aldiansyah.p@smkswj.sch.id', status: 'Pantauan', sandiStatus: 'Default', lastLogin: '3 hari lalu' },
    { id: 4, role: 'siswa', name: 'Salsabila Azzahra', identifier: '009112002', rombel: 'X DKV 1', email: 'salsabila.a@smkswj.sch.id', status: 'Aktif', sandiStatus: 'Kustom', lastLogin: 'Hari ini, 06:55' },
    { id: 5, role: 'siswa', name: 'Dimas Anggara Putra', identifier: '008492106', rombel: 'XII RPL 2', email: 'dimas.anggara@smkswj.sch.id', status: 'Terkunci', sandiStatus: 'Reset Dibutuhkan', lastLogin: 'Kemarin, 14:10' },
    { id: 6, role: 'siswa', name: 'Bagas Pratama', identifier: '009112003', rombel: 'X DKV 1', email: 'bagas.pratama@smkswj.sch.id', status: 'Aktif', sandiStatus: 'Kustom', lastLogin: 'Hari ini, 06:40' },

    // GURU
    { id: 101, role: 'guru', name: 'Budi Santoso, S.Kom', identifier: '19880412 201504 1 002', rombel: 'Guru Produktif RPL', email: 'budi.santoso@smkswj.sch.id', status: 'Aktif', sandiStatus: 'Kustom', lastLogin: 'Hari ini, 07:45' },
    { id: 102, role: 'guru', name: 'Andi Saputra, S.Sn', identifier: '19870311 201602 1 003', rombel: 'Guru Fotografi DKV', email: 'andi.saputra@smkswj.sch.id', status: 'Aktif', sandiStatus: 'Kustom', lastLogin: 'Hari ini, 07:05' },
    { id: 103, role: 'guru', name: 'Rina Kusuma, M.Kom', identifier: '19900915 201903 2 004', rombel: 'Guru Komputer Grafis', email: 'rina.kusuma@smkswj.sch.id', status: 'Aktif', sandiStatus: 'Kustom', lastLogin: 'Kemarin, 16:30' },
    { id: 104, role: 'guru', name: 'Dra. Sri Mulyani', identifier: '19680214 199403 2 001', rombel: 'Guru Bahasa Indonesia', email: 'sri.mulyani@smkswj.sch.id', status: 'Aktif', sandiStatus: 'Kustom', lastLogin: 'Hari ini, 07:50' },
    { id: 105, role: 'guru', name: 'Irfan Maulana, S.T', identifier: '19910522 201802 1 005', rombel: 'Guru Produktif TKJ', email: 'irfan.maulana@smkswj.sch.id', status: 'Aktif', sandiStatus: 'Kustom', lastLogin: 'Hari ini, 08:00' },

    // WALI KELAS
    { id: 201, role: 'wali', name: 'Siti Rahma, M.Pd', identifier: '19890520 201801 2 003', rombel: 'Wali Kelas X DKV 1', email: 'siti.rahma@smkswj.sch.id', status: 'Aktif', sandiStatus: 'Kustom', lastLogin: 'Hari ini, 06:50' },
    { id: 202, role: 'wali', name: 'Dra. Endang Setyawati', identifier: '19700812 199702 2 002', rombel: 'Wali Kelas XII RPL 1', email: 'endang.setyawati@smkswj.sch.id', status: 'Aktif', sandiStatus: 'Kustom', lastLogin: 'Hari ini, 07:15' },
    { id: 203, role: 'wali', name: 'Ahmad Fauzi, M.Kom', identifier: '19850614 201201 1 004', rombel: 'Wali Kelas XII RPL 2', email: 'ahmad.fauzi@smkswj.sch.id', status: 'Aktif', sandiStatus: 'Kustom', lastLogin: 'Hari ini, 07:20' },
    { id: 204, role: 'wali', name: 'Dra. Sri Wahyuni', identifier: '19720419 199803 2 001', rombel: 'Wali Kelas XI AKL 1', email: 'sri.wahyuni@smkswj.sch.id', status: 'Aktif', sandiStatus: 'Kustom', lastLogin: 'Kemarin, 15:45' },
];

export const masterTahunAjaran = [
    { id: 1, tahun: '2026/2027', semester: 'Ganjil', status: 'Aktif', mulai: '13 Juli 2026', selesai: '19 Des 2026', totalHariEfektif: 108 },
    { id: 2, tahun: '2025/2026', semester: 'Genap', status: 'Arsip', mulai: '05 Jan 2026', selesai: '20 Juni 2026', totalHariEfektif: 112 },
    { id: 3, tahun: '2025/2026', semester: 'Ganjil', status: 'Arsip', mulai: '14 Juli 2025', selesai: '20 Des 2025', totalHariEfektif: 110 },
];

export const masterJurusan = [
    { code: 'RPL', name: 'Rekayasa Perangkat Lunak', tingkat: '3 Tingkat (X, XI, XII)', rombelCount: 12, siswaCount: 412, kepalaLab: 'Budi Santoso, S.Kom' },
    { code: 'TKJ', name: 'Teknik Komputer & Jaringan', tingkat: '3 Tingkat (X, XI, XII)', rombelCount: 12, siswaCount: 398, kepalaLab: 'Irfan Maulana, S.T' },
    { code: 'DKV', name: 'Desain Komunikasi Visual', tingkat: '3 Tingkat (X, XI, XII)', rombelCount: 10, siswaCount: 345, kepalaLab: 'Andi Saputra, S.Sn' },
    { code: 'AKL', name: 'Akuntansi & Keuangan Lembaga', tingkat: '3 Tingkat (X, XI, XII)', rombelCount: 8, siswaCount: 285, kepalaLab: 'Dra. Sri Wahyuni' },
];

export const masterRombel = [
    { code: 'X-DKV-1', name: 'X DKV 1', jurusan: 'DKV', tingkat: 'Kelas X', kapasitas: 36, terisi: 36, ruang: 'Studio DKV 1', wali: 'Siti Rahma, M.Pd' },
    { code: 'X-DKV-2', name: 'X DKV 2', jurusan: 'DKV', tingkat: 'Kelas X', kapasitas: 36, terisi: 35, ruang: 'Studio DKV 2', wali: 'Rina Kusuma, M.Kom' },
    { code: 'X-RPL-1', name: 'X RPL 1', jurusan: 'RPL', tingkat: 'Kelas X', kapasitas: 36, terisi: 35, ruang: 'Ruang Teori 1', wali: 'Fajar Nugroho, S.Pd' },
    { code: 'X-TKJ-1', name: 'X TKJ 1', jurusan: 'TKJ', tingkat: 'Kelas X', kapasitas: 36, terisi: 34, ruang: 'Lab Komputer 3', wali: 'Irfan Maulana, S.T' },
    { code: 'XI-RPL-1', name: 'XI RPL 1', jurusan: 'RPL', tingkat: 'Kelas XI', kapasitas: 36, terisi: 35, ruang: 'Lab RPL 1', wali: 'Hendra Setiawan, M.T' },
    { code: 'XI-AKL-1', name: 'XI AKL 1', jurusan: 'AKL', tingkat: 'Kelas XI', kapasitas: 36, terisi: 36, ruang: 'Ruang Teori 4', wali: 'Dra. Sri Wahyuni' },
    { code: 'XII-RPL-1', name: 'XII RPL 1', jurusan: 'RPL', tingkat: 'Kelas XII', kapasitas: 36, terisi: 34, ruang: 'Lab RPL 2', wali: 'Dra. Endang Setyawati' },
    { code: 'XII-RPL-2', name: 'XII RPL 2', jurusan: 'RPL', tingkat: 'Kelas XII', kapasitas: 36, terisi: 33, ruang: 'Lab RPL 1', wali: 'Ahmad Fauzi, M.Kom' },
    { code: 'XII-TKJ-1', name: 'XII TKJ 1', jurusan: 'TKJ', tingkat: 'Kelas XII', kapasitas: 36, terisi: 34, ruang: 'Lab Jaringan 1', wali: 'Bambang Irawan, S.T' },
];

export const masterMapel = [
    { code: 'PWPB', name: 'Pemrograman Web & Perangkat Bergerak', kategori: 'Kejuruan Produktif', rombelTingkat: 'Kelas XII RPL', jamPerMinggu: 8, kkm: 78 },
    { code: 'PBO', name: 'Pemrograman Berorientasi Objek', kategori: 'Kejuruan Produktif', rombelTingkat: 'Kelas XI RPL', jamPerMinggu: 6, kkm: 75 },
    { code: 'DKV', name: 'Dasar Desain Komunikasi Visual', kategori: 'Kejuruan Produktif', rombelTingkat: 'Kelas X DKV', jamPerMinggu: 6, kkm: 75 },
    { code: 'FTC', name: 'Fotografi & Tata Cahaya', kategori: 'Kejuruan Produktif', rombelTingkat: 'Kelas X & XI DKV', jamPerMinggu: 6, kkm: 75 },
    { code: 'AIJ', name: 'Administrasi Infrastruktur Jaringan', kategori: 'Kejuruan Produktif', rombelTingkat: 'Kelas XII TKJ', jamPerMinggu: 8, kkm: 75 },
    { code: 'BIN', name: 'Bahasa Indonesia', kategori: 'Muatan Umum', rombelTingkat: 'Semua Tingkat', jamPerMinggu: 4, kkm: 75 },
    { code: 'MTK', name: 'Matematika Terapan Vokasi', kategori: 'Muatan Umum', rombelTingkat: 'Semua Tingkat', jamPerMinggu: 4, kkm: 70 },
    { code: 'BIG', name: 'Bahasa Inggris Kejuruan', kategori: 'Muatan Umum', rombelTingkat: 'Semua Tingkat', jamPerMinggu: 3, kkm: 75 },
];

export const initialPlottingWali = [
    { id: 1, rombel: 'X DKV 1', jurusan: 'DKV', tingkat: 'Kelas X', waliName: 'Siti Rahma, M.Pd', nip: '19890520 201801 2 003', siswaCount: 36, ruang: 'Studio DKV 1', status: 'Terplot' },
    { id: 2, rombel: 'X DKV 2', jurusan: 'DKV', tingkat: 'Kelas X', waliName: 'Rina Kusuma, M.Kom', nip: '19900915 201903 2 004', siswaCount: 35, ruang: 'Studio DKV 2', status: 'Terplot' },
    { id: 3, rombel: 'X RPL 1', jurusan: 'RPL', tingkat: 'Kelas X', waliName: 'Fajar Nugroho, S.Pd', nip: '19860314 201402 1 002', siswaCount: 35, ruang: 'Ruang Teori 1', status: 'Terplot' },
    { id: 4, rombel: 'X TKJ 1', jurusan: 'TKJ', tingkat: 'Kelas X', waliName: 'Irfan Maulana, S.T', nip: '19910522 201802 1 005', siswaCount: 34, ruang: 'Lab Komputer 3', status: 'Terplot' },
    { id: 5, rombel: 'XI RPL 1', jurusan: 'RPL', tingkat: 'Kelas XI', waliName: 'Hendra Setiawan, M.T', nip: '19820411 200801 1 007', siswaCount: 35, ruang: 'Lab RPL 1', status: 'Terplot' },
    { id: 6, rombel: 'XI AKL 1', jurusan: 'AKL', tingkat: 'Kelas XI', waliName: 'Dra. Sri Wahyuni', nip: '19720419 199803 2 001', siswaCount: 36, ruang: 'Ruang Teori 4', status: 'Terplot' },
    { id: 7, rombel: 'XII RPL 1', jurusan: 'RPL', tingkat: 'Kelas XII', waliName: 'Dra. Endang Setyawati', nip: '19700812 199702 2 002', siswaCount: 34, ruang: 'Lab RPL 2', status: 'Terplot' },
    { id: 8, rombel: 'XII RPL 2', jurusan: 'RPL', tingkat: 'Kelas XII', waliName: 'Ahmad Fauzi, M.Kom', nip: '19850614 201201 1 004', siswaCount: 33, ruang: 'Lab RPL 1', status: 'Terplot' },
];

export const initialPlottingGuruMapel = [
    { id: 1, guruName: 'Budi Santoso, S.Kom', nip: '19880412 201504 1 002', mapel: 'Pemrograman Web & Perangkat Bergerak', rombelList: ['XII RPL 1', 'XII RPL 2'], totalJam: 16, ruang: 'Lab RPL 1 & 2' },
    { id: 2, guruName: 'Andi Saputra, S.Sn', nip: '19870311 201602 1 003', mapel: 'Fotografi & Tata Cahaya', rombelList: ['X DKV 1', 'X DKV 2', 'XI DKV 1'], totalJam: 18, ruang: 'Studio Fotografi' },
    { id: 3, guruName: 'Rina Kusuma, M.Kom', nip: '19900915 201903 2 004', mapel: 'Komputer Grafis & Tipografi', rombelList: ['X DKV 1', 'X DKV 2'], totalJam: 12, ruang: 'Lab Komputer DKV' },
    { id: 4, guruName: 'Dra. Sri Mulyani', nip: '19680214 199403 2 001', mapel: 'Bahasa Indonesia', rombelList: ['X DKV 1', 'XII RPL 1', 'XII RPL 2'], totalJam: 12, ruang: 'Ruang Teori' },
    { id: 5, guruName: 'Irfan Maulana, S.T', nip: '19910522 201802 1 005', mapel: 'Administrasi Infrastruktur Jaringan', rombelList: ['XII TKJ 1', 'XII TKJ 2'], totalJam: 16, ruang: 'Lab Komputer TKJ' },
    { id: 6, guruName: 'Agus Salim, M.Pd', nip: '19850719 201101 1 003', mapel: 'Matematika Terapan Vokasi', rombelList: ['X DKV 1', 'XI RPL 1', 'XII RPL 1'], totalJam: 12, ruang: 'Ruang Teori' },
];

export const initialJadwalMingguan = [
    { hari: 'Senin', jamKe: '1 - 3 (07:15 - 09:30)', mapel: 'Pemrograman Web & Perangkat Bergerak', rombel: 'XII RPL 1', guru: 'Budi Santoso, S.Kom', ruang: 'Lab RPL 2', status: 'Terjadwal' },
    { hari: 'Senin', jamKe: '4 - 6 (09:45 - 12:00)', mapel: 'Fotografi & Tata Cahaya', rombel: 'X DKV 1', guru: 'Andi Saputra, S.Sn', ruang: 'Studio Fotografi', status: 'Terjadwal' },
    { hari: 'Senin', jamKe: '7 - 8 (12:30 - 14:00)', mapel: 'Bahasa Indonesia', rombel: 'XII RPL 2', guru: 'Dra. Sri Mulyani', ruang: 'Ruang Teori 12', status: 'Terjadwal' },
    { hari: 'Selasa', jamKe: '1 - 3 (07:15 - 09:30)', mapel: 'Administrasi Infrastruktur Jaringan', rombel: 'XII TKJ 1', guru: 'Irfan Maulana, S.T', ruang: 'Lab Jaringan 1', status: 'Terjadwal' },
    { hari: 'Selasa', jamKe: '4 - 6 (09:45 - 12:00)', mapel: 'Dasar Desain Komunikasi Visual', rombel: 'X DKV 1', guru: 'Siti Rahma, M.Pd', ruang: 'Studio DKV 1', status: 'Terjadwal' },
    { hari: 'Rabu', jamKe: '1 - 3 (07:15 - 09:30)', mapel: 'Pemrograman Web & Perangkat Bergerak', rombel: 'XII RPL 2', guru: 'Budi Santoso, S.Kom', ruang: 'Lab RPL 1', status: 'Terjadwal' },
    { hari: 'Rabu', jamKe: '4 - 6 (09:45 - 12:00)', mapel: 'Matematika Terapan Vokasi', rombel: 'X DKV 1', guru: 'Agus Salim, M.Pd', ruang: 'Ruang Teori 12', status: 'Terjadwal' },
    { hari: 'Kamis', jamKe: '1 - 3 (07:15 - 09:30)', mapel: 'Komputer Grafis & Tipografi', rombel: 'X DKV 1', guru: 'Rina Kusuma, M.Kom', ruang: 'Lab Komputer DKV', status: 'Terjadwal' },
    { hari: 'Jumat', jamKe: '1 - 2 (07:15 - 08:45)', mapel: 'Pendidikan Agama & Budi Pekerti', rombel: 'XII RPL 1', guru: 'Ahmad Fauzi, S.Pd.I', ruang: 'Masjid & Ruang 10', status: 'Terjadwal' },
];

export const initialPengumumanList = [
    {
        id: 1,
        title: 'Jadwal Asesmen Tengah Semester (ATS) Ganjil TA 2026/2027',
        target: 'Semua Pengguna',
        targetBadge: 'bg-charcoal text-white',
        urgensi: 'Penting',
        urgensiBadge: 'bg-blossom-light text-blossom-dark',
        status: 'Tayang',
        date: '28 Okt 2026',
        author: 'Rizky Pratama (Operator TU)',
        content: 'Diberitahukan kepada seluruh Guru dan Siswa, ATS Ganjil akan diselenggarakan mulai tanggal 03 November 2026 secara daring melalui sistem CBT LMS SWJ.',
        views: 1140,
    },
    {
        id: 2,
        title: 'Penertiban Presensi Pagi & Pembinaan Siswa oleh Wali Kelas',
        target: 'Khusus Wali Kelas & Guru',
        targetBadge: 'bg-sky-light text-sky-dark',
        urgensi: 'Normal',
        urgensiBadge: 'bg-sage-light text-sage-dark',
        status: 'Tayang',
        date: '27 Okt 2026',
        author: 'Rizky Pratama (Operator TU)',
        content: 'Wali kelas dimohon mengunci presensi pagi sebelum jam 07:30 WIB setiap hari agar data siswa alpa dapat langsung ditindaklanjuti oleh guru piket dan BK.',
        views: 95,
    },
    {
        id: 3,
        title: 'Dispensasi Kontingen Siswa Lomba Keterampilan Siswa (LKS) Provinsi',
        target: 'Khusus Guru Pengampu',
        targetBadge: 'bg-lavender-light text-lavender-dark',
        urgensi: 'Khusus',
        urgensiBadge: 'bg-[#FFF3E0] text-[#E65100]',
        status: 'Tayang',
        date: '25 Okt 2026',
        author: 'Rizky Pratama (Operator TU)',
        content: 'Daftar nama 6 siswa perwakilan kompetensi keahlian RPL dan DKV yang bertanding di LKS Provinsi Jabar terlampir untuk dispensasi presensi dan tugas.',
        views: 68,
    },
    {
        id: 4,
        title: 'Pemeliharaan Server Database & Backup Rutin Dapodik',
        target: 'Semua Pengguna',
        targetBadge: 'bg-charcoal text-white',
        urgensi: 'Info',
        urgensiBadge: 'bg-muted text-subtext',
        status: 'Tayang',
        date: '20 Okt 2026',
        author: 'Rizky Pratama (Operator TU)',
        content: 'Maintenance rutin server LMS akan dilakukan pada hari Sabtu malam pukul 23:00 - 02:00 WIB. Layanan akan offline sementara.',
        views: 890,
    },
];
