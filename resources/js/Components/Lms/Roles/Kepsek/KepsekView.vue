<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import LmsSkeletonLoader from '@/Components/Lms/LmsSkeletonLoader.vue';
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    activeTab: {
        type: String,
        default: 'dashboard',
    },
    isSidebarCollapsed: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['openMenu', 'openProfile', 'showToast', 'toggleSidebar', 'openSettings', 'selectTab']);

// ===== SKELETON LOADER =====
const isLoading = ref(true);
const triggerLoading = (ms = 400) => {
    isLoading.value = true;
    setTimeout(() => {
        isLoading.value = false;
    }, ms);
};
onMounted(() => {
    triggerLoading(500);
});
watch(() => props.activeTab, () => {
    triggerLoading(400);
});

// Tab helpers to support aliases seamlessly
const isCurrentTab = (t) => {
    const cur = props.activeTab;
    if (cur === t) return true;
    if (t === 'dashboard' && (cur === 'home' || cur === 'dashboard')) return true;
    if (t === 'kinerja' && (cur === 'jurusan' || cur === 'kinerja')) return true;
    if (t === 'laporan' && (cur === 'ekspor' || cur === 'laporan')) return true;
    return false;
};

const pageTitle = computed(() => {
    const cur = props.activeTab;
    if (cur === 'dashboard' || cur === 'home') return 'Pantauan Eksekutif Sekolah';
    if (cur === 'kbm') return 'Supervisi KBM Realtime';
    if (cur === 'kinerja' || cur === 'jurusan') return 'Supervisi Kinerja Mengajar Guru';
    if (cur === 'laporan' || cur === 'ekspor') return 'Pusat Dokumen & Laporan Eksekutif';
    return 'Pantauan Eksekutif Sekolah';
});

// ===== DATA MASTER SUPERVISI EKSEKUTIF =====
const majors = [
    { jur: 'Rekayasa Perangkat Lunak (RPL)', rombel: '12 Rombel', guru: '18 Guru', hadir: '97.2%', sesiBerjalan: '11/12 Sesi', lab: 'Lab Komputer 1 & 2' },
    { jur: 'Teknik Komputer & Jaringan (TKJ)', rombel: '12 Rombel', guru: '16 Guru', hadir: '95.6%', sesiBerjalan: '11/12 Sesi', lab: 'Lab Jaringan & Fiber' },
    { jur: 'Desain Komunikasi Visual (DKV)', rombel: '10 Rombel', guru: '14 Guru', hadir: '96.1%', sesiBerjalan: '9/10 Sesi', lab: 'Studio Desain & Foto' },
    { jur: 'Teknik Bisnis Sepeda Motor (TBSM)', rombel: '4 Rombel', guru: '8 Guru', hadir: '94.5%', sesiBerjalan: '4/4 Sesi', lab: 'Bengkel Otomotif' },
    { jur: 'Perbankan Syariah (PBS)', rombel: '4 Rombel', guru: '12 Guru', hadir: '96.8%', sesiBerjalan: '3/4 Sesi', lab: 'Mini Bank Syariah' },
];

// Sesi KBM Realtime
const kbmFilter = ref('semua');
const kbmSearch = ref('');

