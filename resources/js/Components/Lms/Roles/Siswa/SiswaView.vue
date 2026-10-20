<script setup>
import LmsCapsuleCalendar from '@/Components/Lms/LmsCapsuleCalendar.vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';
import MapelSiswaView from './MapelSiswaView.vue';

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
});

const emit = defineEmits([
    'selectTab',
    'selectDay',
    'openAttendanceModal',
    'openLessonSheet',
    'openUploadModal',
    'openMenu',
    'openProfile',
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
                @openMenu="$emit('openMenu')"
                @openProfile="$emit('openProfile')"
            />

            <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 pb-28">
                <h2 class="text-[17px] font-extrabold text-charcoal mb-2.5">
                    Kelas Hari Ini
                </h2>

                <!-- Hero Card: Active Session -->
                <div class="hero-sage rounded-[28px] p-5 relative overflow-hidden soft-shadow">
                    <div class="relative z-10 w-full">
                        <div class="flex items-center gap-1.5 text-[11px] font-bold text-sage-dark">
                            <span class="w-2 h-2 rounded-full bg-[#2E7D32] live-dot"></span>
                            <span>Sesi Aktif • Lab RPL 2</span>
                        </div>
                        <h3 class="text-[21px] font-extrabold text-charcoal mt-1 leading-tight">
                            Pemrograman Web &amp; Perangkat
                        </h3>
                        <p class="text-xs font-semibold text-charcoal/75 mt-1">
                            Rombel: XII RPL 1 (Semester 5)
                        </p>

                        <!-- Step Progress Bar -->
                        <div class="mt-3.5">
                            <div class="text-[11px] font-bold text-charcoal mb-1">
                                Bab 06/10
                            </div>
                            <div class="w-full h-1.5 bg-white/70 rounded-full flex items-center">
                                <div class="w-3/5 h-1.5 bg-charcoal rounded-full relative">
                                    <span
                                        class="absolute -right-1.5 -top-1 w-3.5 h-3.5 rounded-full bg-charcoal border-2 border-white"
                                    ></span>
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

                <!-- Daily Timeline -->
                <div class="mt-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-[16px] font-extrabold text-charcoal">Agenda Harian</h3>
                        <span class="text-xs font-bold text-subtext">
                            {{ getDayLabel(selectedDay) }}, {{ selectedDay }} Okt
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="text-xs font-bold text-subtext w-11 pt-2.5">08:00</span>
                            <div class="flex-1 grid grid-cols-2 gap-2">
                                <div
                                    @click="$emit('selectTab', 'mapel')"
                                    class="cursor-pointer bg-white rounded-2xl p-2.5 border border-[#ECEAE4] soft-shadow flex items-center justify-between hover:border-charcoal/30 transition-all"
                                >
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-7 h-7 rounded-full bg-sage-light text-sage-dark flex items-center justify-center text-xs font-bold"
                                        >
                                            W
                                        </span>
                                        <div>
                                            <div class="text-xs font-extrabold text-charcoal">
                                                Web Dev
                                            </div>
                                            <div class="text-[10px] text-subtext">
                                                08:00–09:30
                                            </div>
                                        </div>
                                    </div>
                                    <LmsIcon name="arrowUpRight" className="w-3.5 h-3.5 text-subtext" />
                                </div>

                                <div
                                    @click="$emit('selectTab', 'mapel')"
                                    class="cursor-pointer bg-white rounded-2xl p-2.5 border border-[#ECEAE4] soft-shadow flex items-center justify-between hover:border-charcoal/30 transition-all"
                                >
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-7 h-7 rounded-full bg-blossom-light text-blossom-dark flex items-center justify-center text-xs font-bold"
                                        >
                                            M
                                        </span>
                                        <div>
                                            <div class="text-xs font-extrabold text-charcoal">
                                                Matematika
                                            </div>
                                            <div class="text-[10px] text-subtext">
                                                09:30–11:00
                                            </div>
                                        </div>
                                    </div>
                                    <LmsIcon name="arrowUpRight" className="w-3.5 h-3.5 text-subtext" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 border-t border-dashed border-[#E2DFD7] pt-3">
                            <span class="text-xs font-bold text-subtext w-11 pt-2.5">11:00</span>
                            <div class="flex-1">
                                <div
                                    @click="$emit('selectTab', 'tugas')"
                                    class="cursor-pointer bg-white rounded-2xl p-3 border border-[#ECEAE4] soft-shadow flex items-center justify-between hover:border-charcoal/30 transition-all"
                                >
                                    <div class="flex items-center gap-2.5">
                                        <span
                                            class="w-8 h-8 rounded-full bg-lavender-light text-lavender-dark flex items-center justify-center text-xs font-bold"
                                        >
                                            DB
                                        </span>
                                        <div>
                                            <div class="text-xs font-extrabold text-charcoal">
                                                Basis Data Praktik • Lab 1
                                            </div>
                                            <div class="text-[10px] text-subtext">
                                                11:00–13:30 • 1 Tugas Aktif
                                            </div>
                                        </div>
                                    </div>
                                    <span
                                        class="text-xs font-bold px-2.5 py-1 rounded-full bg-canvas text-charcoal border border-[#E6E4DF]"
                                    >
                                        Buka
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
                @openMenu="$emit('openMenu')"
                @openProfile="$emit('openProfile')"
            />

            <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 pb-28">
                <div class="space-y-3 mt-1">
                    <div
                        v-for="a in assignments"
                        :key="a.id"
                        class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow"
                    >
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
                        <h4 class="text-[15px] font-extrabold text-charcoal mt-2">
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

                        <!-- Action Submit / Revision Button -->
                        <div class="mt-3.5 flex items-center justify-end gap-2">
                            <button
                                v-if="a.allowRevision"
                                type="button"
                                @click="$emit('openUploadModal', a.id)"
                                class="px-4 py-2 rounded-full bg-charcoal text-white text-xs font-bold active:scale-95 transition-transform cursor-pointer"
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
                @openMenu="$emit('openMenu')"
                @openProfile="$emit('openProfile')"
            />

            <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 pb-28">
                <!-- Semester Attendance Macro -->
                <div class="hero-sky rounded-[28px] p-5 soft-shadow">
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

                <!-- Subject Average Transcript -->
                <h3 class="text-[15px] font-extrabold text-charcoal mt-6 mb-3">
                    Rekap Nilai Rata-Rata Mapel
                </h3>
                <div class="space-y-2.5">
                    <div
                        v-for="n in subjectGrades"
                        :key="n.mapel"
                        class="bg-white rounded-[22px] p-3.5 border border-[#ECEAE4] flex items-center justify-between shadow-2xs"
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
</style>
