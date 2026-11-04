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
    selectedDayDate: {
        type: String,
        default: '',
    },
    days: {
        type: Array,
        default: () => [],
    },
    calendarEvents: {
        type: Object,
        default: () => ({}),
    },
    assignments: {
        type: Array,
        default: () => [],
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
    'openLessonSheet',
    'openUploadModal',
    'openMenu',
    'openProfile',
    'openSettings',
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
    const found = props.days.find((d) => d.n === n);
    if (found) return found.full;
    const map = { 18: 'Senin', 19: 'Selasa', 20: 'Rabu', 21: 'Kamis', 22: 'Jumat', 23: 'Sabtu' };
    return map[n] || 'Senin';
};

const weeklySchedule = {
    Senin: [
        { id: 1, name: 'Pemrograman Web', teacher: 'Budi Santoso', code: 'RPL-501', room: 'Lab RPL 2', time: '08:00-10:30', progress: 60, completedMeetings: 3, totalMeetings: 5, heroBg: 'hero-sage' },
    ],
    Selasa: [
        { id: 2, name: 'Basis Data', teacher: 'Budi Santoso', code: 'RPL-302', room: 'Lab RPL 1', time: '10:30-12:00', progress: 40, completedMeetings: 2, totalMeetings: 5, heroBg: 'hero-blossom' },
    ],
    Rabu: [
        { id: 3, name: 'Bahasa Inggris Vokasi', teacher: 'Siti Rahma', code: 'UMM-101', room: 'Ruang 12', time: '07:00-08:30', progress: 80, completedMeetings: 4, totalMeetings: 5, heroBg: 'hero-sky' },
    ],
    Kamis: [],
    Jumat: [
        { id: 4, name: 'Infrastruktur Jaringan', teacher: 'Eko Prabowo', code: 'TKJ-203', room: 'Lab TKJ', time: '07:00-09:30', progress: 20, completedMeetings: 1, totalMeetings: 5, heroBg: 'hero-amber' },
    ],
    Sabtu: [],
};

const currentDayLabel = computed(() => getDayLabel(props.selectedDay));

const currentDayEvents = computed(() => {
    const found = props.days.find((d) => d.n === props.selectedDay);
    const dateStr = found ? found.date : props.selectedDayDate;
    return props.calendarEvents[dateStr] || null;
});

// Hanya hari libur resmi atau ujian (UTS/UAS) yang ditampilkan langsung di bawah kalender jadwal
const isCrucialScheduleEvent = computed(() => {
    return (
        currentDayEvents.value &&
        (currentDayEvents.value.type === 'holiday' ||
            currentDayEvents.value.type === 'leave' ||
            currentDayEvents.value.type === 'exam')
    );
});

// Peringatan hari nasional / agenda sekolah umum lainnya dipindahkan ke tempat terpisah
const isGeneralNationalEvent = computed(() => {
    return currentDayEvents.value && !isCrucialScheduleEvent.value;
});

const currentSchedule = computed(() => {
    const dayName = currentDayLabel.value;
    return weeklySchedule[dayName] || [];
});

const activeDaySubject = computed(() => {
    if (currentDayEvents.value?.type === 'holiday' || currentDayEvents.value?.type === 'leave') {
        return {
            name: currentDayEvents.value.name,
            teacher: 'Hari Libur Resmi (SKB 3 Menteri)',
            code: 'LIBUR-NASIONAL',
            room: 'Libur Sekolah',
            time: 'Seluruh Hari',
            progress: 100,
            completedMeetings: 0,
            totalMeetings: 0,
            heroBg: 'hero-blossom',
        };
    }

    if (currentDayEvents.value?.type === 'exam') {
        return {
            name: currentDayEvents.value.name,
            teacher: 'Panitia Asesmen Akademik SMK SWJ',
            code: 'UJIAN-SMK',
            room: 'Ruang Ujian CBT',
            time: '07:30-12:00',
            progress: 75,
            completedMeetings: 1,
            totalMeetings: 1,
            heroBg: 'hero-sky',
        };
    }

    const list = currentSchedule.value;
    return list[0] || {
        name: 'Tidak Ada Kelas',
        teacher: '-',
        code: '-',
        room: '-',
        time: '-',
        progress: 0,
        completedMeetings: 0,
        totalMeetings: 0,
        heroBg: 'hero-sage',
    };
});

