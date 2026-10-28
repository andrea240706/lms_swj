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
        default: 'home',
    },
    isSidebarCollapsed: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['openMenu', 'openProfile', 'showToast', 'toggleSidebar']);

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

const pageTitle = computed(() => {
    const map = {
        home: 'Konfigurasi & Master Data',
        master: 'Data Master Akademik',
        akses: 'Hak Akses & Akun Pejabat',
        log: 'Live Audit Trail Log Sistem',
    };
    return map[props.activeTab] || 'Konfigurasi & Master Data';
});

const masterItems = [
    { title: 'Konsentrasi Keahlian (Jurusan)', desc: 'RPL, TKJ, DKV, AKL (4 Program Aktif)', count: '4 Jurusan' },
    { title: 'Tingkat & Rombel Kelas', desc: 'Kelas X (14 Rombel), XI (14 Rombel), XII (14 Rombel)', count: '42 Rombel' },
    { title: 'Mata Pelajaran Kurikulum Merdeka', desc: 'Muatan Nasional, Kejuruan Produktif, & P5', count: '58 Mapel' },
    { title: 'Ruang Teori & Laboratorium Vokasi', desc: '6 Lab Komputer, 2 Studio Desain, 24 Ruang Kelas', count: '32 Ruangan' },
];

const officialAccounts = [
    { name: 'Drs. Hendra Wijaya', role: 'Kepala Sekolah', status: 'Aktif', nip: '19710315 199802 1 001' },
    { name: 'Rizky Pratama', role: 'Admin Operator TU', status: 'Aktif', nip: '19920810 202012 1 004' },
    { name: 'Siti Rahma, M.Pd', role: 'Wali Kelas X DKV 1', status: 'Aktif', nip: '19890520 201801 2 003' },
    { name: 'Budi Santoso, S.Kom', role: 'Guru Produktif RPL', status: 'Aktif', nip: '19880412 201504 1 002' },
];

