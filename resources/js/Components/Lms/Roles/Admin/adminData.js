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

    // KEPALA SEKOLAH
    { id: 301, role: 'kepsek', name: 'Drs. Hendra Wijaya, M.M', identifier: '19650812 199003 1 004', rombel: 'Kepala Sekolah SMK SWJ', email: 'kepsek@smk.sch.id', status: 'Aktif', sandiStatus: 'Kustom', lastLogin: 'Hari ini, 06:15' },
];

export const masterTahunAjaran = [
    { id: 1, tahun: '2026/2027', semester: 'Ganjil', status: 'Aktif', mulai: '13 Juli 2026', selesai: '19 Des 2026', totalHariEfektif: 108 },
    { id: 2, tahun: '2025/2026', semester: 'Genap', status: 'Arsip', mulai: '05 Jan 2026', selesai: '20 Juni 2026', totalHariEfektif: 112 },
    { id: 3, tahun: '2025/2026', semester: 'Ganjil', status: 'Arsip', mulai: '14 Juli 2025', selesai: '20 Des 2025', totalHariEfektif: 110 },
];

export const masterJurusan = [
    { code: 'TBSM', name: 'Teknik & Bisnis Sepeda Motor', tingkat: '3 Tingkat (X, XI, XII)', rombelCount: 8, siswaCount: 280, kepalaLab: 'Hendra Gunawan, S.T' },
    { code: 'PBS', name: 'Perbankan Syariah', tingkat: '3 Tingkat (X, XI, XII)', rombelCount: 6, siswaCount: 210, kepalaLab: 'Nurul Hidayah, S.E.I' },
    { code: 'RPL', name: 'Rekayasa Perangkat Lunak', tingkat: '3 Tingkat (X, XI, XII)', rombelCount: 12, siswaCount: 412, kepalaLab: 'Budi Santoso, S.Kom' },
    { code: 'TKJ', name: 'Teknik Komputer & Jaringan', tingkat: '3 Tingkat (X, XI, XII)', rombelCount: 12, siswaCount: 398, kepalaLab: 'Irfan Maulana, S.T' },
    { code: 'DKV', name: 'Desain Komunikasi Visual', tingkat: '3 Tingkat (X, XI, XII)', rombelCount: 10, siswaCount: 345, kepalaLab: 'Andi Saputra, S.Sn' },
    { code: 'AKL', name: 'Akuntansi & Keuangan Lembaga', tingkat: '3 Tingkat (X, XI, XII)', rombelCount: 8, siswaCount: 285, kepalaLab: 'Dra. Sri Wahyuni' },
];

export const masterRombel = [
    // TINGKAT X
    { code: 'X-TBSM-A', name: 'X TBSM A', jurusan: 'TBSM', tingkat: 'Kelas X', kapasitas: 36, terisi: 35, ruang: 'Bengkel TBSM 1', wali: 'Hendra Gunawan, S.T' },
    { code: 'X-TBSM-B', name: 'X TBSM B', jurusan: 'TBSM', tingkat: 'Kelas X', kapasitas: 36, terisi: 34, ruang: 'Bengkel TBSM 2', wali: 'Dedi Kurniawan, S.Pd' },
    { code: 'X-PBS', name: 'X PBS', jurusan: 'PBS', tingkat: 'Kelas X', kapasitas: 36, terisi: 35, ruang: 'Mini Bank Syariah', wali: 'Nurul Hidayah, S.E.I' },
    { code: 'X-DKV-1', name: 'X DKV 1', jurusan: 'DKV', tingkat: 'Kelas X', kapasitas: 36, terisi: 36, ruang: 'Studio DKV 1', wali: 'Siti Rahma, M.Pd' },
    { code: 'X-RPL-1', name: 'X RPL 1', jurusan: 'RPL', tingkat: 'Kelas X', kapasitas: 36, terisi: 35, ruang: 'Ruang Teori 1', wali: 'Fajar Nugroho, S.Pd' },

    // TINGKAT XI
    { code: 'XI-TBSM', name: 'XI TBSM', jurusan: 'TBSM', tingkat: 'Kelas XI', kapasitas: 36, terisi: 35, ruang: 'Bengkel TBSM 1', wali: 'Aris Munandar, S.T' },
    { code: 'XI-PBS', name: 'XI PBS', jurusan: 'PBS', tingkat: 'Kelas XI', kapasitas: 36, terisi: 34, ruang: 'Mini Bank Syariah', wali: 'Zulfa Ramadhani, M.E' },
    { code: 'XI-RPL-1', name: 'XI RPL 1', jurusan: 'RPL', tingkat: 'Kelas XI', kapasitas: 36, terisi: 35, ruang: 'Lab RPL 1', wali: 'Hendra Setiawan, M.T' },
    { code: 'XI-AKL-1', name: 'XI AKL 1', jurusan: 'AKL', tingkat: 'Kelas XI', kapasitas: 36, terisi: 36, ruang: 'Ruang Teori 4', wali: 'Dra. Sri Wahyuni' },

    // TINGKAT XII
    { code: 'XII-TBSM', name: 'XII TBSM', jurusan: 'TBSM', tingkat: 'Kelas XII', kapasitas: 36, terisi: 33, ruang: 'Bengkel TBSM 2', wali: 'Agus Salim, M.Pd' },
    { code: 'XII-PBS', name: 'XII PBS', jurusan: 'PBS', tingkat: 'Kelas XII', kapasitas: 36, terisi: 35, ruang: 'Ruang Teori 6', wali: 'Ratna Dewi, S.Pd' },
    { code: 'XII-RPL-1', name: 'XII RPL 1', jurusan: 'RPL', tingkat: 'Kelas XII', kapasitas: 36, terisi: 34, ruang: 'Lab RPL 2', wali: 'Dra. Endang Setyawati' },
    { code: 'XII-RPL-2', name: 'XII RPL 2', jurusan: 'RPL', tingkat: 'Kelas XII', kapasitas: 36, terisi: 33, ruang: 'Lab RPL 1', wali: 'Ahmad Fauzi, M.Kom' },
];