const allKbmSessions = [
    { id: 1, rombel: 'XII RPL 1', mapel: 'Pemrograman Web & Perangkat Bergerak', guru: 'Budi Santoso, S.Kom', ruang: 'Lab Komputer 2 (RPL)', status: 'Berlangsung', jam: 'Jam 1 - 3 (07:15 - 09:30)', hadirSiswa: '36/36 Hadir', catatan: 'Praktikum RESTful API' },
    { id: 2, rombel: 'XII TKJ 2', mapel: 'Administrasi Infrastruktur Jaringan', guru: 'Irfan Maulana, S.T', ruang: 'Lab Jaringan & Server', status: 'Berlangsung', jam: 'Jam 1 - 3 (07:15 - 09:30)', hadirSiswa: '35/36 Hadir', catatan: 'Konfigurasi Mikrotik Routing' },
    { id: 3, rombel: 'X DKV 1', mapel: 'Dasar Seni & Komposisi Fotografi', guru: 'Siti Rahma, M.Pd', ruang: 'Studio DKV & Foto', status: 'Berlangsung', jam: 'Jam 2 - 4 (08:00 - 10:15)', hadirSiswa: '34/36 Hadir', catatan: 'Praktek Tata Cahaya Indoor' },
    { id: 4, rombel: 'XI TBSM 1', mapel: 'Pemeliharaan Mesin Sepeda Motor', guru: 'Hendra Gunawan, S.T', ruang: 'Bengkel TBSM 1', status: 'Berlangsung', jam: 'Jam 1 - 4 (07:15 - 10:15)', hadirSiswa: '36/36 Hadir', catatan: 'Overhaul Sistem Bahan Bakar' },
    { id: 5, rombel: 'X PBS', mapel: 'Dasar Layanan Perbankan Syariah', guru: 'Zulfa Ramadhani, M.E', ruang: 'Mini Bank Syariah', status: 'Berlangsung', jam: 'Jam 3 - 5 (08:45 - 11:00)', hadirSiswa: '35/35 Hadir', catatan: 'Simulasi Customer Service' },
    { id: 6, rombel: 'XI RPL 2', mapel: 'Basis Data & SQL Lanjutan', guru: 'Eko Prabowo, S.Kom', ruang: 'Lab Komputer 1', status: 'Berlangsung', jam: 'Jam 3 - 5 (08:45 - 11:00)', hadirSiswa: '36/36 Hadir', catatan: 'Stored Procedure & Trigger' },
    { id: 7, rombel: 'X TBSM B', mapel: 'Bahasa Indonesia Vokasi', guru: 'Dra. Sri Mulyani', ruang: 'Ruang Teori 12', status: 'Terjadwal', jam: 'Jam 6 - 8 (11:15 - 13:30)', hadirSiswa: '33/36 Hadir', catatan: 'Presentasi Laporan Bengkel' },
    { id: 8, rombel: 'XI DKV 2', mapel: 'Animasi 2D & Motion Graphic', guru: 'Zainal Abidin, S.Sn', ruang: 'Studio Animasi Komputer', status: 'Terjadwal', jam: 'Jam 6 - 8 (11:15 - 13:30)', hadirSiswa: 'Belum Mulai', catatan: 'Rigging Karakter Animasi' },
    { id: 9, rombel: 'X RPL A', mapel: 'Pendidikan Agama & Budi Pekerti', guru: 'Ahmad Fauzi, S.Pd.I', ruang: 'Ruang Teori 4', status: 'Selesai', jam: 'Jam 1 - 2 (07:15 - 08:45)', hadirSiswa: '36/36 Hadir', catatan: 'Sesi Selesai Tepat Waktu' },
];

const filteredKbmSessions = computed(() => {
    return allKbmSessions.filter((s) => {
        const matchFilter =
            kbmFilter.value === 'semua' ||
            (kbmFilter.value === 'berlangsung' && s.status === 'Berlangsung') ||
            (kbmFilter.value === 'terjadwal' && s.status === 'Terjadwal') ||
            (kbmFilter.value === 'selesai' && s.status === 'Selesai');
        const q = kbmSearch.value.trim().toLowerCase();
        const matchSearch =
            !q ||
            s.rombel.toLowerCase().includes(q) ||
            s.mapel.toLowerCase().includes(q) ||
            s.guru.toLowerCase().includes(q) ||
            s.ruang.toLowerCase().includes(q);
        return matchFilter && matchSearch;
    });
});

// ===== DATA KINERJA GURU =====
const kinerjaSearch = ref('');
const kinerjaJurusanFilter = ref('semua');

const teacherPerformanceList = [
    { id: 1, name: 'Budi Santoso, S.Kom', nip: '19850412 201001 1 012', mapel: 'Pemrograman Web & Perangkat Bergerak', jur: 'RPL', jp: '24 JP', completedMeetings: 10, totalMeetings: 16, journalStatus: 'Tertib', moduleStatus: 'Terverifikasi' },
    { id: 2, name: 'Siti Rahma, M.Pd', nip: '19880723 201402 2 008', mapel: 'Dasar Seni & Komposisi Fotografi', jur: 'DKV', jp: '26 JP', completedMeetings: 11, totalMeetings: 16, journalStatus: 'Tertib', moduleStatus: 'Terverifikasi' },
    { id: 3, name: 'Irfan Maulana, S.T', nip: '19860315 201101 1 007', mapel: 'Administrasi Infrastruktur Jaringan', jur: 'TKJ', jp: '24 JP', completedMeetings: 9, totalMeetings: 16, journalStatus: 'Tertib', moduleStatus: 'Terverifikasi' },
    { id: 4, name: 'Hendra Gunawan, S.T', nip: '19820919 200801 1 005', mapel: 'Pemeliharaan Mesin Sepeda Motor', jur: 'TBSM', jp: '26 JP', completedMeetings: 10, totalMeetings: 16, journalStatus: 'Tertib', moduleStatus: 'Terverifikasi' },
    { id: 5, name: 'Dra. Sri Mulyani', nip: '19671103 199303 2 002', mapel: 'Bahasa Indonesia Vokasi', jur: 'Umum', jp: '28 JP', completedMeetings: 12, totalMeetings: 16, journalStatus: 'Tertib', moduleStatus: 'Terverifikasi' },
    { id: 6, name: 'Ahmad Fauzi, S.Pd.I', nip: '19890520 201503 1 006', mapel: 'Pendidikan Agama Islam & Budi Pekerti', jur: 'Umum', jp: '24 JP', completedMeetings: 10, totalMeetings: 16, journalStatus: 'Tertib', moduleStatus: 'Terverifikasi' },
    { id: 7, name: 'Zulfa Ramadhani, M.E', nip: '19910114 201802 2 011', mapel: 'Dasar Layanan Perbankan Syariah', jur: 'PBS', jp: '22 JP', completedMeetings: 9, totalMeetings: 16, journalStatus: 'Tertib', moduleStatus: 'Terverifikasi' },
    { id: 8, name: 'Eko Prabowo, S.Kom', nip: '19900827 201601 1 009', mapel: 'Basis Data & SQL Lanjutan', jur: 'RPL', jp: '24 JP', completedMeetings: 10, totalMeetings: 16, journalStatus: 'Tertib', moduleStatus: 'Terverifikasi' },
];