const auditLogs = [
    { time: '09:14:22', user: 'Admin Rizky', action: 'Mengoreksi presensi susulan siswa Aldiansyah Putra (X DKV 1)', ip: '192.168.1.104', type: 'info' },
    { time: '08:02:11', user: 'Guru Budi Santoso', action: 'Membuka gerbang presensi sesi KBM XII RPL 1 (Lab RPL 2)', ip: '192.168.1.118', type: 'success' },
    { time: '07:45:09', user: 'Kepsek Hendra', action: 'Mengunduh laporan rekap kehadiran sekolah format PDF', ip: '192.168.1.101', type: 'info' },
    { time: '07:30:00', user: 'System Cron', action: 'Sinkronisasi otomatis jadwal mengajar harian & backup DB', ip: 'localhost', type: 'system' },
];
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <LmsTopBar
            :role="role"
            :isSidebarCollapsed="isSidebarCollapsed"
            :title="pageTitle"
            @openMenu="$emit('openMenu')"
            @openProfile="$emit('openProfile')"
            @toggleSidebar="$emit('toggleSidebar')"
        />

        <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
            <div class="w-full max-w-[1800px] mx-auto">

                <!-- SKELETON LOADER -->
                <LmsSkeletonLoader v-if="isLoading" type="card-list" :rows="4" />

                <!-- CONTENT -->
                <template v-else>
                    <!-- ============================================ -->
                    <!-- TAB 1: HOME (KONFIGURASI UTAMA)              -->
                    <!-- ============================================ -->
                    <div v-if="activeTab === 'home'" class="space-y-5">
                        <!-- Hero Card: Core System Config -->
                        <div class="hero-sage rounded-[28px] p-5 soft-shadow">
                            <span class="bg-white px-3 py-1 rounded-full text-[11px] font-extrabold text-charcoal shadow-2xs">
                                TA 2026/2027 • Semester Ganjil
                            </span>
                            <h3 class="text-[21px] font-extrabold text-charcoal mt-3">
                                Sistem Utama &amp; Master
                            </h3>
                            <p class="text-xs text-charcoal/75 mt-1">
                                Batas Unggah: 15 MB • Format: PDF, ZIP, JPG
                            </p>
                        </div>

                        <!-- Master Data & Security Management -->
                        <div>
                            <h3 class="text-[15px] font-extrabold text-charcoal mb-2.5">
                                Data Master &amp; Keamanan
                            </h3>
                            <div class="space-y-2.5">
                                <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] flex items-center justify-between shadow-2xs">
                                    <div>
                                        <div class="text-sm font-extrabold text-charcoal">
                                            Master Jurusan &amp; Tingkat
                                        </div>
                                        <div class="text-[11px] text-subtext">
                                            RPL, TKJ, DKV, AKL • Kelas X, XI, XII
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-canvas border border-[#E6E4DF] shrink-0 ml-2">
                                        4 Jurusan
                                    </span>
                                </div>

                                <div
                                    @click="$emit('showToast', 'Panel Hak Akses & Akun Pejabat dibuka.')"
                                    class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] flex items-center justify-between hover:border-charcoal/30 transition-all shadow-2xs"
                                >
                                    <div>
                                        <div class="text-sm font-extrabold text-charcoal">
                                            Kelola Akun Pejabat Sekolah
                                        </div>
                                        <div class="text-[11px] text-subtext">
                                            1 Kepsek, 3 Admin TU, 68 Guru Aktif
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-charcoal text-white shrink-0 ml-2 shadow-xs">
                                        Kelola
                                    </span>
                                </div>

                                <!-- Live Audit Trail Log -->
                                <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] shadow-2xs">
                                    <div class="text-xs font-extrabold text-charcoal mb-2">
                                        Audit Log Aktivitas Terbaru
                                    </div>
                                    <div class="text-[11px] text-subtext space-y-1.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-charcoal"></span>
                                            <span><strong>09:14</strong> — Admin Rizky mengoreksi presensi X DKV 1</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-charcoal"></span>
                                            <span><strong>08:02</strong> — Guru Budi membuka sesi presensi XII RPL 1</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-charcoal"></span>
                                            <span><strong>07:45</strong> — Kepsek mengunduh Leger Semester</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- TAB 2: MASTER (DATA MASTER AKADEMIK)         -->
                    <!-- ============================================ -->
                    <div v-else-if="activeTab === 'master'" class="space-y-4">
                        <div class="hero-sage rounded-[26px] p-4 soft-shadow flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-extrabold text-charcoal">Data Entitas Master LMS</h3>
                                <p class="text-xs text-charcoal/75 mt-0.5">Struktur organisasi kurikulum, rombongan belajar, dan sarana.</p>
                            </div>
                            <button
                                type="button"
                                @click="$emit('showToast', 'Modal Tambah Entitas Master dibuka.')"
                                class="px-3.5 py-1.5 rounded-full bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer shadow-xs"
                            >
                                + Entitas
                            </button>
                        </div>

                        <div class="space-y-2.5">
                            <div
                                v-for="item in masterItems"
                                :key="item.title"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between"
                            >
                                <div>
                                    <div class="text-sm font-extrabold text-charcoal">{{ item.title }}</div>
                                    <div class="text-[11px] text-subtext mt-0.5">{{ item.desc }}</div>
                                </div>
                                <span class="px-3 py-1 rounded-full bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal shrink-0 ml-2">
                                    {{ item.count }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- TAB 3: AKSES (HAK AKSES PEJABAT)             -->
                    <!-- ============================================ -->
                    <div v-else-if="activeTab === 'akses'" class="space-y-4">
                        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-extrabold text-charcoal">Hak Akses &amp; Akun Pejabat</h3>
                                <p class="text-xs text-subtext mt-0.5">Atur wewenang level operator, kepala sekolah, dan staf pengajar.</p>
                            </div>
                            <button
                                type="button"
                                @click="$emit('showToast', 'Modal Buat Akun Pejabat Baru dibuka.')"
                                class="px-3.5 py-1.5 rounded-full bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer shadow-xs"
                            >
                                + Akun
                            </button>
                        </div>

                        <div class="space-y-2.5">
                            <div
                                v-for="acc in officialAccounts"
                                :key="acc.nip"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-sage-light text-sage-dark font-extrabold flex items-center justify-center text-xs shrink-0">
                                        {{ acc.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-extrabold text-charcoal">{{ acc.name }}</div>
                                        <div class="text-[11px] text-subtext">NIP: {{ acc.nip }} • {{ acc.role }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-sage-light text-sage-dark">
                                        {{ acc.status }}
                                    </span>
                                    <button
                                        type="button"
                                        @click="$emit('showToast', `Pengaturan hak akses untuk ${acc.name} dibuka.`)"
                                        class="px-3 py-1 rounded-full bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer shadow-xs"
                                    >
                                        Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- TAB 4: LOG (LIVE AUDIT TRAIL)                -->
                    <!-- ============================================ -->
                    <div v-else-if="activeTab === 'log'" class="space-y-4">
                        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-extrabold text-charcoal">Log Aktivitas Sistem (Audit Trail)</h3>
                                <p class="text-xs text-subtext mt-0.5">Catatan realtime seluruh mutasi data, presensi, dan penilaian.</p>
                            </div>
                            <button
                                type="button"
                                @click="$emit('showToast', 'Log aktivitas berhasil diekspor (.csv).')"
                                class="px-3.5 py-1.5 rounded-full bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer shadow-xs"
                            >
                                Ekspor CSV
                            </button>
                        </div>

                        <div class="space-y-2.5">
                            <div
                                v-for="(log, idx) in auditLogs"
                                :key="idx"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-2"
                            >
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-black text-charcoal bg-canvas border border-[#ECEAE4] px-2 py-0.5 rounded-md">
                                            {{ log.time }}
                                        </span>
                                        <span class="text-xs font-extrabold text-charcoal">{{ log.user }}</span>
                                        <span class="text-[10px] text-subtext">• IP: {{ log.ip }}</span>
                                    </div>
                                    <div class="text-xs text-charcoal/85">{{ log.action }}</div>
                                </div>
                                <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-canvas text-subtext border border-[#ECEAE4] self-start sm:self-center">
                                    {{ log.type }}
                                </span>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>
    </div>
</template>
