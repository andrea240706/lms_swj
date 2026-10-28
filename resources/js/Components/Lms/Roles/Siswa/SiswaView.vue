<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import LmsCapsuleCalendar from '@/Components/Lms/LmsCapsuleCalendar.vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import LmsSkeletonLoader from '@/Components/Lms/LmsSkeletonLoader.vue';
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';
import MapelSiswaView from './MapelSiswaView.vue';
import { initialMapelList } from './mapelData.js';

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    activeTab: {
        type: String,
        default: 'home',
    },
    selectedDay: {
        type: Number,
        default: 18,
    },
    days: {
        type: Array,
        default: () => [],
    },
    assignments: {
        type: Array,
        default: () => [],
    },
    studentAttendanceDone: {
        type: Boolean,
        default: false,
    },
    materialsLock: {
        type: Object,
        default: () => ({ 1: false, 2: false, 3: true, 4: true }),
    },
    isSidebarCollapsed: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits([
    'selectTab',
    'selectDay',
    'openAttendanceModal',
    'openLessonSheet',
    'openUploadModal',
    'openMenu',
    'openProfile',
    'toggleSidebar',
]);

// ===== SKELETON LOADER =====
const isLoading = ref(true);
const triggerLoading = (ms = 450) => {
    isLoading.value = true;
    setTimeout(() => {
        isLoading.value = false;
    }, ms);
};
onMounted(() => {
    triggerLoading(600);
});
watch(() => props.activeTab, () => {
    triggerLoading(450);
});

const subjectGrades = [
    { mapel: 'Pemrograman Web & Perangkat Bergerak', avg: 91, kkm: 78 },
    { mapel: 'Basis Data Rombel XII', avg: 88, kkm: 78 },
    { mapel: 'Infrastruktur Jaringan', avg: 84, kkm: 75 },
    { mapel: 'Bahasa Inggris Vokasi', avg: 89, kkm: 75 },
];

const getDayLabel = (n) => {
    const map = { 18: 'Senin', 19: 'Selasa', 20: 'Rabu', 21: 'Kamis', 22: 'Jumat', 23: 'Sabtu' };
    return map[n] || '-';
};

const schedule = {
    18: [
        { id: 1, name: 'Pemrograman Web', teacher: 'Budi Santoso', code: 'RPL-501', room: 'Lab RPL 2', time: '08:00-10:30', progress: 60, completedMeetings: 3, totalMeetings: 5, heroBg: 'hero-sage' },
    ],
    19: [
        { id: 2, name: 'Basis Data', teacher: 'Budi Santoso', code: 'RPL-302', room: 'Lab RPL 1', time: '10:30-12:00', progress: 40, completedMeetings: 2, totalMeetings: 5, heroBg: 'hero-blossom' },
    ],
    20: [
        { id: 3, name: 'Bahasa Inggris Vokasi', teacher: 'Siti Rahma', code: 'UMM-101', room: 'Ruang 12', time: '07:00-08:30', progress: 80, completedMeetings: 4, totalMeetings: 5, heroBg: 'hero-sky' },
    ],
    21: [],
    22: [
        { id: 4, name: 'Infrastruktur Jaringan', teacher: 'Eko Prabowo', code: 'TKJ-203', room: 'Lab TKJ', time: '07:00-09:30', progress: 20, completedMeetings: 1, totalMeetings: 5, heroBg: 'hero-amber' },
    ],
    23: [],
};

const activeDaySubject = computed(() => {
    const list = schedule[props.selectedDay] || [];
    return list[0] || {
        name: 'Tidak Ada Kelas', teacher: '-', code: '-', room: '-',
        time: '-', progress: 0, completedMeetings: 0, totalMeetings: 0, heroBg: 'hero-sage',
    };
});

// Status badges
const assignmentStatusColor = (status) => {
    if (status === 'Dinilai') return 'bg-sage-light text-sage-dark';
    if (status === 'Perlu Revisi') return 'bg-blossom-light text-blossom-dark';
    if (status === 'Menunggu Penilaian Guru') return 'bg-sky-light text-sky-dark';
    return 'bg-[#FFF3E0] text-[#E65100]';
};