const filteredTeacherPerformance = computed(() => {
    return teacherPerformanceList.filter((t) => {
        const matchJur =
            kinerjaJurusanFilter.value === 'semua' ||
            (kinerjaJurusanFilter.value === 'kejuruan' && t.jur !== 'Umum') ||
            (kinerjaJurusanFilter.value === 'umum' && t.jur === 'Umum') ||
            t.jur.toLowerCase() === kinerjaJurusanFilter.value.toLowerCase();
        const q = kinerjaSearch.value.trim().toLowerCase();
        const matchSearch =
            !q ||
            t.name.toLowerCase().includes(q) ||
            t.mapel.toLowerCase().includes(q) ||
            t.nip.toLowerCase().includes(q);
        return matchJur && matchSearch;
    });
});

// ===== DOKUMEN LAPORAN RESMI (3 JENIS FORMAT: CSV, EXCEL, PDF) =====
const reportCategories = [
    {
        id: 'kbm_beban',
        title: 'Laporan Keterlaksanaan KBM & Rekap Jam Mengajar Guru',
        code: 'LAP-KBM-2026/G1',
        description: 'Rekapitulasi jam mengajar (JP) 68 tenaga pendidik, realisasi tatap muka, dan persentase kehadiran guru mengajar.',
        badge: 'Akademik & Kurikulum',
        pdfName: 'Laporan_KBM_Mengajar_Guru_Ganjil_2026.pdf',
        excelName: 'Template_Laporan_KBM_Dinas_Pendidikan.xlsx',
        csvName: 'Data_Mentah_Log_KBM_Harian.csv',
    },
    {
        id: 'presensi_sekolah',
        title: 'Rekapitulasi Presensi & Kedisiplinan Siswa Sekolah',
        code: 'LAP-PRES-2026/G1',
        description: 'Laporan makro kehadiran 42 rombel (1.240 siswa), rekap angka sakit, izin, alpa, dan evaluasi kedisiplinan semester berjalan.',
        badge: 'Kesiswaan & Disiplin',
        pdfName: 'Rekap_Presensi_Siswa_Sekolah_Resmi.pdf',
        excelName: 'Template_Rekap_Presensi_Rombel_Dapodik.xlsx',
        csvName: 'Log_Absensi_Harian_Siswa_Mentah.csv',
    },
    {
        id: 'silabus_kurikulum',
        title: 'Laporan Ketercapaian Kurikulum & Modul Ajar Vokasi',
        code: 'LAP-KUR-2026/VOK',
        description: 'Evaluasi ketuntasan materi ajar 5 program keahlian SMK (RPL, TKJ, DKV, TBSM, PBS) sesuai standar Kurikulum Merdeka.',
        badge: 'Supervisi Vokasi',
        pdfName: 'Laporan_Ketuntasan_Kurikulum_Merdeka_SMK.pdf',
        excelName: 'Template_Matriks_Capaian_Pembelajaran.xlsx',
        csvName: 'Data_Progres_Modul_Ajar_Semua_Guru.csv',
    },
    {
        id: 'utilisasi_lab',
        title: 'Laporan Inventaris & Utilisasi Laboratorium / Bengkel Vokasi',
        code: 'LAP-SARPRAS-2026/V1',
        description: 'Pemantauan jam terbang dan efektivitas penggunaan 12 laboratorium komputer, studio desain, dan bengkel praktik kerja.',
        badge: 'Sarana & Prasarana',
        pdfName: 'Laporan_Utilisasi_Lab_Bengkel_Resmi.pdf',
        excelName: 'Template_Pemakaian_Fasilitas_Vokasi.xlsx',
        csvName: 'Log_Penggunaan_Ruang_Praktik.csv',
    },
];