export const masterMapel = [
    // PRODUKTIF TBSM
    { code: 'PMSM', name: 'Pemeliharaan Mesin Sepeda Motor', jurusan: 'TBSM', kategori: 'Kejuruan Produktif', rombelTingkat: 'Kelas X & XI TBSM', jamPerMinggu: 6, kkm: 75 },
    { code: 'PKSM', name: 'Pemeliharaan Listrik Sepeda Motor', jurusan: 'TBSM', kategori: 'Kejuruan Produktif', rombelTingkat: 'Kelas XI & XII TBSM', jamPerMinggu: 6, kkm: 75 },
    { code: 'PSSM', name: 'Pemeliharaan Sasis Sepeda Motor', jurusan: 'TBSM', kategori: 'Kejuruan Produktif', rombelTingkat: 'Kelas X & XII TBSM', jamPerMinggu: 6, kkm: 75 },

    // PRODUKTIF PBS
    { code: 'LPS', name: 'Layanan Lembaga Perbankan Syariah', jurusan: 'PBS', kategori: 'Kejuruan Produktif', rombelTingkat: 'Kelas X & XI PBS', jamPerMinggu: 6, kkm: 78 },
    { code: 'APS', name: 'Akuntansi Perbankan Syariah', jurusan: 'PBS', kategori: 'Kejuruan Produktif', rombelTingkat: 'Kelas XI & XII PBS', jamPerMinggu: 6, kkm: 75 },
    { code: 'PKS', name: 'Pengelolaan Kas & Kliring Perbankan', jurusan: 'PBS', kategori: 'Kejuruan Produktif', rombelTingkat: 'Kelas X & XII PBS', jamPerMinggu: 6, kkm: 75 },

    // PRODUKTIF LAIN
    { code: 'PWPB', name: 'Pemrograman Web & Perangkat Bergerak', jurusan: 'RPL', kategori: 'Kejuruan Produktif', rombelTingkat: 'Kelas XII RPL', jamPerMinggu: 8, kkm: 78 },
    { code: 'DKV', name: 'Dasar Desain Komunikasi Visual', jurusan: 'DKV', kategori: 'Kejuruan Produktif', rombelTingkat: 'Kelas X DKV', jamPerMinggu: 6, kkm: 75 },
    { code: 'AIJ', name: 'Administrasi Infrastruktur Jaringan', jurusan: 'TKJ', kategori: 'Kejuruan Produktif', rombelTingkat: 'Kelas XII TKJ', jamPerMinggu: 8, kkm: 75 },

    // MUATAN UMUM
    { code: 'BIN', name: 'Bahasa Indonesia', jurusan: 'Umum', kategori: 'Muatan Umum', rombelTingkat: 'Semua Tingkat', jamPerMinggu: 4, kkm: 75 },
    { code: 'MTK', name: 'Matematika Terapan Vokasi', jurusan: 'Umum', kategori: 'Muatan Umum', rombelTingkat: 'Semua Tingkat', jamPerMinggu: 4, kkm: 70 },
    { code: 'BIG', name: 'Bahasa Inggris Kejuruan', jurusan: 'Umum', kategori: 'Muatan Umum', rombelTingkat: 'Semua Tingkat', jamPerMinggu: 3, kkm: 75 },
    { code: 'PAI', name: 'Pendidikan Agama & Budi Pekerti', jurusan: 'Umum', kategori: 'Muatan Umum', rombelTingkat: 'Semua Tingkat', jamPerMinggu: 3, kkm: 75 },
];

