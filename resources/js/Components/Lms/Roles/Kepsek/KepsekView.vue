<script setup>
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';

defineProps({
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

defineEmits(['openMenu', 'openProfile', 'showToast', 'toggleSidebar']);

const majors = [
    { jur: 'Rekayasa Perangkat Lunak (RPL)', hadir: '97.1%', avg: '87.5' },
    { jur: 'Teknik Komputer & Jaringan (TKJ)', hadir: '95.4%', avg: '85.2' },
    { jur: 'Desain Komunikasi Visual (DKV)', hadir: '94.8%', avg: '86.9' },
    { jur: 'Akuntansi & Keuangan Lembaga', hadir: '96.9%', avg: '88.1' },
];
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <LmsTopBar
            :role="role"
            :isSidebarCollapsed="isSidebarCollapsed"
            title="Pantauan Eksekutif Sekolah"
            @openMenu="$emit('openMenu')"
            @openProfile="$emit('openProfile')"
            @toggleSidebar="$emit('toggleSidebar')"
        />

        <div class="flex-1 overflow-y-auto no-scrollbar px-6 pb-28">
            <!-- Hero Card: Executive Macro Indicators -->
            <div class="hero-amber rounded-[28px] p-5 soft-shadow">
                <span
                    class="bg-white px-3 py-1 rounded-full text-[11px] font-extrabold text-charcoal shadow-2xs"
                >
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
            <h3 class="text-[15px] font-extrabold text-charcoal mt-5 mb-2.5">
                Capaian per Program Keahlian (Jurusan)
            </h3>
            <div class="space-y-2.5">
                <div
                    v-for="j in majors"
                    :key="j.jur"
                    class="bg-white rounded-[24px] p-3.5 border border-[#ECEAE4] soft-shadow flex items-center justify-between"
                >
                    <div>
                        <div class="text-xs font-extrabold text-charcoal">{{ j.jur }}</div>
                        <div class="text-[11px] text-subtext mt-0.5">
                            Kehadiran: {{ j.hadir }}
                        </div>
                    </div>
                    <span
                        class="px-3 py-1 rounded-full bg-sage-light text-sage-dark text-xs font-extrabold shrink-0 ml-2"
                    >
                        Nilai: {{ j.avg }}
                    </span>
                </div>
            </div>

            <!-- Official Report Downloads -->
            <h3 class="text-[15px] font-extrabold text-charcoal mt-5 mb-2.5">
                Unduh Laporan Resmi (PDF / Excel)
            </h3>
            <div class="grid grid-cols-2 gap-2.5">
                <button
                    type="button"
                    @click="$emit('showToast', 'Laporan Kehadiran Sekolah (PDF) berhasil diunduh.')"
                    class="bg-charcoal text-white rounded-[22px] p-3.5 text-left flex flex-col justify-between cursor-pointer hover:opacity-90 active:scale-95 transition-all shadow-xs"
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
                    class="bg-white border border-[#ECEAE4] text-charcoal rounded-[22px] p-3.5 text-left flex flex-col justify-between cursor-pointer hover:border-charcoal/40 active:scale-95 transition-all shadow-2xs"
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
</template>
