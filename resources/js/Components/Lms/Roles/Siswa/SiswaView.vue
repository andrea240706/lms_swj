<script setup>
import { computed } from 'vue';
import LmsCapsuleCalendar from '@/Components/Lms/LmsCapsuleCalendar.vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
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


const subjectGrades = [
    { mapel: 'Pemrograman Web & Perangkat Bergerak', avg: 91, kkm: 78 },
    { mapel: 'Basis Data Rombel XII', avg: 88, kkm: 78 },
    { mapel: 'Infrastruktur Jaringan', avg: 84, kkm: 75 },
    { mapel: 'Bahasa Inggris Vokasi', avg: 89, kkm: 75 },
];

const dayNamesMap = {
    18: 'Senin',
    19: 'Selasa',
    20: 'Rabu',
    21: 'Kamis',
    22: 'Jumat',
    23: 'Sabtu',
};

const getDayLabel = (dayNum) => {
    const found = props.days?.find((d) => d.n === dayNum);
    return found?.full || dayNamesMap[dayNum] || 'Senin';
};

const dayKey = computed(() => {
    return getDayLabel(props.selectedDay).toLowerCase();
});

// Dynamic Daily Agenda mapping using real mapelData
const dailyAgendaMap = computed(() => {
    const pwpb = initialMapelList.find((m) => m.id === 'pwpb') || initialMapelList[0];
    const bd = initialMapelList.find((m) => m.id === 'bd') || initialMapelList[1];
    const ppl = initialMapelList.find((m) => m.id === 'ppl') || initialMapelList[2];
    const mtk = initialMapelList.find((m) => m.id === 'mtk') || initialMapelList[3];
    const eng = initialMapelList.find((m) => m.id === 'eng') || initialMapelList[4];

    return {
        senin: [
            {
                time: '08:00',
                timeRange: '08:00 – 10:30',
                mapel: pwpb,
                topic: 'Konsep MVC & Setup Laravel 11',
                room: pwpb.room,
                teacher: pwpb.teacher,
                taskCount: 1,
            },
            {
                time: '11:00',
                timeRange: '11:00 – 13:00',
                mapel: mtk,
                topic: 'Aljabar Boolean & Gerbang Logika Komputasi',
                room: mtk.room,
                teacher: mtk.teacher,
                taskCount: 0,
            },
        ],
        selasa: [
            {
                time: '08:00',
                timeRange: '08:00 – 10:15',
                mapel: bd,
                topic: 'Normalisasi Basis Data Relasional (1NF, 2NF, 3NF)',
                room: bd.room,
                teacher: bd.teacher,
                taskCount: 1,
            },
            {
                time: '10:45',
                timeRange: '10:45 – 12:30',
                mapel: ppl,
                topic: 'Analisis Kebutuhan Sistem & Pembuatan User Persona',
                room: ppl.room,
                teacher: ppl.teacher,
                taskCount: 0,
            },
        ],
        rabu: [
            {
                time: '08:00',
                timeRange: '08:00 – 10:30',
                mapel: ppl,
                topic: 'Perancangan Diagram UML & Use Case Architecture',
                room: ppl.room,
                teacher: ppl.teacher,
                taskCount: 1,
            },
            {
                time: '11:00',
                timeRange: '11:00 – 13:00',
                mapel: eng,
                topic: 'Technical English: Software Pitching & QA Session',
                room: eng.room,
                teacher: eng.teacher,
                taskCount: 1,
            },
        ],
        kamis: [
            {
                time: '07:30',
                timeRange: '07:30 – 10:00',
                mapel: mtk,
                topic: 'Teori Graf, Pohon Biner & Kompleksitas Algoritma',
                room: mtk.room,
                teacher: mtk.teacher,
                taskCount: 0,
            },
            {
                time: '10:30',
                timeRange: '10:30 – 13:00',
                mapel: pwpb,
                topic: 'Eloquent ORM Relationship & Database Seeding',
                room: pwpb.room,
                teacher: pwpb.teacher,
                taskCount: 1,
            },
        ],
        jumat: [
            {
                time: '08:00',
                timeRange: '08:00 – 10:30',
                mapel: eng,
                topic: 'Technical Writing: API Documentation & README Guide',
                room: eng.room,
                teacher: eng.teacher,
                taskCount: 0,
            },
            {
                time: '10:45',
                timeRange: '10:45 – 11:45',
                mapel: bd,
                topic: 'Query Optimization, Indexing & Store Procedure',
                room: bd.room,
                teacher: bd.teacher,
                taskCount: 1,
            },
        ],
        sabtu: [
            {
                time: '08:00',
                timeRange: '08:00 – 10:30',
                mapel: pwpb,
                topic: 'Review Portofolio Web & Mentoring Proyek Mandiri',
                room: pwpb.room,
                teacher: pwpb.teacher,
                taskCount: 1,
            },
            {
                time: '10:45',
                timeRange: '10:45 – 12:30',
                mapel: bd,
                topic: 'Klinik Database & Konsultasi Skema Tabel',
                room: bd.room,
                teacher: bd.teacher,
                taskCount: 0,
            },
        ],
    };
});