const attendanceStatusColor = (status) => {
    if (status === 'Hadir') return 'bg-sage-light text-sage-dark';
    if (status === 'Sakit') return 'bg-sky-light text-sky-dark';
    if (status === 'Izin') return 'bg-[#FFF3E0] text-[#E65100]';
    return 'bg-blossom-light text-blossom-dark';
};
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <Transition name="tab-view" mode="out-in">

            <!-- ========================================== -->
            <!-- TAB 1: HOME                               -->
            <!-- ========================================== -->
            <div
                v-if="activeTab === 'home'"
                key="home"
                class="flex-1 flex flex-col h-full overflow-hidden"
            >
                <LmsTopBar
                    :role="role"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    @openMenu="$emit('openMenu')"
                    @openProfile="$emit('openProfile')"
                    @toggleSidebar="$emit('toggleSidebar')"
                />

                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
                    <div class="w-full max-w-[1800px] mx-auto">

                        <!-- SKELETON -->
                        <LmsSkeletonLoader v-if="isLoading" type="dashboard" />

                        <!-- CONTENT -->
                        <template v-else>

                        <!-- 1. Hero: Sesi Aktif -->
                        <div
                            class="rounded-[28px] p-5 relative overflow-hidden soft-shadow transition-all duration-300 mb-5"
                            :class="activeDaySubject.heroBg"
                        >
                            <div class="relative z-10 w-full">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 text-[11px] font-bold text-charcoal/80">
                                        <span class="w-2 h-2 rounded-full bg-[#2E7D32] live-dot"></span>
                                        <span>Sesi Aktif • {{ activeDaySubject.room }}</span>
                                    </div>
                                    <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-white/90 text-charcoal border border-white/60 shadow-2xs">
                                        {{ getDayLabel(selectedDay) }}, {{ selectedDay }} Okt
                                    </span>
                                </div>
                                <h3 class="text-[21px] font-extrabold text-charcoal mt-1.5 leading-tight">
                                    {{ activeDaySubject.name }}
                                </h3>
                                <p class="text-xs font-semibold text-charcoal/75 mt-1">
                                    Pengampu: {{ activeDaySubject.teacher }} • Kode: {{ activeDaySubject.code }}
                                </p>

                                <!-- Step Progress Bar -->
                                <div class="mt-3.5">
                                    <div class="flex items-center justify-between text-[11px] font-bold text-charcoal mb-1">
                                        <span>Progres Belajar: Pertemuan {{ activeDaySubject.completedMeetings }}/{{ activeDaySubject.totalMeetings }}</span>
                                        <span>{{ activeDaySubject.progress }}%</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-white/70 rounded-full flex items-center">
                                        <div
                                            class="h-1.5 bg-charcoal rounded-full relative transition-all duration-500"
                                            :style="{ width: `${activeDaySubject.progress}%` }"
                                        >
                                            <span class="absolute -right-1.5 -top-1 w-3.5 h-3.5 rounded-full bg-charcoal border-2 border-white"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Capsule Calendar -->
                        <LmsCapsuleCalendar
                            title="Jadwal Pelajaran"
                            :days="days"
                            :selectedDay="selectedDay"
                            @selectDay="(d) => $emit('selectDay', d)"
                        />

                        <!-- 3. Daily Timeline -->
                        <div class="mt-5">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-[15px] font-extrabold text-charcoal">
                                    Agenda Pelajaran — {{ getDayLabel(selectedDay) }}
                                </h2>
                            </div>
                            <div
                                v-if="(schedule[selectedDay] || []).length === 0"
                                class="bg-white rounded-[22px] p-5 border border-[#ECEAE4] text-center text-subtext text-xs"
                            >
                                Tidak ada sesi pelajaran hari ini 🎉
                            </div>
                            <div
                                v-for="s in (schedule[selectedDay] || [])"
                                :key="s.id"
                                @click="$emit('openLessonSheet', s.name, s.name)"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/25 active:scale-[0.99] transition-all flex items-center gap-3.5 mb-2.5 group"
                            >
                                <div class="w-10 h-10 rounded-2xl bg-charcoal/10 flex items-center justify-center shrink-0">
                                    <LmsIcon name="book" className="w-5 h-5 text-charcoal" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-extrabold text-charcoal group-hover:text-black truncate">{{ s.name }}</h4>
                                    <p class="text-[11px] text-subtext">{{ s.time }} • {{ s.room }}</p>
                                </div>
                                <LmsIcon name="chevronRight" className="w-4 h-4 text-subtext shrink-0" />
                            </div>
                        </div>

                        <!-- 4. Bottom Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-1">
                            <!-- Tugas -->
                            <div class="bg-white rounded-[28px] p-5 border border-[#ECEAE4] soft-shadow">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-full bg-blossom-light text-blossom-dark flex items-center justify-center">
                                            <LmsIcon name="task" className="w-3.5 h-3.5" />
                                        </span>
                                        <h3 class="text-sm font-extrabold text-charcoal">Tugas Terdekat</h3>
                                    </div>
                                    <button type="button" @click="$emit('selectTab', 'tugas')" class="text-xs font-bold text-blossom-dark hover:underline cursor-pointer">
                                        Lihat Semua →
                                    </button>
                                </div>
                                <div class="space-y-2.5">
                                    <div
                                        v-for="a in assignments.slice(0,3)"
                                        :key="a.id"
                                        @click="$emit('openUploadModal', a.id)"
                                        class="bg-canvas rounded-[20px] p-3.5 border border-[#ECEAE4] cursor-pointer hover:border-charcoal/20 active:scale-[0.99] transition-all group flex items-center gap-3"
                                    >
                                        <div class="flex-1 min-w-0">
                                            <div class="text-[10px] font-bold text-subtext">{{ a.subject }}</div>
                                            <div class="text-xs font-extrabold text-charcoal group-hover:text-black truncate">{{ a.title }}</div>
                                            <div class="text-[10px] text-subtext mt-0.5">{{ a.due }}</div>
                                        </div>
                                        <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full shrink-0" :class="assignmentStatusColor(a.status)">
                                            {{ a.status }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Nilai -->
                            <div class="bg-white rounded-[28px] p-5 border border-[#ECEAE4] soft-shadow">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-7 h-7 rounded-full bg-sage-light text-sage-dark flex items-center justify-center">
                                        <LmsIcon name="chart" className="w-3.5 h-3.5" />
                                    </span>
                                    <h3 class="text-sm font-extrabold text-charcoal">Rata-Rata Nilai</h3>
                                </div>
                                <div class="space-y-2.5">
                                    <div
                                        v-for="s in subjectGrades"
                                        :key="s.mapel"
                                        class="flex items-center gap-3"
                                    >
                                        <div class="flex-1 min-w-0">
                                            <div class="text-[11px] font-bold text-charcoal truncate">{{ s.mapel }}</div>
                                            <div class="mt-1 w-full h-1.5 bg-muted rounded-full overflow-hidden">
                                                <div
                                                    class="h-full rounded-full transition-all"
                                                    :class="s.avg >= s.kkm ? 'bg-charcoal' : 'bg-blossom-dark'"
                                                    :style="`width:${s.avg}%`"
                                                ></div>
                                            </div>
                                        </div>
                                        <span
                                            class="text-xs font-extrabold shrink-0 w-8 text-right"
                                            :class="s.avg >= s.kkm ? 'text-charcoal' : 'text-blossom-dark'"
                                        >
                                            {{ s.avg }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        </template>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 2: MAPEL                             -->
            <!-- ========================================== -->
            <MapelSiswaView
                v-else-if="activeTab === 'mapel'"
                key="mapel"
                :role="role"
                :materialsLock="materialsLock"
                :isSidebarCollapsed="isSidebarCollapsed"
                @openMenu="$emit('openMenu')"
                @openProfile="$emit('openProfile')"
                @toggleSidebar="$emit('toggleSidebar')"
            />

            <!-- ========================================== -->
            <!-- TAB 3: TUGAS                              -->
            <!-- ========================================== -->
            <div
                v-else-if="activeTab === 'tugas'"
                key="tugas"
                class="flex-1 flex flex-col h-full overflow-hidden"
            >
                <LmsTopBar
                    :role="role"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    title="Tugas & Pengumpulan"
                    @openMenu="$emit('openMenu')"
                    @openProfile="$emit('openProfile')"
                    @toggleSidebar="$emit('toggleSidebar')"
                />

                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
                    <div class="w-full max-w-[1800px] mx-auto">
                        <!-- SKELETON -->
                        <LmsSkeletonLoader v-if="isLoading" type="card-list" :rows="3" />
                        <!-- CONTENT -->
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-1">
                            <div
                                v-for="a in assignments"
                                :key="a.id"
                                @click="$emit('openUploadModal', a.id)"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all"
                            >
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-[10px] font-bold text-subtext">{{ a.subject }}</div>
                                        <h4 class="text-sm font-extrabold text-charcoal leading-snug mt-0.5">{{ a.title }}</h4>
                                        <p class="text-[11px] text-subtext mt-1">Tenggat: {{ a.due }}</p>
                                    </div>
                                    <span
                                        class="text-[10px] font-extrabold px-2.5 py-1 rounded-full shrink-0"
                                        :class="assignmentStatusColor(a.status)"
                                    >
                                        {{ a.status }}
                                    </span>
                                </div>
                                <div v-if="a.score" class="pt-3 border-t border-[#F2F0EC] flex items-center gap-2">
                                    <span class="text-[11px] font-bold text-charcoal">Nilai: {{ a.score }}</span>
                                    <span class="text-[11px] text-subtext">• {{ a.feedback }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 4: PRESENSI & REKAP NILAI            -->
            <!-- ========================================== -->
            <div
                v-else-if="activeTab === 'presensi'"
                key="presensi"
                class="flex-1 flex flex-col h-full overflow-hidden"
            >
                <LmsTopBar
                    :role="role"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    title="Presensi & Rekap Nilai"
                    @openMenu="$emit('openMenu')"
                    @openProfile="$emit('openProfile')"
                    @toggleSidebar="$emit('toggleSidebar')"
                />

                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
                    <div class="w-full max-w-[1800px] mx-auto">
                        <!-- SKELETON -->
                        <LmsSkeletonLoader v-if="isLoading" type="rekap-list" :rows="4" />
                        <!-- CONTENT -->
                        <template v-else>
                            <LmsTopBar
                                :role="role"
                                :isSidebarCollapsed="isSidebarCollapsed"
                                @openProfile="$emit('openProfile')"
                            />

                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start mt-1">
                                <!-- Left Column: Attendance Macro Card -->
                                <div class="lg:col-span-5 space-y-4">
                                    <div class="hero-sky rounded-[28px] p-5 soft-shadow border border-sky-light/40">
                                        <div class="flex items-center justify-between">
                                            <span class="bg-white/95 px-3 py-1 rounded-full text-[10px] font-extrabold text-charcoal shadow-2xs">
                                                Semester Ganjil 2026/2027
                                            </span>
                                            <span class="text-xs font-extrabold text-[#2E7D32] flex items-center gap-1">
                                                <span class="w-2 h-2 rounded-full bg-[#2E7D32] animate-pulse"></span>
                                                Aktif
                                            </span>
                                        </div>
                                        <h3 class="text-xl font-black text-charcoal mt-3">Kehadiran: 96.4%</h3>
                                        <p class="text-xs text-charcoal/70 mt-0.5">Total 56 Hari Efektif KBM Terjadwal</p>
                                        <div class="grid grid-cols-4 gap-2 mt-4">
                                            <div v-for="(v, k) in { Hadir: 54, Sakit: 1, Izin: 1, Alpa: 0 }" :key="k" class="bg-white/95 rounded-2xl p-2.5 text-center shadow-xs">
                                                <div class="text-base font-extrabold text-charcoal">{{ v }}</div>
                                                <div class="text-[10px] font-bold text-subtext">{{ k }}</div>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            @click="$emit('openAttendanceModal')"
                                            class="mt-4 w-full py-2.5 rounded-2xl text-xs font-extrabold bg-charcoal text-white hover:bg-black active:scale-95 shadow-sm transition-all cursor-pointer flex items-center justify-center gap-2"
                                        >
                                            <LmsIcon name="clock" className="w-4 h-4" />
                                            <span>Isi Presensi Masuk</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Right Column: Nilai -->
                                <div class="lg:col-span-7 space-y-4">
                                    <div class="bg-white rounded-[26px] p-5 border border-[#ECEAE4] soft-shadow">
                                        <h3 class="text-sm font-extrabold text-charcoal mb-3">Rata-rata Nilai Mapel</h3>
                                        <div class="space-y-3">
                                            <div v-for="s in subjectGrades" :key="s.mapel" class="flex items-center gap-3">
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-[11px] font-bold text-charcoal truncate mb-1">{{ s.mapel }}</div>
                                                    <div class="w-full h-1.5 bg-muted rounded-full overflow-hidden">
                                                        <div
                                                            class="h-full bg-charcoal rounded-full"
                                                            :style="`width:${s.avg}%`"
                                                        ></div>
                                                    </div>
                                                </div>
                                                <span class="text-sm font-extrabold text-charcoal shrink-0">{{ s.avg }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

        </Transition>
    </div>
</template>

<style scoped>
.tab-view-enter-active,
.tab-view-leave-active {
    transition: opacity 0.22s cubic-bezier(0.16, 1, 0.3, 1), transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}
.tab-view-enter-from {
    opacity: 0;
    transform: translateY(6px) scale(0.995);
}
.tab-view-leave-to {
    opacity: 0;
    transform: translateY(-6px) scale(0.995);
}
.live-dot {
    animation: pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.7); }
}
</style>
