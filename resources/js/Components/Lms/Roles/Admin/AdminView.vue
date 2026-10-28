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

// Dynamic page title based on active tab
const pageTitle = computed(() => {
    const map = {
        home: 'Panel Operator Sekolah',
        pengguna: 'Kelola Data Siswa & Akun',
        plotting: 'Plotting Rombel & Wali Kelas',
        moderasi: 'Koreksi & Moderasi Presensi',
    };
    return map[props.activeTab] || 'Panel Operator Sekolah';
});

// Mock data for Admin sub-tabs
const siswaList = [
    { nisn: '008492104', name: 'Nadia Putri Maharani', rombel: 'XII RPL 1', status: 'Aktif', sandiStatus: 'Default' },
    { nisn: '008492105', name: 'Fajar Nugraha Pratama', rombel: 'XII RPL 1', status: 'Aktif', sandiStatus: 'Kustom' },
    { nisn: '008492106', name: 'Dimas Anggara Putra', rombel: 'XII RPL 2', status: 'Aktif', sandiStatus: 'Reset Dibutuhkan' },
    { nisn: '009112001', name: 'Aldiansyah Putra', rombel: 'X DKV 1', status: 'Pantauan', sandiStatus: 'Kustom' },
    { nisn: '009112002', name: 'Salsabila Azzahra', rombel: 'X DKV 1', status: 'Aktif', sandiStatus: 'Default' },
];

const plottingRombel = [
    { rombel: 'XII RPL 1', wali: 'Dra. Endang Setyawati', siswa: 34, kapasitas: 36, status: 'Lengkap', jurusan: 'RPL' },
    { rombel: 'XII RPL 2', wali: 'Ahmad Fauzi, M.Kom', siswa: 33, kapasitas: 36, status: 'Lengkap', jurusan: 'RPL' },
    { rombel: 'XI RPL 1', wali: 'Budi Santoso, S.Kom', siswa: 35, kapasitas: 36, status: 'Lengkap', jurusan: 'RPL' },
    { rombel: 'X DKV 1', wali: 'Siti Rahma, M.Pd', siswa: 36, kapasitas: 36, status: 'Lengkap', jurusan: 'DKV' },
    { rombel: 'X TKJ 1', wali: 'Irfan Maulana, S.T', siswa: 32, kapasitas: 36, status: 'Lengkap', jurusan: 'TKJ' },
];