export const masterRuanganList = [
    'Bengkel TBSM 1',
    'Bengkel TBSM 2',
    'Mini Bank Syariah',
    'Lab Komputer RPL 1',
    'Lab RPL 2',
    'Lab Komputer DKV',
    'Studio DKV 1',
    'Lab Jaringan TKJ',
    'Ruang Teori 1',
    'Ruang Teori 4',
    'Ruang Teori 6',
    'Ruang Teori 12',
];

export const slotJamPelajaran = [
    { id: 1, label: 'Jam 1 - 3 (07:15 - 09:30)', waktu: '07:15 - 09:30', durasiJam: 3 },
    { id: 2, label: 'Jam 4 - 6 (09:45 - 12:00)', waktu: '09:45 - 12:00', durasiJam: 3 },
    { id: 3, label: 'Jam 7 - 8 (12:45 - 14:15)', waktu: '12:45 - 14:15', durasiJam: 2 },
    { id: 4, label: 'Jam 9 - 10 (14:15 - 15:45)', waktu: '14:15 - 15:45', durasiJam: 2 },
];

export const initialPlottingWali = [
    { id: 1, rombel: 'X TBSM A', jurusan: 'TBSM', tingkat: 'Kelas X', waliName: 'Hendra Gunawan, S.T', nip: '19850314 201101 1 004', siswaCount: 35, ruang: 'Bengkel TBSM 1', status: 'Terplot' },
    { id: 2, rombel: 'X TBSM B', jurusan: 'TBSM', tingkat: 'Kelas X', waliName: 'Dedi Kurniawan, S.Pd', nip: '19890422 201602 1 003', siswaCount: 34, ruang: 'Bengkel TBSM 2', status: 'Terplot' },
    { id: 3, rombel: 'X PBS', jurusan: 'PBS', tingkat: 'Kelas X', waliName: 'Nurul Hidayah, S.E.I', nip: '19910815 201903 2 006', siswaCount: 35, ruang: 'Mini Bank Syariah', status: 'Terplot' },
    { id: 4, rombel: 'XI TBSM', jurusan: 'TBSM', tingkat: 'Kelas XI', waliName: 'Aris Munandar, S.T', nip: '19870611 201402 1 005', siswaCount: 35, ruang: 'Bengkel TBSM 1', status: 'Terplot' },
    { id: 5, rombel: 'XI PBS', jurusan: 'PBS', tingkat: 'Kelas XI', waliName: 'Zulfa Ramadhani, M.E', nip: '19920317 202001 2 002', siswaCount: 34, ruang: 'Mini Bank Syariah', status: 'Terplot' },
    { id: 6, rombel: 'XII TBSM', jurusan: 'TBSM', tingkat: 'Kelas XII', waliName: 'Agus Salim, M.Pd', nip: '19850719 201101 1 003', siswaCount: 33, ruang: 'Bengkel TBSM 2', status: 'Terplot' },
    { id: 7, rombel: 'XII PBS', jurusan: 'PBS', tingkat: 'Kelas XII', waliName: 'Ratna Dewi, S.Pd', nip: '19881120 201503 2 004', siswaCount: 35, ruang: 'Ruang Teori 6', status: 'Terplot' },
    { id: 8, rombel: 'XII RPL 1', jurusan: 'RPL', tingkat: 'Kelas XII', waliName: 'Dra. Endang Setyawati', nip: '19700812 199702 2 002', siswaCount: 34, ruang: 'Lab RPL 2', status: 'Terplot' },
];