const currentAgendaList = computed(() => {
    return dailyAgendaMap.value[dayKey.value] || dailyAgendaMap.value.senin;
});

const activeDaySubject = computed(() => {
    return currentAgendaList.value[0]?.mapel || initialMapelList[0];
});
</script>

<template>
    <div class="flex-1 flex flex-col h-full overflow-hidden">
        <Transition name="tab-view" mode="out-in">
            <!-- ========================================== -->
            <!-- TAB 1: HOME (SESI AKTIF & TIMELINE)       -->
            <!-- ========================================== -->
            <div
                v-if="activeTab === 'home'"
                key="home"
                class="flex-1 flex flex-col h-full overflow-hidden"
            >
                <LmsTopBar
                    :role="role"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    @toggleSidebar="$emit('toggleSidebar')"
                    @openMenu="$emit('openMenu')"
                    @openProfile="$emit('openProfile')"
                />

                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 pb-28 lg:pb-8">
                    <!-- Responsive Multi-Column Desktop Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                        <!-- Left Primary Column (7 cols lg, 8 cols xl): Hero Card, Weekly Schedule & Agenda -->
                        <div class="lg:col-span-7 xl:col-span-8 space-y-5">
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <h2 class="text-[17px] font-extrabold text-charcoal">
                                        Kelas Hari Ini
                                    </h2>
                                    <span class="hidden sm:inline-block text-xs font-bold text-subtext">
                                        {{ getDayLabel(selectedDay) }}, {{ selectedDay }} Oktober 2026
                                    </span>
                                </div>

                                <!-- Hero Card: Active Session (Berubah dinamis sesuai hari yang dipilih) -->
                                <div
                                    class="rounded-[28px] p-5 relative overflow-hidden soft-shadow transition-all duration-300"
                                    :class="activeDaySubject.heroBg"
                                >
                                    <div class="relative z-10 w-full">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1.5 text-[11px] font-bold text-charcoal/80">
                                                <span class="w-2 h-2 rounded-full bg-[#2E7D32] live-dot"></span>
                                                <span>Sesi Aktif • {{ activeDaySubject.room }}</span>
                                            </div>
                                            <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-white/90 text-charcoal border border-white/60 shadow-2xs">
                                                {{ getDayLabel(selectedDay) }}
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
                                                    <span
                                                        class="absolute -right-1.5 -top-1 w-3.5 h-3.5 rounded-full bg-charcoal border-2 border-white"
                                                    ></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Capsule Weekly Schedule -->
                            <LmsCapsuleCalendar
                                title="Jadwal Pelajaran"
                                :days="days"
                                :selectedDay="selectedDay"
                                @selectDay="(d) => $emit('selectDay', d)"
                            />

                            <!-- Daily Timeline (Berubah otomatis sesuai mapel pada hari yang dipilih) -->
                            <div class="mt-5">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-[16px] font-extrabold text-charcoal">Agenda Harian</h3>
                                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-charcoal text-white">
                                            {{ currentAgendaList.length }} Sesi
                                        </span>
                                    </div>
                                    <span class="text-xs font-bold text-subtext">
                                        {{ getDayLabel(selectedDay) }}, {{ selectedDay }} Okt
                                    </span>
                                </div>

                                <TransitionGroup
                                    name="agenda-item"
                                    tag="div"
                                    class="space-y-3"
                                >
                                    <div
                                        v-for="item in currentAgendaList"
                                        :key="item.mapel.id + '-' + item.time"
                                        class="flex items-start gap-3 border-t first:border-t-0 border-dashed border-[#E2DFD7] pt-3 first:pt-0"
                                    >
                                        <div class="w-12 pt-1 flex flex-col shrink-0">
                                            <span class="text-xs font-extrabold text-charcoal">{{ item.time }}</span>
                                            <span class="text-[9px] font-bold text-subtext">WIB</span>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div
                                                @click="$emit('selectTab', 'mapel')"
                                                class="cursor-pointer bg-white rounded-2xl p-3 border border-[#ECEAE4] soft-shadow flex items-center justify-between hover:border-charcoal/30 hover:shadow-md transition-all group"
                                            >
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <span
                                                        class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 transition-transform group-hover:scale-105"
                                                        :class="item.mapel.iconBg"
                                                    >
                                                        {{ item.mapel.shortName.charAt(0) }}
                                                    </span>
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-1.5 flex-wrap">
                                                            <div class="text-xs font-extrabold text-charcoal truncate">
                                                                {{ item.mapel.name }}
                                                            </div>
                                                            <span
                                                                class="text-[9px] font-bold px-1.5 py-0.2 rounded-full border shrink-0"
                                                                :class="item.mapel.badgeColor"
                                                            >
                                                                {{ item.mapel.code }}
                                                            </span>
                                                        </div>
                                                        <div class="text-[10px] text-subtext mt-0.5 truncate">
                                                            {{ item.timeRange }} • {{ item.room }} • {{ item.topic }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex items-center gap-1.5 shrink-0 ml-2">
                                                    <span
                                                        v-if="item.taskCount > 0"
                                                        class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-blossom-light text-blossom-dark"
                                                    >
                                                        {{ item.taskCount }} Tugas
                                                    </span>
                                                    <div class="w-7 h-7 rounded-full bg-canvas flex items-center justify-center text-charcoal group-hover:bg-charcoal group-hover:text-white transition-colors">
                                                        <LmsIcon name="arrowUpRight" className="w-3.5 h-3.5" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </TransitionGroup>
                            </div>
                        </div>

                        <!-- Right Secondary Column (5 cols lg, 4 cols xl): Desktop Academic Widgets -->
                        <div class="hidden lg:flex lg:col-span-5 xl:col-span-4 flex-col space-y-5">
                            <!-- 1. Semester Attendance Macro Card -->
                            <div class="hero-sky rounded-[28px] p-5 soft-shadow border border-sky-light/50">
                                <div class="flex items-center justify-between">
                                    <span class="bg-white/95 px-3 py-1 rounded-full text-[10px] font-extrabold text-charcoal shadow-2xs">
                                        Semester Ganjil 2026/2027
                                    </span>
                                    <span class="text-xs font-extrabold text-[#2E7D32] flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-[#2E7D32] animate-pulse"></span>
                                        Aktif
                                    </span>
                                </div>
                                <h3 class="text-xl font-black text-charcoal mt-3">
                                    Kehadiran: 96.4%
                                </h3>
                                <p class="text-xs text-charcoal/70 mt-0.5">
                                    Total 56 Hari Efektif KBM Terjadwal
                                </p>

                                <div class="grid grid-cols-4 gap-2 mt-4">
                                    <div class="bg-white/95 rounded-2xl p-2.5 text-center shadow-xs">
                                        <div class="text-base font-extrabold text-charcoal">54</div>
                                        <div class="text-[10px] font-bold text-subtext">Hadir</div>
                                    </div>
                                    <div class="bg-white/95 rounded-2xl p-2.5 text-center shadow-xs">
                                        <div class="text-base font-extrabold text-charcoal">1</div>
                                        <div class="text-[10px] font-bold text-subtext">Sakit</div>
                                    </div>
                                    <div class="bg-white/95 rounded-2xl p-2.5 text-center shadow-xs">
                                        <div class="text-base font-extrabold text-charcoal">1</div>
                                        <div class="text-[10px] font-bold text-subtext">Izin</div>
                                    </div>
                                    <div class="bg-white/95 rounded-2xl p-2.5 text-center shadow-xs">
                                        <div class="text-base font-extrabold text-charcoal">0</div>
                                        <div class="text-[10px] font-bold text-subtext">Alpa</div>
                                    </div>
                                </div>

                                <!-- Quick Action: Presensi Hari Ini -->
                                <button
                                    type="button"
                                    @click="$emit('openAttendanceModal')"
                                    class="mt-4 w-full py-2.5 rounded-2xl text-xs font-extrabold transition-all cursor-pointer flex items-center justify-center gap-2"
                                    :class="
                                        studentAttendanceDone
                                            ? 'bg-white/90 text-[#2E7D32] border border-[#2E7D32]/30'
                                            : 'bg-charcoal text-white hover:bg-black active:scale-95 shadow-sm'
                                    "
                                >
                                    <LmsIcon :name="studentAttendanceDone ? 'check' : 'clock'" className="w-4 h-4" />
                                    <span>{{ studentAttendanceDone ? '✓ Sudah Presensi Mandiri' : 'Isi Presensi Masuk' }}</span>
                                </button>
                            </div>

                            <!-- 2. Ringkasan Tugas Menanti Card -->
                            <div class="bg-white rounded-[26px] p-5 border border-[#ECEAE4] soft-shadow">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-full bg-blossom-light text-blossom-dark flex items-center justify-center">
                                            <LmsIcon name="task" className="w-4 h-4" />
                                        </span>
                                        <h3 class="text-sm font-extrabold text-charcoal">Tugas Terdekat</h3>
                                    </div>
                                    <button
                                        type="button"
                                        @click="$emit('selectTab', 'tugas')"
                                        class="text-[11px] font-extrabold text-subtext hover:text-charcoal cursor-pointer flex items-center gap-1"
                                    >
                                        Semua
                                        <LmsIcon name="chevronRight" className="w-3.5 h-3.5" />
                                    </button>
                                </div>

                                <div class="space-y-2.5">
                                    <div
                                        v-for="a in assignments.slice(0, 2)"
                                        :key="a.id"
                                        @click="$emit('selectTab', 'tugas')"
                                        class="p-3 rounded-2xl bg-canvas hover:bg-canvas/80 border border-[#ECEAE4] cursor-pointer transition-all"
                                    >
                                        <div class="flex items-center justify-between text-[10px]">
                                            <span class="font-bold text-subtext">{{ a.subject }}</span>
                                            <span class="font-extrabold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-100">
                                                {{ a.due }}
                                            </span>
                                        </div>
                                        <div class="text-xs font-extrabold text-charcoal mt-1 truncate">
                                            {{ a.title }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Mini Transkrip Akademik Preview -->
                            <div class="bg-white rounded-[26px] p-5 border border-[#ECEAE4] soft-shadow">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-full bg-sage-light text-sage-dark flex items-center justify-center">
                                            <LmsIcon name="chart" className="w-4 h-4" />
                                        </span>
                                        <h3 class="text-sm font-extrabold text-charcoal">Rata-Rata Nilai</h3>
                                    </div>
                                    <button
                                        type="button"
                                        @click="$emit('selectTab', 'presensi')"
                                        class="text-[11px] font-extrabold text-subtext hover:text-charcoal cursor-pointer flex items-center gap-1"
                                    >
                                        Detail
                                        <LmsIcon name="chevronRight" className="w-3.5 h-3.5" />
                                    </button>
                                </div>

                                <div class="space-y-2">
                                    <div
                                        v-for="n in subjectGrades.slice(0, 3)"
                                        :key="n.mapel"
                                        class="flex items-center justify-between p-2 rounded-xl bg-canvas text-xs"
                                    >
                                        <span class="font-bold text-charcoal truncate max-w-[70%]">
                                            {{ n.mapel }}
                                        </span>
                                        <span class="font-black px-2 py-0.5 rounded-full bg-sage-light text-sage-dark text-[11px]">
                                            {{ n.avg }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 2: MAPEL (DAFTAR MAPEL & PERTEMUAN)    -->
            <!-- ========================================== -->
            <div
                v-else-if="activeTab === 'mapel' || activeTab === 'materi'"
                key="mapel"
                class="flex-1 flex flex-col h-full overflow-hidden"
            >
                <MapelSiswaView
                    :role="role"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    @toggleSidebar="$emit('toggleSidebar')"
                    @openMenu="$emit('openMenu')"
                    @openProfile="$emit('openProfile')"
                />
            </div>

            <!-- ========================================== -->
            <!-- TAB 3: TUGAS & AKTIVITAS                  -->
            <!-- ========================================== -->
            <div
                v-else-if="activeTab === 'tugas'"
                key="tugas"
                class="flex-1 flex flex-col h-full overflow-hidden"
            >
                <LmsTopBar
                    :role="role"
                    title="Tugas & Aktivitas"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    @toggleSidebar="$emit('toggleSidebar')"
                    @openMenu="$emit('openMenu')"
                    @openProfile="$emit('openProfile')"
                />

                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 pb-28 lg:pb-8">
                    <!-- Responsive Multi-Column Grid for Desktop -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-1">
                        <div
                            v-for="a in assignments"
                            :key="a.id"
                            class="bg-white rounded-[26px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow flex flex-col justify-between"
                        >
                            <div>
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-[10px] font-bold px-2.5 py-1 rounded-full"
                                        :class="
                                            a.score !== null
                                                ? 'bg-sage-light text-sage-dark'
                                                : 'bg-blossom-light text-blossom-dark'
                                        "
                                    >
                                        {{ a.status }}
                                    </span>
                                    <span class="text-[11px] font-bold text-subtext">
                                        Tenggat: {{ a.due }}
                                    </span>
                                </div>
                                <h4 class="text-[15px] font-extrabold text-charcoal mt-2.5 leading-snug">
                                    {{ a.title }}
                                </h4>
                                <p class="text-xs text-subtext mt-0.5">{{ a.subject }}</p>

                                <!-- Teacher Feedback Box -->
                                <div
                                    v-if="a.score !== null"
                                    class="mt-3 p-3 rounded-2xl bg-canvas border border-[#ECEAE4] flex items-center justify-between"
                                >
                                    <div>
                                        <div class="text-[10px] font-bold text-subtext uppercase tracking-wide">
                                            Umpan Balik Guru
                                        </div>
                                        <div class="text-xs font-medium text-charcoal mt-0.5">
                                            "{{ a.feedback }}"
                                        </div>
                                    </div>
                                    <div
                                        class="w-11 h-11 rounded-2xl bg-charcoal text-white flex flex-col items-center justify-center shrink-0 ml-3"
                                    >
                                        <span class="text-[9px] uppercase opacity-70">Nilai</span>
                                        <span class="text-sm font-extrabold leading-none">{{ a.score }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Submit / Revision Button -->
                            <div class="mt-4 pt-3 border-t border-[#F2F0EC] flex items-center justify-end gap-2">
                                <button
                                    v-if="a.allowRevision"
                                    type="button"
                                    @click="$emit('openUploadModal', a.id)"
                                    class="px-4 py-2 rounded-full bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer"
                                >
                                    {{ a.score !== null ? 'Unggah Revisi Tugas' : 'Unggah Berkas Tugas' }}
                                </button>
                                <span v-else class="text-[11px] font-bold text-[#2E7D32]">
                                    ✓ Tugas Tuntas
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 4: PRESENSI & REKAP NILAI             -->
            <!-- ========================================== -->
            <div
                v-else-if="activeTab === 'presensi'"
                key="presensi"
                class="flex-1 flex flex-col h-full overflow-hidden"
            >
                <LmsTopBar
                    :role="role"
                    title="Presensi & Transkrip"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    @toggleSidebar="$emit('toggleSidebar')"
                    @openMenu="$emit('openMenu')"
                    @openProfile="$emit('openProfile')"
                />

                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 pb-28 lg:pb-8">
                    <!-- Responsive Multi-Column Layout on Desktop -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start mt-1">
                        <!-- Left Column (5 cols): Attendance Macro Card -->
                        <div class="lg:col-span-5 space-y-4">
                            <div class="hero-sky rounded-[28px] p-5 soft-shadow border border-sky-light/40">
                                <span
                                    class="inline-block bg-white px-3 py-1 rounded-full text-[11px] font-bold text-charcoal shadow-2xs"
                                >
                                    Semester Ganjil 2026/2027
                                </span>
                                <h3 class="text-xl font-extrabold text-charcoal mt-2.5">
                                    Kehadiran: 96.4%
                                </h3>
                                <div class="grid grid-cols-4 gap-2 mt-4">
                                    <div class="bg-white/90 rounded-2xl p-2.5 text-center shadow-xs">
                                        <div class="text-base font-extrabold text-charcoal">54</div>
                                        <div class="text-[10px] font-bold text-subtext">Hadir</div>
                                    </div>
                                    <div class="bg-white/90 rounded-2xl p-2.5 text-center shadow-xs">
                                        <div class="text-base font-extrabold text-charcoal">1</div>
                                        <div class="text-[10px] font-bold text-subtext">Sakit</div>
                                    </div>
                                    <div class="bg-white/90 rounded-2xl p-2.5 text-center shadow-xs">
                                        <div class="text-base font-extrabold text-charcoal">1</div>
                                        <div class="text-[10px] font-bold text-subtext">Izin</div>
                                    </div>
                                    <div class="bg-white/90 rounded-2xl p-2.5 text-center shadow-xs">
                                        <div class="text-base font-extrabold text-charcoal">0</div>
                                        <div class="text-[10px] font-bold text-subtext">Alpa</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column (7 cols): Subject Average Transcript -->
                        <div class="lg:col-span-7 space-y-4">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="text-[15px] font-extrabold text-charcoal">
                                    Rekap Nilai Rata-Rata Mapel
                                </h3>
                                <span class="text-xs font-bold text-subtext">
                                    4 Mapel Terverifikasi
                                </span>
                            </div>

                            <div class="space-y-2.5">
                                <div
                                    v-for="n in subjectGrades"
                                    :key="n.mapel"
                                    class="bg-white rounded-[22px] p-3.5 border border-[#ECEAE4] flex items-center justify-between shadow-2xs hover:border-charcoal/20 transition-all"
                                >
                                    <div>
                                        <div class="text-xs font-extrabold text-charcoal">{{ n.mapel }}</div>
                                        <div class="text-[11px] text-subtext mt-0.5">
                                            KKM: {{ n.kkm }} • Status: Melampaui
                                        </div>
                                    </div>
                                    <span
                                        class="w-10 h-10 rounded-full bg-sage-light text-sage-dark font-extrabold text-xs flex items-center justify-center shrink-0 ml-2"
                                    >
                                        {{ n.avg }}
                                    </span>
                                </div>
                            </div>
                        </div>
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

.agenda-item-enter-active,
.agenda-item-leave-active {
    transition: opacity 0.22s cubic-bezier(0.16, 1, 0.3, 1), transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}

.agenda-item-enter-from {
    opacity: 0;
    transform: translateY(6px);
}

.agenda-item-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}
</style>