const handleDownloadReport = (format, report) => {
    if (format === 'pdf') {
        emit('showToast', `Mengunduh '${report.title}' [PDF Rapi Siap Cetak]...`);
    } else if (format === 'excel') {
        emit('showToast', `Mengunduh '${report.title}' [Excel Template Resmi Dinas]...`);
    } else if (format === 'csv') {
        emit('showToast', `Mengunduh '${report.title}' [CSV Data Mentah]...`);
    }
};
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <LmsTopBar
            :role="role"
            :isSidebarCollapsed="isSidebarCollapsed"
            :title="pageTitle"
            @openMenu="$emit('openMenu')"
            @openProfile="$emit('openProfile')"
            @openSettings="$emit('openSettings')"
            @toggleSidebar="$emit('toggleSidebar')"
        />

        <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
            <div class="w-full max-w-[1800px] mx-auto">

                <!-- SKELETON LOADER -->
                <LmsSkeletonLoader v-if="isLoading" type="card-list" :rows="4" />

                <!-- CONTENT -->
                <template v-else>
                    <!-- ======================================================== -->
                    <!-- TAB 1: DASHBOARD (PANTAUAN EKSEKUTIF HARI INI)           -->
                    <!-- ======================================================== -->
                    <div v-if="isCurrentTab('dashboard')" class="space-y-5">
                        <!-- Hero Banner Standar (115px) -->
                        <div class="hero-amber rounded-[28px] py-7 px-6 sm:py-8 sm:px-8 soft-shadow relative overflow-hidden flex flex-col justify-center min-h-[115px]">
                            <div class="relative z-10">
                                <h2 class="text-[22px] sm:text-[25px] font-black text-charcoal leading-tight">
                                    Pusat Kendali Eksekutif
                                </h2>
                                <p class="text-xs sm:text-[13px] text-charcoal/80 mt-1.5 max-w-xl leading-relaxed">
                                    Tahun Ajaran 2026/2027 Ganjil • Supervisi 42 Rombel, 68 Tenaga Pendidik, &amp; 1.240 Siswa
                                </p>
                            </div>
                        </div>

                        <!-- 4 Kartu KPI Makro Eksekutif -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                            <!-- KPI 1: Keterlaksanaan KBM -->
                            <div
                                @click="$emit('selectTab', 'kbm')"
                                class="bg-white rounded-[24px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-subtext">Keterlaksanaan KBM</span>
                                    <span class="w-8 h-8 rounded-xl bg-sage-light flex items-center justify-center text-sage-dark">
                                        <LmsIcon name="book" className="w-4 h-4" />
                                    </span>
                                </div>
                                <div class="mt-3">
                                    <div class="text-[26px] sm:text-[30px] font-black text-charcoal leading-none">96.2%</div>
                                    <div class="text-[11px] font-bold text-[#2E7D32] mt-1.5 flex items-center gap-1">
                                        <span>38 dari 40 sesi berjalan normal</span>
                                    </div>
                                </div>
                            </div>

                            <!-- KPI 2: Kehadiran Guru -->
                            <div
                                @click="$emit('selectTab', 'kinerja')"
                                class="bg-white rounded-[24px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-subtext">Kehadiran Guru</span>
                                    <span class="w-8 h-8 rounded-xl bg-amber-light flex items-center justify-center text-amber-dark">
                                        <LmsIcon name="user" className="w-4 h-4" />
                                    </span>
                                </div>
                                <div class="mt-3">
                                    <div class="text-[26px] sm:text-[30px] font-black text-charcoal leading-none">65 / 68</div>
                                    <div class="text-[11px] font-bold text-charcoal/70 mt-1.5">
                                        <span>95.6% pengampu tepat waktu</span>
                                    </div>
                                </div>
                            </div>

                            <!-- KPI 3: Kehadiran Siswa -->
                            <div
                                class="bg-white rounded-[24px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow flex flex-col justify-between"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-subtext">Kehadiran Siswa Makro</span>
                                    <span class="w-8 h-8 rounded-xl bg-sky-light flex items-center justify-center text-sky-dark">
                                        <LmsIcon name="chart" className="w-4 h-4" />
                                    </span>
                                </div>
                                <div class="mt-3">
                                    <div class="text-[26px] sm:text-[30px] font-black text-charcoal leading-none">95.8%</div>
                                    <div class="text-[11px] font-bold text-subtext mt-1.5">
                                        <span>1.188 hadir • 10 alpa se-sekolah</span>
                                    </div>
                                </div>
                            </div>

                            <!-- KPI 4: Ruang Praktik Vokasi -->
                            <div
                                @click="$emit('selectTab', 'kbm')"
                                class="bg-white rounded-[24px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-subtext">Lab &amp; Bengkel Vokasi</span>
                                    <span class="w-8 h-8 rounded-xl bg-lavender-light flex items-center justify-center text-lavender-dark">
                                        <LmsIcon name="clock" className="w-4 h-4" />
                                    </span>
                                </div>
                                <div class="mt-3">
                                    <div class="text-[26px] sm:text-[30px] font-black text-charcoal leading-none">8 Ruang</div>
                                    <div class="text-[11px] font-bold text-charcoal/70 mt-1.5">
                                        <span>Fasilitas praktik sedang aktif</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Live KBM Quick Spotlight -->
                        <div class="bg-white rounded-[26px] p-5 border border-[#ECEAE4] soft-shadow">
                            <div class="flex items-center justify-between mb-3.5">
                                <div>
                                    <h3 class="text-sm sm:text-base font-extrabold text-charcoal">Sesi KBM yang Sedang Berjalan Saat Ini</h3>
                                    <p class="text-xs text-subtext mt-0.5">Pantauan langsung guru dan ruangan yang sedang melangsungkan pembelajaran.</p>
                                </div>
                                <button
                                    type="button"
                                    @click="$emit('selectTab', 'kbm')"
                                    class="text-xs font-black text-charcoal hover:underline flex items-center gap-1 cursor-pointer shrink-0"
                                >
                                    Lihat Semua KBM →
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div
                                    v-for="s in allKbmSessions.slice(0, 3)"
                                    :key="s.id"
                                    class="p-3.5 rounded-2xl bg-canvas border border-[#ECEAE4] flex flex-col justify-between gap-2"
                                >
                                    <div>
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[11px] font-black px-2 py-0.5 rounded-md bg-white text-charcoal border border-[#ECEAE4]">{{ s.rombel }}</span>
                                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-[#E8F5E9] text-[#2E7D32]">
                                                {{ s.status }}
                                            </span>
                                        </div>
                                        <div class="text-xs font-extrabold text-charcoal mt-2 line-clamp-1">{{ s.mapel }}</div>
                                        <div class="text-[11px] text-subtext mt-0.5">{{ s.guru }}</div>
                                    </div>
                                    <div class="pt-2 border-t border-[#ECEAE4] text-[10px] text-subtext flex items-center justify-between">
                                        <span>{{ s.ruang }}</span>
                                        <span class="font-bold text-charcoal">{{ s.jam.split('(')[1]?.replace(')', '') || s.jam }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Capaian & Keterlaksanaan per Program Keahlian (SMK) -->
                        <div>
                            <h3 class="text-[15px] font-extrabold text-charcoal mb-2.5">
                                Keterlaksanaan KBM per Program Keahlian (Jurusan)
                            </h3>
                            <div class="space-y-2.5">
                                <div
                                    v-for="j in majors"
                                    :key="j.jur"
                                    class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-charcoal/20 transition-all"
                                >
                                    <div>
                                        <div class="text-xs sm:text-sm font-extrabold text-charcoal">{{ j.jur }}</div>
                                        <div class="text-[11px] text-subtext mt-1 flex items-center gap-3 flex-wrap">
                                            <span>{{ j.rombel }}</span>
                                            <span>•</span>
                                            <span>{{ j.guru }}</span>
                                            <span>•</span>
                                            <span class="text-charcoal font-semibold">{{ j.lab }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0 self-start sm:self-center">
                                        <div class="text-right">
                                            <div class="text-xs font-extrabold text-[#2E7D32]">{{ j.sesiBerjalan }}</div>
                                            <div class="text-[10px] text-subtext">Kehadiran: {{ j.hadir }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 2: KBM (SUPERVISI SESI KELAS & RUANGAN REALTIME)      -->
                    <!-- ======================================================== -->
                    <div v-else-if="isCurrentTab('kbm')" class="space-y-4">
                        <!-- Hero Banner Standar (115px) -->
                        <div class="hero-amber rounded-[28px] py-7 px-6 sm:py-8 sm:px-8 soft-shadow relative overflow-hidden flex flex-col justify-center min-h-[115px]">
                            <div class="relative z-10">
                                <h2 class="text-[20px] sm:text-[23px] font-black text-charcoal leading-tight">
                                    Supervisi KBM Realtime
                                </h2>
                                <p class="text-xs sm:text-[13px] text-charcoal/80 mt-1.5 max-w-xl leading-relaxed">
                                    Pantau kehadiran guru, keterisian ruang teori, laboratorium komputer, dan bengkel praktik vokasi.
                                </p>
                            </div>
                        </div>

                        <!-- Filter & Action Controls -->
                        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                            <!-- Status Filter Buttons -->
                            <div class="flex items-center gap-1.5 p-1 bg-muted/70 rounded-2xl overflow-x-auto no-scrollbar">
                                <button
                                    type="button"
                                    @click="kbmFilter = 'semua'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                                    :class="kbmFilter === 'semua' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Semua Sesi ({{ allKbmSessions.length }})
                                </button>
                                <button
                                    type="button"
                                    @click="kbmFilter = 'berlangsung'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                                    :class="kbmFilter === 'berlangsung' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Berlangsung ({{ allKbmSessions.filter(s => s.status === 'Berlangsung').length }})
                                </button>
                                <button
                                    type="button"
                                    @click="kbmFilter = 'terjadwal'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                                    :class="kbmFilter === 'terjadwal' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Terjadwal ({{ allKbmSessions.filter(s => s.status === 'Terjadwal').length }})
                                </button>
                                <button
                                    type="button"
                                    @click="kbmFilter = 'selesai'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                                    :class="kbmFilter === 'selesai' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Selesai ({{ allKbmSessions.filter(s => s.status === 'Selesai').length }})
                                </button>
                            </div>

                            <!-- Search Input -->
                            <div class="w-full md:w-72">
                                <input
                                    v-model="kbmSearch"
                                    type="text"
                                    placeholder="Cari rombel, guru, atau ruang..."
                                    class="w-full px-3.5 py-2 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-semibold text-charcoal placeholder:text-subtext focus:outline-none focus:border-charcoal/40 transition-all"
                                />
                            </div>
                        </div>

                        <!-- Daftar Sesi KBM -->
                        <div class="space-y-3">
                            <div
                                v-for="s in filteredKbmSessions"
                                :key="s.id"
                                class="bg-white rounded-[24px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow flex flex-col md:flex-row md:items-center justify-between gap-3 hover:border-charcoal/30 transition-all"
                            >
                                <div class="space-y-1.5 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-black px-2.5 py-0.5 rounded-md bg-canvas text-charcoal border border-[#ECEAE4]">{{ s.rombel }}</span>
                                        <h4 class="text-sm font-extrabold text-charcoal">{{ s.mapel }}</h4>
                                        <span
                                            class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                            :class="
                                                s.status === 'Berlangsung'
                                                    ? 'bg-[#E8F5E9] text-[#2E7D32]'
                                                    : s.status === 'Terjadwal'
                                                    ? 'bg-sky-light text-sky-dark'
                                                    : 'bg-muted text-subtext'
                                            "
                                        >
                                            {{ s.status }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-subtext flex items-center gap-3 flex-wrap">
                                        <span>Guru: <strong class="text-charcoal">{{ s.guru }}</strong></span>
                                        <span>•</span>
                                        <span>Ruang: <strong class="text-charcoal">{{ s.ruang }}</strong></span>
                                        <span>•</span>
                                        <span>Waktu: {{ s.jam }}</span>
                                    </div>
                                    <div class="text-[11px] text-charcoal/75 pt-1">
                                        Materi/Aktivitas: <span class="italic font-medium">{{ s.catatan }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2.5 shrink-0 self-start md:self-center">
                                    <button
                                        type="button"
                                        @click="$emit('showToast', `Supervisi kelas ${s.rombel} terverifikasi normal.`)"
                                        class="px-3.5 py-2 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                                    >
                                        Verifikasi Kelas
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 3: KINERJA GURU (SUPERVISI KETUNTASAN & JURNAL AJAR)  -->
                    <!-- ======================================================== -->
                    <div v-else-if="isCurrentTab('kinerja')" class="space-y-4">
                        <!-- Hero Banner Standar (115px) -->
                        <div class="hero-amber rounded-[28px] py-7 px-6 sm:py-8 sm:px-8 soft-shadow relative overflow-hidden flex flex-col justify-center min-h-[115px]">
                            <div class="relative z-10">
                                <h2 class="text-[20px] sm:text-[23px] font-black text-charcoal leading-tight">
                                    Supervisi Kinerja Mengajar Guru
                                </h2>
                                <p class="text-xs sm:text-[13px] text-charcoal/80 mt-1.5 max-w-xl leading-relaxed">
                                    Evaluasi pemenuhan jam mengajar mingguan (JP), kepatuhan jurnal kelas, dan ketuntasan silabus ajar.
                                </p>
                            </div>
                        </div>

                        <!-- 3 Kartu Ringkasan Kinerja -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="bg-white rounded-[22px] p-4 border border-[#ECEAE4] soft-shadow">
                                <div class="text-xs font-bold text-subtext">Rata-rata Progres Silabus</div>
                                <div class="text-[24px] font-black text-charcoal mt-1">64.2%</div>
                                <div class="text-[11px] text-[#2E7D32] font-semibold mt-0.5">Sesuai Kalender Akademik Ganjil</div>
                            </div>
                            <div class="bg-white rounded-[22px] p-4 border border-[#ECEAE4] soft-shadow">
                                <div class="text-xs font-bold text-subtext">Kepatuhan Jurnal Mengajar</div>
                                <div class="text-[24px] font-black text-charcoal mt-1">95.6%</div>
                                <div class="text-[11px] text-subtext font-semibold mt-0.5">65 dari 68 guru tertib mengisi</div>
                            </div>
                            <div class="bg-white rounded-[22px] p-4 border border-[#ECEAE4] soft-shadow">
                                <div class="text-xs font-bold text-subtext">Total Beban Mengajar Sekolah</div>
                                <div class="text-[24px] font-black text-charcoal mt-1">1.632 JP</div>
                                <div class="text-[11px] text-subtext font-semibold mt-0.5">Terplotting penuh di jadwal</div>
                            </div>
                        </div>

                        <!-- Filter Controls -->
                        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                            <div class="flex items-center gap-1.5 p-1 bg-muted/70 rounded-2xl overflow-x-auto no-scrollbar">
                                <button
                                    type="button"
                                    @click="kinerjaJurusanFilter = 'semua'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                                    :class="kinerjaJurusanFilter === 'semua' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Semua Guru ({{ teacherPerformanceList.length }})
                                </button>
                                <button
                                    type="button"
                                    @click="kinerjaJurusanFilter = 'kejuruan'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                                    :class="kinerjaJurusanFilter === 'kejuruan' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Mapel Kejuruan
                                </button>
                                <button
                                    type="button"
                                    @click="kinerjaJurusanFilter = 'umum'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                                    :class="kinerjaJurusanFilter === 'umum' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Mapel Umum
                                </button>
                            </div>

                            <div class="w-full md:w-72">
                                <input
                                    v-model="kinerjaSearch"
                                    type="text"
                                    placeholder="Cari nama guru atau mapel..."
                                    class="w-full px-3.5 py-2 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-semibold text-charcoal placeholder:text-subtext focus:outline-none focus:border-charcoal/40 transition-all"
                                />
                            </div>
                        </div>

                        <!-- Tabel/List Kinerja Guru -->
                        <div class="space-y-3">
                            <div
                                v-for="t in filteredTeacherPerformance"
                                :key="t.id"
                                class="bg-white rounded-[24px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow flex flex-col md:flex-row md:items-center justify-between gap-4 hover:border-charcoal/20 transition-all"
                            >
                                <div class="space-y-1.5 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-sm font-extrabold text-charcoal">{{ t.name }}</h4>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-canvas border border-[#ECEAE4] text-subtext">
                                            {{ t.nip }}
                                        </span>
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-amber-light text-amber-dark">
                                            {{ t.jp }} / Minggu
                                        </span>
                                    </div>
                                    <div class="text-xs text-subtext">
                                        Pengampu: <strong class="text-charcoal">{{ t.mapel }}</strong> • Program: <strong class="text-charcoal">{{ t.jur }}</strong>
                                    </div>

                                    <!-- Progress Pertemuan Bar -->
                                    <div class="pt-1 max-w-md">
                                        <div class="flex items-center justify-between text-[11px] font-semibold text-charcoal/80 mb-1">
                                            <span>Ketuntasan Pertemuan</span>
                                            <span>{{ t.completedMeetings }} / {{ t.totalMeetings }} Pertemuan ({{ Math.round((t.completedMeetings / t.totalMeetings) * 100) }}%)</span>
                                        </div>
                                        <div class="w-full h-2 rounded-full bg-canvas border border-[#ECEAE4] overflow-hidden">
                                            <div
                                                class="h-full rounded-full bg-charcoal transition-all duration-500"
                                                :style="{ width: `${(t.completedMeetings / t.totalMeetings) * 100}%` }"
                                            ></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2.5 shrink-0 self-start md:self-center">
                                    <div class="text-right hidden sm:block">
                                        <div class="text-[10px] text-subtext">Jurnal: <strong class="text-[#2E7D32]">{{ t.journalStatus }}</strong></div>
                                        <div class="text-[10px] text-subtext">Modul: <strong class="text-charcoal">{{ t.moduleStatus }}</strong></div>
                                    </div>
                                    <button
                                        type="button"
                                        @click="$emit('showToast', `Catatan apresiasi dan supervisi terkirim ke ${t.name}.`)"
                                        class="px-3.5 py-2 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                                    >
                                        Beri Catatan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 4: LAPORAN (PUSAT DOKUMEN: CSV, EXCEL, & PDF)         -->
                    <!-- ======================================================== -->
                    <div v-else-if="isCurrentTab('laporan')" class="space-y-4">
                        <!-- Hero Banner Standar (115px) -->
                        <div class="hero-amber rounded-[28px] py-7 px-6 sm:py-8 sm:px-8 soft-shadow relative overflow-hidden flex flex-col justify-center min-h-[115px]">
                            <div class="relative z-10">
                                <h2 class="text-[20px] sm:text-[23px] font-black text-charcoal leading-tight">
                                    Pusat Dokumen &amp; Laporan Eksekutif
                                </h2>
                                <p class="text-xs sm:text-[13px] text-charcoal/80 mt-1.5 max-w-xl leading-relaxed">
                                    Unduh berkas resmi sekolah dalam format CSV data mentah, Excel template resmi dinas, dan PDF siap cetak.
                                </p>
                            </div>
                        </div>

                        <!-- 4 Kartu Kategori Dokumen Laporan Resmi -->
                        <div class="grid grid-cols-1 gap-4">
                            <div
                                v-for="rep in reportCategories"
                                :key="rep.id"
                                class="bg-white rounded-[26px] p-5 sm:p-6 border border-[#ECEAE4] soft-shadow flex flex-col lg:flex-row lg:items-center justify-between gap-4 hover:border-charcoal/20 transition-all"
                            >
                                <div class="space-y-1.5 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-canvas text-charcoal border border-[#ECEAE4]">
                                            {{ rep.code }}
                                        </span>
                                        <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-amber-light text-amber-dark">
                                            {{ rep.badge }}
                                        </span>
                                    </div>
                                    <h4 class="text-base sm:text-lg font-extrabold text-charcoal leading-snug">
                                        {{ rep.title }}
                                    </h4>
                                    <p class="text-xs text-subtext max-w-2xl leading-relaxed">
                                        {{ rep.description }}
                                    </p>
                                </div>

                                <!-- 3 Tombol Format: CSV, Excel (Template), PDF (Rapi) -->
                                <div class="flex items-center gap-2 flex-wrap shrink-0 pt-2 lg:pt-0 border-t lg:border-t-0 border-[#ECEAE4]">
                                    <!-- Tombol 1: PDF -->
                                    <button
                                        type="button"
                                        @click="handleDownloadReport('pdf', rep)"
                                        class="px-3.5 py-2 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs flex items-center gap-1.5"
                                        title="Format PDF Rapi Siap Cetak & Tanda Tangan"
                                    >
                                        <LmsIcon name="download" className="w-3.5 h-3.5 text-white" />
                                        <span>PDF (Rapi)</span>
                                    </button>

                                    <!-- Tombol 2: Excel (Template Resmi) -->
                                    <button
                                        type="button"
                                        @click="handleDownloadReport('excel', rep)"
                                        class="px-3.5 py-2 rounded-xl bg-[#E8F5E9] text-[#2E7D32] border border-[#C8E6C9] text-xs font-bold hover:bg-[#C8E6C9] active:scale-95 transition-all cursor-pointer shadow-2xs flex items-center gap-1.5"
                                        title="Format Excel Ber-template Resmi Dinas"
                                    >
                                        <LmsIcon name="download" className="w-3.5 h-3.5 text-[#2E7D32]" />
                                        <span>Excel (Template)</span>
                                    </button>

                                    <!-- Tombol 3: CSV (Data Mentah) -->
                                    <button
                                        type="button"
                                        @click="handleDownloadReport('csv', rep)"
                                        class="px-3.5 py-2 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal text-xs font-bold hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs flex items-center gap-1.5"
                                        title="Format CSV Data Mentah"
                                    >
                                        <LmsIcon name="download" className="w-3.5 h-3.5 text-charcoal" />
                                        <span>CSV (Mentah)</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>
    </div>
</template>