const moderasiList = [
    { id: 1, siswa: 'Aldiansyah Putra', rombel: 'X DKV 1', tgl: '28 Okt 2026', dari: 'Alpa', menjadi: 'Sakit', alasan: 'Surat dokter RSUD terlampir', status: 'Menunggu' },
    { id: 2, siswa: 'Bayu Prasetyo', rombel: 'XI TKJ 2', tgl: '27 Okt 2026', dari: 'Alpa', menjadi: 'Izin', alasan: 'Dispensasi LKS Tingkat Provinsi', status: 'Disetujui' },
    { id: 3, siswa: 'Citra Kirana', rombel: 'XII RPL 1', tgl: '26 Okt 2026', dari: 'Terlambat', menjadi: 'Hadir', alasan: 'Kendala angkutan umum kota', status: 'Disetujui' },
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
                    <!-- TAB 1: HOME (OPERASIONAL AKADEMIK)           -->
                    <!-- ============================================ -->
                    <div v-if="activeTab === 'home'" class="space-y-5">
                        <!-- Hero Card: Operational Metrics -->
                        <div class="hero-lavender rounded-[28px] p-5 soft-shadow">
                            <span class="bg-white px-3 py-1 rounded-full text-[11px] font-extrabold text-charcoal shadow-2xs">
                                Operasional Akademik
                            </span>
                            <h3 class="text-[21px] font-extrabold text-charcoal mt-3">
                                1.240 Siswa &amp; 68 Guru
                            </h3>
                            <p class="text-xs text-charcoal/75 mt-1">
                                Plotting Rombel, Reset Sandi, &amp; Koreksi Presensi
                            </p>
                        </div>

                        <!-- Operator Quick Actions -->
                        <div>
                            <h3 class="text-[15px] font-extrabold text-charcoal mb-3">
                                Tindakan Cepat Operator
                            </h3>
                            <div class="space-y-2.5">
                                <div
                                    @click="$emit('showToast', 'Sandi sementara [SMK-8821] berhasil dibuat untuk akun siswa terkait.')"
                                    class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between hover:border-charcoal/30 transition-all"
                                >
                                    <div>
                                        <div class="text-sm font-extrabold text-charcoal">
                                            Reset Kata Sandi Pengguna
                                        </div>
                                        <div class="text-[11px] text-subtext">
                                            Buat sandi sementara untuk Siswa / Guru
                                        </div>
                                    </div>
                                    <span class="px-3 py-1.5 rounded-full bg-charcoal text-white text-xs font-bold shrink-0 ml-2 shadow-xs">
                                        Reset
                                    </span>
                                </div>

                                <div
                                    @click="$emit('showToast', 'Status Alpa siswa berhasil dikoreksi menjadi Sakit (Surat Dokter Terverifikasi).')"
                                    class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between hover:border-charcoal/30 transition-all"
                                >
                                    <div>
                                        <div class="text-sm font-extrabold text-charcoal">
                                            Koreksi Presensi Siswa
                                        </div>
                                        <div class="text-[11px] text-subtext">
                                            Sesuaikan status izin/sakit susulan
                                        </div>
                                    </div>
                                    <span class="px-3 py-1.5 rounded-full bg-lavender-light text-lavender-dark text-xs font-bold shrink-0 ml-2">
                                        Koreksi
                                    </span>
                                </div>

                                <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-extrabold text-charcoal">
                                            Plotting Guru &amp; Wali Kelas
                                        </div>
                                        <div class="text-[11px] text-subtext">
                                            42 Rombel telah memiliki Wali Kelas aktif
                                        </div>
                                    </div>
                                    <span class="px-3 py-1.5 rounded-full bg-sage-light text-sage-dark text-xs font-bold shrink-0 ml-2">
                                        Lengkap
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- TAB 2: PENGGUNA (DATA SISWA & AKUN)          -->
                    <!-- ============================================ -->
                    <div v-else-if="activeTab === 'pengguna'" class="space-y-4">
                        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row gap-3 items-center justify-between">
                            <div class="text-xs text-subtext">Total <strong>1.240</strong> Akun Siswa Terdaftar di Dapodik</div>
                            <button
                                type="button"
                                @click="$emit('showToast', 'Sinkronisasi data Dapodik selesai. 0 perubahan terdeteksi.')"
                                class="px-3.5 py-1.5 rounded-full bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer"
                            >
                                Sinkron Dapodik
                            </button>
                        </div>

                        <div class="space-y-2.5">
                            <div
                                v-for="s in siswaList"
                                :key="s.nisn"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between hover:border-charcoal/30 transition-all"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-lavender-light text-lavender-dark font-extrabold flex items-center justify-center text-xs shrink-0">
                                        {{ s.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-extrabold text-charcoal">{{ s.name }}</div>
                                        <div class="text-[11px] text-subtext">NISN: {{ s.nisn }} • {{ s.rombel }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-canvas border border-[#ECEAE4]">
                                        {{ s.status }}
                                    </span>
                                    <button
                                        type="button"
                                        @click="$emit('showToast', `Sandi untuk ${s.name} direset ke default [SMK-${s.nisn.slice(-4)}].`)"
                                        class="px-3 py-1 rounded-full bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer shadow-xs"
                                    >
                                        Reset Sandi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- TAB 3: PLOTTING (ROMBEL & WALI KELAS)        -->
                    <!-- ============================================ -->
                    <div v-else-if="activeTab === 'plotting'" class="space-y-4">
                        <div class="hero-lavender rounded-[26px] p-4 soft-shadow flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-extrabold text-charcoal">Status Plotting Wali Kelas</h3>
                                <p class="text-xs text-charcoal/75 mt-0.5">Semua rombel telah memiliki wali kelas terplot secara valid.</p>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-white text-charcoal text-xs font-black shadow-2xs">
                                100% Terplot
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            <div
                                v-for="r in plottingRombel"
                                :key="r.rombel"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between"
                            >
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-black px-2.5 py-0.5 rounded-full bg-canvas border border-[#ECEAE4] text-charcoal">
                                        {{ r.rombel }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-sage-light text-sage-dark">
                                        {{ r.status }}
                                    </span>
                                </div>
                                <div class="text-sm font-extrabold text-charcoal mt-1">
                                    {{ r.wali }}
                                </div>
                                <div class="text-[11px] text-subtext mt-0.5">
                                    Kapasitas: {{ r.siswa }}/{{ r.kapasitas }} Siswa
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- TAB 4: MODERASI (KOREKSI PRESENSI)           -->
                    <!-- ============================================ -->
                    <div v-else-if="activeTab === 'moderasi'" class="space-y-4">
                        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-extrabold text-charcoal">Antrean Verifikasi Surat Susulan</h3>
                                <p class="text-xs text-subtext mt-0.5">Koreksi status kehadiran siswa berdasarkan berkas surat resmi.</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-[#FFF3E0] text-[#E65100] text-[11px] font-extrabold">
                                {{ moderasiList.filter(m => m.status === 'Menunggu').length }} Menunggu
                            </span>
                        </div>

                        <div class="space-y-2.5">
                            <div
                                v-for="m in moderasiList"
                                :key="m.id"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                            >
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-extrabold text-charcoal">{{ m.siswa }}</span>
                                        <span class="text-[10px] font-bold text-subtext">• {{ m.rombel }} • {{ m.tgl }}</span>
                                    </div>
                                    <div class="text-xs font-semibold text-charcoal mt-1">
                                        Ubah status dari <span class="line-through text-subtext">{{ m.dari }}</span> → <strong class="text-[#2E7D32]">{{ m.menjadi }}</strong>
                                    </div>
                                    <div class="text-[11px] text-subtext mt-0.5 italic">"{{ m.alasan }}"</div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button
                                        v-if="m.status === 'Menunggu'"
                                        type="button"
                                        @click="$emit('showToast', `Koreksi presensi ${m.siswa} berhasil disetujui.`)"
                                        class="px-3.5 py-1.5 rounded-full bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer shadow-xs"
                                    >
                                        Setujui Koreksi
                                    </button>
                                    <span v-else class="text-[11px] font-bold text-sage-dark bg-sage-light px-3 py-1 rounded-full">
                                        ✓ Sudah Disetujui
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>
    </div>
</template>