export const initialPlottingGuruMapel = [
    { id: 1, guruName: 'Hendra Gunawan, S.T', nip: '19850314 201101 1 004', mapel: 'Pemeliharaan Mesin Sepeda Motor', rombelList: ['X TBSM A', 'X TBSM B', 'XI TBSM'], totalJam: 18, ruang: 'Bengkel TBSM 1' },
    { id: 2, guruName: 'Dedi Kurniawan, S.Pd', nip: '19890422 201602 1 003', mapel: 'Pemeliharaan Listrik Sepeda Motor', rombelList: ['XI TBSM', 'XII TBSM'], totalJam: 12, ruang: 'Bengkel TBSM 2' },
    { id: 3, guruName: 'Nurul Hidayah, S.E.I', nip: '19910815 201903 2 006', mapel: 'Layanan Lembaga Perbankan Syariah', rombelList: ['X PBS', 'XI PBS'], totalJam: 12, ruang: 'Mini Bank Syariah' },
    { id: 4, guruName: 'Zulfa Ramadhani, M.E', nip: '19920317 202001 2 002', mapel: 'Akuntansi Perbankan Syariah', rombelList: ['XI PBS', 'XII PBS'], totalJam: 12, ruang: 'Mini Bank Syariah' },
    { id: 5, guruName: 'Budi Santoso, S.Kom', nip: '19880412 201504 1 002', mapel: 'Pemrograman Web & Perangkat Bergerak', rombelList: ['XII RPL 1', 'XII RPL 2'], totalJam: 16, ruang: 'Lab RPL 1 & 2' },
    { id: 6, guruName: 'Dra. Sri Mulyani', nip: '19680214 199403 2 001', mapel: 'Bahasa Indonesia', rombelList: ['X TBSM A', 'X TBSM B', 'X PBS', 'XII RPL 1'], totalJam: 16, ruang: 'Ruang Teori' },
    { id: 7, guruName: 'Agus Salim, M.Pd', nip: '19850719 201101 1 003', mapel: 'Matematika Terapan Vokasi', rombelList: ['X TBSM A', 'XI TBSM', 'XII TBSM'], totalJam: 12, ruang: 'Bengkel TBSM 1' },
];