// Status badges
const assignmentStatusColor = (status) => {
    if (status === 'Dinilai') return 'bg-sage-light text-sage-dark';
    if (status === 'Perlu Revisi') return 'bg-blossom-light text-blossom-dark';
    if (status === 'Menunggu Penilaian Guru') return 'bg-sky-light text-sky-dark';
    return 'bg-[#FFF3E0] text-[#E65100]';
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
                    @openSettings="$emit('openSettings')"
                    @toggleSidebar="$emit('toggleSidebar')"
                />

                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
                    <div class="w-full max-w-[1800px] mx-auto">

                        <!-- SKELETON -->
                        <LmsSkeletonLoader v-if="isLoading" type="dashboard" />

                        <!-- CONTENT -->
                        <template v-else>

                        <!-- 1. Hero: Sesi Aktif / Jadwal Mandiri (Ukuran Konsisten & Tidak Mengecil Saat Kosong) -->
                        <div
                            class="rounded-[28px] p-5 relative overflow-hidden soft-shadow transition-all duration-300 mb-5 min-h-[224px] flex flex-col justify-between"
                            :class="activeDaySubject.heroBg"
                        >
                            <div class="relative z-10 w-full flex flex-col justify-between h-full">
                                <!-- Top row -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1.5 text-[11px] font-bold text-charcoal/80">
                                        <span
                                            class="w-2 h-2 rounded-full"
                                            :class="currentSchedule.length > 0 ? 'bg-[#2E7D32] live-dot' : 'bg-subtext'"
                                        ></span>
                                        <span>
                                            {{ currentSchedule.length > 0 ? `Sesi Aktif • ${activeDaySubject.room}` : 'Jadwal Belajar • Bebas Tatap Muka / Mandiri' }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-white/90 text-charcoal border border-white/60 shadow-2xs">
                                        {{ getDayLabel(selectedDay) }}, {{ selectedDay }} Okt
                                    </span>
                                </div>

                                <!-- Title & Subtitle -->
                                <div>
                                    <h3 class="text-[21px] sm:text-[22px] font-extrabold text-charcoal mt-2 leading-tight">
                                        {{ currentSchedule.length > 0 ? activeDaySubject.name : 'Tidak Ada Sesi Pelajaran Aktif' }}
                                    </h3>
                                    <p class="text-xs font-semibold text-charcoal/75 mt-0.5">
                                        {{ currentSchedule.length > 0
                                            ? `Pengampu: ${activeDaySubject.teacher} • Kode: ${activeDaySubject.code}`
                                            : `Hari ${getDayLabel(selectedDay)} tidak ada jadwal tatap muka • Manfaatkan waktu untuk belajar mandiri atau tugas projek`
                                        }}
                                    </p>
                                </div>

                                <!-- Stats Row (3 Kartu Konsisten) -->
                                <div class="flex items-center gap-3 mt-4">
                                    <div class="flex-1 bg-white/80 rounded-2xl px-3.5 py-2.5">
                                        <div class="text-[10px] font-bold text-charcoal/60 uppercase tracking-wider">
                                            {{ currentSchedule.length > 0 ? 'Waktu Sesi' : 'Status Hari' }}
                                        </div>
                                        <div class="text-xs sm:text-sm font-extrabold text-charcoal mt-0.5 truncate">
                                            {{ currentSchedule.length > 0 ? activeDaySubject.time : 'Bebas KBM Tatap Muka' }}
                                        </div>
                                    </div>
                                    <div class="flex-1 bg-white/80 rounded-2xl px-3.5 py-2.5">
                                        <div class="text-[10px] font-bold text-charcoal/60 uppercase tracking-wider">
                                            {{ currentSchedule.length > 0 ? 'Pertemuan' : 'Aktivitas Belajar' }}
                                        </div>
                                        <div class="text-xs sm:text-sm font-extrabold text-charcoal mt-0.5 truncate">
                                            {{ currentSchedule.length > 0 ? `Sesi ${activeDaySubject.completedMeetings}/${activeDaySubject.totalMeetings}` : 'Tugas Mandiri / Projek' }}
                                        </div>
                                    </div>
                                    <div class="flex-1 bg-white/80 rounded-2xl px-3.5 py-2.5">
                                        <div class="text-[10px] font-bold text-charcoal/60 uppercase tracking-wider">
                                            {{ currentSchedule.length > 0 ? 'Ruang Kelas' : 'Pendampingan' }}
                                        </div>
                                        <div class="text-xs sm:text-sm font-extrabold text-charcoal mt-0.5 truncate">
                                            {{ currentSchedule.length > 0 ? activeDaySubject.room : 'Online / Mandiri' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2 mt-4">
                                    <button
                                        type="button"
                                        @click="$emit('selectTab', 'mapel')"
                                        class="px-4 py-2.5 rounded-full bg-charcoal text-white text-xs font-extrabold shadow-sm active:scale-95 transition-transform cursor-pointer flex items-center gap-1.5"
                                    >
                                        <span>Buka Materi Pembelajaran</span>
                                        <LmsIcon name="chevronRight" className="w-3.5 h-3.5" />
                                    </button>
                                    <button
                                        type="button"
                                        @click="$emit('selectTab', 'tugas')"
                                        class="px-3.5 py-2.5 rounded-full bg-white/90 text-charcoal text-xs font-bold cursor-pointer hover:bg-white border border-white/60 transition-colors shadow-2xs"
                                    >
                                        Cek Tugas Terdekat
                                    </button>
                                </div>
                            </div>

                            <!-- Decorative Blobs -->
                            <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-white/15 pointer-events-none"></div>
                            <div class="absolute right-10 -bottom-10 w-16 h-16 rounded-full bg-white/10 pointer-events-none"></div>
                        </div>

                        <!-- 2. Capsule Calendar -->
                        <LmsCapsuleCalendar
                            title="Jadwal Pelajaran"
                            :days="days"
                            :selectedDay="selectedDay"
                            :events="calendarEvents"
                            @selectDay="(d, date) => $emit('selectDay', d, date)"
                        />

                        <!-- 3. Daily Timeline & Academic Calendar Notice (Konsisten & Tetap Berspace Saat Kosong) -->
                        <div class="mt-5">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-[15px] font-extrabold text-charcoal">
                                    Agenda Pelajaran — {{ getDayLabel(selectedDay) }}
                                </h2>
                                <span v-if="isCrucialScheduleEvent" class="text-[11px] font-extrabold px-2.5 py-0.5 rounded-full" :class="currentDayEvents.badgeColor">
                                    {{ currentDayEvents.name }}
                                </span>
                                <span v-else class="text-[11px] font-bold text-subtext">
                                    {{ currentSchedule.length > 0 ? `${currentSchedule.length} Mapel` : 'Jadwal Kosong' }}
                                </span>
                            </div>

                            <!-- Academic Event Banner (HANYA jika Hari Libur Resmi atau Pekan Ujian UTS/UAS) -->
                            <div
                                v-if="isCrucialScheduleEvent"
                                class="rounded-[22px] p-4 mb-3 border flex items-center gap-3.5"
                                :class="
                                    currentDayEvents.type === 'holiday' || currentDayEvents.type === 'leave'
                                        ? 'bg-rose-50/80 border-rose-200'
                                        : 'bg-purple-50/80 border-purple-200'
                                "
                            >
                                <div class="w-9 h-9 rounded-2xl flex items-center justify-center shrink-0" :class="currentDayEvents.type === 'holiday' || currentDayEvents.type === 'leave' ? 'bg-rose-100 text-rose-700' : 'bg-purple-100 text-purple-700'">
                                    <LmsIcon :name="currentDayEvents.type === 'holiday' || currentDayEvents.type === 'leave' ? 'bell' : 'task'" className="w-4 h-4" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-charcoal/70">
                                        {{ currentDayEvents.type === 'holiday' || currentDayEvents.type === 'leave' ? 'Hari Libur Nasional Resmi (SKB 3 Menteri)' : 'Agenda Ujian Kejuruan' }}
                                    </div>
                                    <h4 class="text-xs font-extrabold text-charcoal mt-0.5">{{ currentDayEvents.name }}</h4>
                                    <p class="text-[11px] text-subtext mt-0.5">{{ currentDayEvents.description }}</p>
                                </div>
                            </div>

                            <!-- Jika Ada Jadwal -->
                            <div v-if="currentSchedule.length > 0" class="space-y-2.5">
                                <div
                                    v-for="s in currentSchedule"
                                    :key="s.id"
                                    @click="$emit('openLessonSheet', s.name, s.name)"
                                    class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/25 active:scale-[0.99] transition-all flex items-center gap-3.5 group"
                                >
                                    <div class="w-10 h-10 rounded-2xl bg-charcoal/10 flex items-center justify-center shrink-0">
                                        <LmsIcon name="book" className="w-5 h-5 text-charcoal" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-extrabold text-charcoal group-hover:text-black truncate">{{ s.name }}</h4>
                                        <p class="text-[11px] text-subtext">{{ s.time }} • {{ s.room }} • {{ s.teacher }}</p>
                                    </div>
                                    <LmsIcon name="chevronRight" className="w-4 h-4 text-subtext shrink-0" />
                                </div>
                            </div>

                            <!-- Jika Tidak Ada Jadwal: Space yang setara & keterangan kosong yang rapi -->
                            <div
                                v-else
                                class="bg-white rounded-[24px] p-6 border border-[#ECEAE4] soft-shadow flex flex-col items-center justify-center min-h-[160px] text-center"
                            >
                                <div class="w-10 h-10 rounded-full bg-canvas border border-[#ECEAE4] flex items-center justify-center mb-2.5 text-subtext">
                                    <LmsIcon name="clock" className="w-4 h-4 text-subtext" />
                                </div>
                                <div class="text-xs font-bold text-charcoal">Tidak Ada Sesi Pelajaran Hari Ini</div>
                                <p class="text-[11px] text-subtext mt-0.5">Jadwal kosong pada hari {{ getDayLabel(selectedDay) }}. Silakan manfaatkan untuk belajar mandiri atau penyelesaian tugas.</p>
                            </div>
                        </div>

                        <!-- 4. Ringkasan & Rekapitulasi (Tugas & Nilai) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <!-- Tugas Terdekat -->
                            <div class="bg-white rounded-[28px] p-6 border border-[#ECEAE4] soft-shadow flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-8 h-8 rounded-full bg-blossom-light text-blossom-dark flex items-center justify-center">
                                                <LmsIcon name="task" className="w-4 h-4" />
                                            </span>
                                            <div>
                                                <h3 class="text-sm font-extrabold text-charcoal">Tugas Terdekat</h3>
                                                <p class="text-[11px] text-subtext mt-0.5">Tugas yang perlu segera diselesaikan</p>
                                            </div>
                                        </div>
                                        <button type="button" @click="$emit('selectTab', 'tugas')" class="text-xs font-bold text-blossom-dark hover:underline cursor-pointer flex items-center gap-1">
                                            <span>Lihat Semua</span>
                                            <span>→</span>
                                        </button>
                                    </div>
                                    <div v-if="assignments.length" class="space-y-3">
                                        <div
                                            v-for="a in assignments.slice(0,3)"
                                            :key="a.id"
                                            @click="$emit('openUploadModal', a.id)"
                                            class="bg-canvas rounded-[20px] p-4 border border-[#ECEAE4] cursor-pointer hover:border-charcoal/20 active:scale-[0.99] transition-all group flex items-center gap-3.5"
                                        >
                                            <div class="flex-1 min-w-0">
                                                <div class="text-[10px] font-bold text-subtext uppercase tracking-wider">{{ a.subject }}</div>
                                                <div class="text-xs font-extrabold text-charcoal group-hover:text-black truncate mt-0.5">{{ a.title }}</div>
                                                <div class="text-[11px] text-subtext mt-1 flex items-center gap-1.5">
                                                    <LmsIcon name="clock" className="w-3.5 h-3.5 text-subtext shrink-0" />
                                                    <span>Tenggat: {{ a.due }}</span>
                                                </div>
                                            </div>
                                            <span class="text-[10px] font-extrabold px-3 py-1.5 rounded-full shrink-0" :class="assignmentStatusColor(a.status)">
                                                {{ a.status }}
                                            </span>
                                        </div>
                                    </div>
                                    <div v-else class="text-center py-6 text-subtext text-xs">
                                        Belum ada tugas baru yang perlu dikerjakan 🎉
                                    </div>
                                </div>
                            </div>

                            <!-- Rekap Nilai Mapel -->
                            <div class="bg-white rounded-[28px] p-6 border border-[#ECEAE4] soft-shadow flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-8 h-8 rounded-full bg-sage-light text-sage-dark flex items-center justify-center">
                                                <LmsIcon name="chart" className="w-4 h-4" />
                                            </span>
                                            <div>
                                                <h3 class="text-sm font-extrabold text-charcoal">Rekap Nilai Mapel</h3>
                                                <p class="text-[11px] text-subtext mt-0.5">Rata-rata capaian akademik semester ini</p>
                                            </div>
                                        </div>
                                        <button type="button" @click="$emit('selectTab', 'mapel')" class="text-xs font-bold text-charcoal/70 hover:underline cursor-pointer flex items-center gap-1">
                                            <span>Buka Mapel</span>
                                            <span>→</span>
                                        </button>
                                    </div>
                                    <div class="space-y-3.5">
                                        <div
                                            v-for="s in subjectGrades"
                                            :key="s.mapel"
                                            class="bg-canvas/60 rounded-2xl p-3 border border-[#F2F0EC] flex items-center gap-3"
                                        >
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between mb-1.5">
                                                    <span class="text-xs font-extrabold text-charcoal truncate">{{ s.mapel }}</span>
                                                    <span class="text-[10px] font-bold text-subtext">KKM: {{ s.kkm }}</span>
                                                </div>
                                                <div class="w-full h-2 bg-muted rounded-full overflow-hidden">
                                                    <div
                                                        class="h-full rounded-full transition-all"
                                                        :class="s.avg >= s.kkm ? 'bg-charcoal' : 'bg-blossom-dark'"
                                                        :style="`width:${s.avg}%`"
                                                    ></div>
                                                </div>
                                            </div>
                                            <span
                                                class="text-sm font-black shrink-0 w-9 text-right"
                                                :class="s.avg >= s.kkm ? 'text-charcoal' : 'text-blossom-dark'"
                                            >
                                                {{ s.avg }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Agenda & Peringatan Nasional (Ditempatkan di bagian terpisah agar tidak mengganggu alur jadwal) -->
                        <div
                            v-if="isGeneralNationalEvent"
                            class="mt-6 bg-sky-50/70 rounded-[24px] p-4 sm:p-5 border border-sky-200 soft-shadow flex items-center gap-3.5"
                        >
                            <div class="w-10 h-10 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">
                                <LmsIcon name="book" className="w-5 h-5" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-sky-800">
                                    Agenda / Peringatan Nasional
                                </div>
                                <h4 class="text-xs sm:text-sm font-extrabold text-charcoal mt-0.5">{{ currentDayEvents.name }}</h4>
                                <p class="text-[11px] text-subtext mt-0.5">{{ currentDayEvents.description }}</p>
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
                @openSettings="$emit('openSettings')"
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
                    @openSettings="$emit('openSettings')"
                    @toggleSidebar="$emit('toggleSidebar')"
                />

                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
                    <div class="w-full max-w-[1800px] mx-auto">
                        <!-- SKELETON -->
                        <LmsSkeletonLoader v-if="isLoading" type="card-list" :rows="3" />
                        <!-- CONTENT -->
                        <div v-else-if="assignments.length" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-1">
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
                        <div v-else class="text-center py-12 text-subtext text-sm">
                            Tidak ada tugas yang perlu dikerjakan 🎉
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
.live-dot {
    animation: pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.7); }
}
</style>
