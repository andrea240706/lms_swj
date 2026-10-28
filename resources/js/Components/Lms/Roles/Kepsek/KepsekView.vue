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
        home: 'Pantauan Eksekutif Sekolah',
        jurusan: 'Capaian Program Keahlian',
        kbm: 'Monitoring KBM Real-time',
        ekspor: 'Unduh Dokumen & Laporan Resmi',
    };
    return map[props.activeTab] || 'Pantauan Eksekutif Sekolah';
});

const majors = [
    { jur: 'Rekayasa Perangkat Lunak (RPL)', hadir: '97.1%', avg: '87.5', guru: '18 Guru', rombel: '12 Rombel' },
    { jur: 'Teknik Komputer & Jaringan (TKJ)', hadir: '95.4%', avg: '85.2', guru: '16 Guru', rombel: '12 Rombel' },
    { jur: 'Desain Komunikasi Visual (DKV)', hadir: '94.8%', avg: '86.9', guru: '14 Guru', rombel: '10 Rombel' },
    { jur: 'Akuntansi & Keuangan Lembaga', hadir: '96.9%', avg: '88.1', guru: '20 Guru', rombel: '8 Rombel' },
];

const kbmSessions = [
    { mapel: 'Pemrograman Web & Perangkat Bergerak', rombel: 'XII RPL 1', guru: 'Budi Santoso, S.Kom', ruang: 'Lab RPL 2', status: 'Berlangsung', jam: '08:00 - 10:30' },
    { mapel: 'Administrasi Infrastruktur Jaringan', rombel: 'XII TKJ 2', guru: 'Irfan Maulana, S.T', ruang: 'Lab Komputer 1', status: 'Berlangsung', jam: '08:00 - 10:30' },
    { mapel: 'Fotografi & Tata Cahaya', rombel: 'X DKV 1', guru: 'Siti Rahma, M.Pd', ruang: 'Studio DKV', status: 'Berlangsung', jam: '08:30 - 11:00' },
    { mapel: 'Praktikum Akuntansi Perusahaan Jasa', rombel: 'XI AKL 1', guru: 'Dra. Sri Wahyuni', ruang: 'Ruang Teori 4', status: 'Berlangsung', jam: '09:00 - 11:30' },
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
                    <!-- TAB 1: HOME (PANTAUAN EKSEKUTIF)             -->
                    <!-- ============================================ -->
                    <div v-if="activeTab === 'home'" class="space-y-5">
                        <!-- Hero Card: Executive Macro Indicators -->
                        <div class="hero-amber rounded-[28px] p-5 soft-shadow">
                            <span class="bg-white px-3 py-1 rounded-full text-[11px] font-extrabold text-charcoal shadow-2xs">
                                Indikator KBM Hari Ini
                            </span>
                            <h3 class="text-[22px] font-extrabold text-charcoal mt-3">
                                96.2% Sesi Terlaksana
                            </h3>
                            <p class="text-xs text-charcoal/75 mt-1">
                                42 Rombel Aktif • Kehadiran Siswa Makro: 95.8%
                            </p>
                        </div>

                        <!-- Program / Department Macro Statistics -->
                        <div>
                            <h3 class="text-[15px] font-extrabold text-charcoal mb-2.5">
                                Capaian per Program Keahlian (Jurusan)
                            </h3>
                            <div class="space-y-2.5">
                                <div
                                    v-for="j in majors"
                                    :key="j.jur"
                                    class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between"
                                >
                                    <div>
                                        <div class="text-xs font-extrabold text-charcoal">{{ j.jur }}</div>
                                        <div class="text-[11px] text-subtext mt-0.5">
                                            Kehadiran: {{ j.hadir }}
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 rounded-full bg-sage-light text-sage-dark text-xs font-extrabold shrink-0 ml-2">
                                        Nilai: {{ j.avg }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Official Report Downloads -->
                        <div>
                            <h3 class="text-[15px] font-extrabold text-charcoal mb-2.5">
                                Unduh Laporan Resmi (PDF / Excel)
                            </h3>
                            <div class="grid grid-cols-2 gap-2.5">
                                <button
                                    type="button"
                                    @click="$emit('showToast', 'Laporan Kehadiran Sekolah (PDF) berhasil diunduh.')"
                                    class="bg-charcoal text-white rounded-[22px] p-4 text-left flex flex-col justify-between cursor-pointer hover:opacity-90 active:scale-95 transition-all shadow-xs"
                                >
                                    <LmsIcon name="download" className="w-5 h-5 text-white" />
                                    <div class="mt-3">
                                        <div class="text-xs font-extrabold">Rekap Presensi</div>
                                        <div class="text-[10px] opacity-75">Format PDF Resmi</div>
                                    </div>
                                </button>
                                <button
                                    type="button"
                                    @click="$emit('showToast', 'Leger Nilai Akademik Sekolah (.xlsx) berhasil diunduh.')"
                                    class="bg-white border border-[#ECEAE4] text-charcoal rounded-[22px] p-4 text-left flex flex-col justify-between cursor-pointer hover:border-charcoal/40 active:scale-95 transition-all shadow-2xs"
                                >
                                    <LmsIcon name="download" className="w-5 h-5 text-charcoal" />
                                    <div class="mt-3">
                                        <div class="text-xs font-extrabold">Leger Nilai</div>
                                        <div class="text-[10px] text-subtext">Format Excel (.xlsx)</div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- TAB 2: JURUSAN (DETAIL PROGRAM KEAHLIAN)    -->
                    <!-- ============================================ -->
                    <div v-else-if="activeTab === 'jurusan'" class="space-y-4">
                        <div class="hero-amber rounded-[26px] p-4 soft-shadow">
                            <span class="bg-white px-2.5 py-0.5 rounded-full text-[10px] font-extrabold text-charcoal">
                                Analitik Vokasi
                            </span>
                            <h3 class="text-lg font-extrabold text-charcoal mt-2">Komparasi Antar Jurusan</h3>
                            <p class="text-xs text-charcoal/75 mt-0.5">Pemetaan rata-rata nilai, kehadiran, dan rasio guru per konsentrasi keahlian.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            <div
                                v-for="j in majors"
                                :key="j.jur"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between"
                            >
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-extrabold text-charcoal">{{ j.jur }}</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-sage-light text-sage-dark">
                                            Rata-rata {{ j.avg }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 mt-3">
                                        <div class="bg-canvas rounded-xl p-2.5 text-center">
                                            <div class="text-xs font-extrabold text-charcoal">{{ j.hadir }}</div>
                                            <div class="text-[10px] text-subtext">Kehadiran</div>
                                        </div>
                                        <div class="bg-canvas rounded-xl p-2.5 text-center">
                                            <div class="text-xs font-extrabold text-charcoal">{{ j.rombel }}</div>
                                            <div class="text-[10px] text-subtext">{{ j.guru }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- TAB 3: KBM (MONITORING SESI REALTIME)        -->
                    <!-- ============================================ -->
                    <div v-else-if="activeTab === 'kbm'" class="space-y-4">
                        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-extrabold text-charcoal">Sesi KBM yang Sedang Berjalan</h3>
                                <p class="text-xs text-subtext mt-0.5">Pantau keterisian ruang kelas, laboratorium, dan kehadiran guru pengampu.</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full bg-[#E8F5E9] text-[#2E7D32] text-xs font-extrabold flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-[#2E7D32] animate-pulse"></span>
                                4 Sesi Aktif
                            </span>
                        </div>

                        <div class="space-y-2.5">
                            <div
                                v-for="s in kbmSessions"
                                :key="s.rombel"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                            >
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-black px-2 py-0.5 rounded-md bg-canvas text-charcoal border border-[#ECEAE4]">{{ s.rombel }}</span>
                                        <span class="text-sm font-extrabold text-charcoal">{{ s.mapel }}</span>
                                    </div>
                                    <div class="text-xs text-subtext mt-1">
                                        Guru: <strong>{{ s.guru }}</strong> • Ruang: {{ s.ruang }} • Jam: {{ s.jam }}
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full bg-[#E8F5E9] text-[#2E7D32] text-xs font-bold shrink-0 self-start sm:self-center">
                                    {{ s.status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- TAB 4: EKSPOR (LAPORAN & DOKUMEN RESMI)     -->
                    <!-- ============================================ -->
                    <div v-else-if="activeTab === 'ekspor'" class="space-y-4">
                        <div class="hero-amber rounded-[26px] p-4 soft-shadow">
                            <h3 class="text-base font-extrabold text-charcoal">Arsip & Dokumen Resmi Sekolah</h3>
                            <p class="text-xs text-charcoal/75 mt-0.5">Unduh rekapitulasi nilai dan kehadiran semester untuk keperluan akreditasi & dinas.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            <div
                                @click="$emit('showToast', 'Mengunduh Buku Leger Nilai Lengkap TA 2026/2027 (.xlsx)...')"
                                class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow hover:border-charcoal/30 active:scale-[0.99] transition-all flex items-center justify-between"
                            >
                                <div>
                                    <div class="text-sm font-extrabold text-charcoal">Leger Nilai Lengkap</div>
                                    <div class="text-[11px] text-subtext mt-0.5">Semua tingkat & jurusan • Format Excel</div>
                                </div>
                                <span class="w-9 h-9 rounded-2xl bg-canvas border border-[#ECEAE4] flex items-center justify-center text-charcoal shrink-0">
                                    <LmsIcon name="download" className="w-4 h-4" />
                                </span>
                            </div>

                            <div
                                @click="$emit('showToast', 'Mengunduh Rekap Presensi Siswa & Guru (.pdf)...')"
                                class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow hover:border-charcoal/30 active:scale-[0.99] transition-all flex items-center justify-between"
                            >
                                <div>
                                    <div class="text-sm font-extrabold text-charcoal">Rekap Presensi KBM</div>
                                    <div class="text-[11px] text-subtext mt-0.5">Tingkat kehadiran 95.8% • Format PDF</div>
                                </div>
                                <span class="w-9 h-9 rounded-2xl bg-canvas border border-[#ECEAE4] flex items-center justify-center text-charcoal shrink-0">
                                    <LmsIcon name="download" className="w-4 h-4" />
                                </span>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>
    </div>
</template>