export const initialJadwalMingguan = [
    // SENIN
    { id: 101, ta: '2026/2027 • Ganjil', hari: 'Senin', jamKe: 'Jam 1 - 3 (07:15 - 09:30)', mapel: 'Pemeliharaan Mesin Sepeda Motor', rombel: 'X TBSM A', guru: 'Hendra Gunawan, S.T', ruang: 'Bengkel TBSM 1', status: 'Terjadwal' },
    { id: 102, ta: '2026/2027 • Ganjil', hari: 'Senin', jamKe: 'Jam 1 - 3 (07:15 - 09:30)', mapel: 'Layanan Lembaga Perbankan Syariah', rombel: 'X PBS', guru: 'Nurul Hidayah, S.E.I', ruang: 'Mini Bank Syariah', status: 'Terjadwal' },
    { id: 103, ta: '2026/2027 • Ganjil', hari: 'Senin', jamKe: 'Jam 4 - 6 (09:45 - 12:00)', mapel: 'Pemeliharaan Mesin Sepeda Motor', rombel: 'X TBSM B', guru: 'Hendra Gunawan, S.T', ruang: 'Bengkel TBSM 2', status: 'Terjadwal' },
    { id: 104, ta: '2026/2027 • Ganjil', hari: 'Senin', jamKe: 'Jam 4 - 6 (09:45 - 12:00)', mapel: 'Bahasa Indonesia', rombel: 'X PBS', guru: 'Dra. Sri Mulyani', ruang: 'Mini Bank Syariah', status: 'Terjadwal' },
    { id: 105, ta: '2026/2027 • Ganjil', hari: 'Senin', jamKe: 'Jam 7 - 8 (12:45 - 14:15)', mapel: 'Bahasa Indonesia', rombel: 'X TBSM A', guru: 'Dra. Sri Mulyani', ruang: 'Ruang Teori 1', status: 'Terjadwal' },

    // SELASA
    { id: 106, ta: '2026/2027 • Ganjil', hari: 'Selasa', jamKe: 'Jam 1 - 3 (07:15 - 09:30)', mapel: 'Pemeliharaan Listrik Sepeda Motor', rombel: 'XI TBSM', guru: 'Dedi Kurniawan, S.Pd', ruang: 'Bengkel TBSM 2', status: 'Terjadwal' },
    { id: 107, ta: '2026/2027 • Ganjil', hari: 'Selasa', jamKe: 'Jam 1 - 3 (07:15 - 09:30)', mapel: 'Akuntansi Perbankan Syariah', rombel: 'XI PBS', guru: 'Zulfa Ramadhani, M.E', ruang: 'Mini Bank Syariah', status: 'Terjadwal' },
    { id: 108, ta: '2026/2027 • Ganjil', hari: 'Selasa', jamKe: 'Jam 4 - 6 (09:45 - 12:00)', mapel: 'Matematika Terapan Vokasi', rombel: 'X TBSM A', guru: 'Agus Salim, M.Pd', ruang: 'Bengkel TBSM 1', status: 'Terjadwal' },
    { id: 109, ta: '2026/2027 • Ganjil', hari: 'Selasa', jamKe: 'Jam 4 - 6 (09:45 - 12:00)', mapel: 'Pengelolaan Kas & Kliring Perbankan', rombel: 'X PBS', guru: 'Nurul Hidayah, S.E.I', ruang: 'Mini Bank Syariah', status: 'Terjadwal' },

    // RABU
    { id: 110, ta: '2026/2027 • Ganjil', hari: 'Rabu', jamKe: 'Jam 1 - 3 (07:15 - 09:30)', mapel: 'Pemeliharaan Mesin Sepeda Motor', rombel: 'XI TBSM', guru: 'Hendra Gunawan, S.T', ruang: 'Bengkel TBSM 1', status: 'Terjadwal' },
    { id: 111, ta: '2026/2027 • Ganjil', hari: 'Rabu', jamKe: 'Jam 1 - 3 (07:15 - 09:30)', mapel: 'Pemeliharaan Listrik Sepeda Motor', rombel: 'XII TBSM', guru: 'Dedi Kurniawan, S.Pd', ruang: 'Bengkel TBSM 2', status: 'Terjadwal' },
    { id: 112, ta: '2026/2027 • Ganjil', hari: 'Rabu', jamKe: 'Jam 4 - 6 (09:45 - 12:00)', mapel: 'Akuntansi Perbankan Syariah', rombel: 'XII PBS', guru: 'Zulfa Ramadhani, M.E', ruang: 'Ruang Teori 6', status: 'Terjadwal' },
    { id: 113, ta: '2026/2027 • Ganjil', hari: 'Rabu', jamKe: 'Jam 4 - 6 (09:45 - 12:00)', mapel: 'Matematika Terapan Vokasi', rombel: 'XI TBSM', guru: 'Agus Salim, M.Pd', ruang: 'Bengkel TBSM 1', status: 'Terjadwal' },

    // KAMIS
    { id: 114, ta: '2026/2027 • Ganjil', hari: 'Kamis', jamKe: 'Jam 1 - 3 (07:15 - 09:30)', mapel: 'Pemeliharaan Sasis Sepeda Motor', rombel: 'X TBSM A', guru: 'Hendra Gunawan, S.T', ruang: 'Bengkel TBSM 1', status: 'Terjadwal' },
    { id: 115, ta: '2026/2027 • Ganjil', hari: 'Kamis', jamKe: 'Jam 1 - 3 (07:15 - 09:30)', mapel: 'Layanan Lembaga Perbankan Syariah', rombel: 'XI PBS', guru: 'Nurul Hidayah, S.E.I', ruang: 'Mini Bank Syariah', status: 'Terjadwal' },
    { id: 116, ta: '2026/2027 • Ganjil', hari: 'Kamis', jamKe: 'Jam 4 - 6 (09:45 - 12:00)', mapel: 'Bahasa Indonesia', rombel: 'X TBSM B', guru: 'Dra. Sri Mulyani', ruang: 'Bengkel TBSM 2', status: 'Terjadwal' },

    // JUMAT
    { id: 117, ta: '2026/2027 • Ganjil', hari: 'Jumat', jamKe: 'Jam 1 - 2 (07:15 - 08:45)', mapel: 'Pendidikan Agama & Budi Pekerti', rombel: 'X TBSM A', guru: 'Ahmad Fauzi, S.Pd.I', ruang: 'Bengkel TBSM 1', status: 'Terjadwal' },
    { id: 118, ta: '2026/2027 • Ganjil', hari: 'Jumat', jamKe: 'Jam 1 - 2 (07:15 - 08:45)', mapel: 'Pendidikan Agama & Budi Pekerti', rombel: 'X PBS', guru: 'Zulfa Ramadhani, M.E', ruang: 'Mini Bank Syariah', status: 'Terjadwal' },
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
