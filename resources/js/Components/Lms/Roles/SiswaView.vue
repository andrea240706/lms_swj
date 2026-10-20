<script setup>
import LmsCapsuleCalendar from '@/Components/Lms/LmsCapsuleCalendar.vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
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
    selectedDay: {
        type: Number,
        default: 17,
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

const lessons = [
    {
        id: 1,
        title: 'Arsitektur MVC & Routing',
        subject: 'Pemrograman Web',
        date: '16 Okt',
        time: '08:00–09:30',
        color: 'bg-sky-light text-sky-dark',
    },
    {
        id: 2,
        title: 'Eloquent ORM & Migration',
        subject: 'Pemrograman Web',
        date: '17 Okt',
        time: '08:00–09:30',
        color: 'bg-sage-light text-sage-dark',
    },
    {
        id: 3,
        title: 'Middleware & Autentikasi Role',
        subject: 'Pemrograman Web',
        date: '20 Okt',
        time: '08:00–09:30',
        color: 'bg-lavender-light text-lavender-dark',
    },
    {
        id: 4,
        title: 'Deployment Server Produksi',
        subject: 'Pemrograman Web',
        date: '24 Okt',
        time: '08:00–09:30',
        color: 'bg-blossom-light text-blossom-dark',
    },
];

const subjectGrades = [
    { mapel: 'Pemrograman Web & Perangkat Bergerak', avg: 91, kkm: 78 },
    { mapel: 'Basis Data Rombel XII', avg: 88, kkm: 78 },
    { mapel: 'Infrastruktur Jaringan', avg: 84, kkm: 75 },
    { mapel: 'Bahasa Inggris Vokasi', avg: 89, kkm: 75 },
];
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- ========================================== -->
        <!-- TAB 1: HOME (SESI AKTIF & TIMELINE)       -->
        <!-- ========================================== -->
        <template v-if="activeTab === 'home'">
            <LmsTopBar
                :role="role"
                @openMenu="$emit('openMenu')"
                @openProfile="$emit('openProfile')"
            />

            <div class="flex-1 overflow-y-auto no-scrollbar px-6 pb-28">
                <h2 class="text-[17px] font-extrabold text-charcoal mb-2.5">
                    Today Class
                </h2>

                <!-- Hero Card: Active Session -->
                <div class="hero-sage rounded-[28px] p-5 relative overflow-hidden soft-shadow">
                    <div class="relative z-10 max-w-[68%]">
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

                        <!-- Action Play Button + Quick Presensi Pill -->
                        <div class="mt-4 flex items-center gap-2.5">
                            <button
                                type="button"
                                @click="$emit('selectTab', 'materi')"
                                class="w-11 h-11 rounded-full bg-charcoal text-white flex items-center justify-center shadow-md active:scale-95 transition-transform cursor-pointer"
                                title="Buka Materi"
                            >
                                <LmsIcon name="play" className="w-4 h-4 ml-0.5" />
                            </button>
                            <button
                                type="button"
                                @click="$emit('openAttendanceModal')"
                                class="px-3.5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer shadow-xs"
                                :class="
                                    studentAttendanceDone
                                        ? 'bg-white/90 text-[#2E7D32]'
                                        : 'bg-charcoal text-white'
                                "
                            >
                                {{ studentAttendanceDone ? '✓ Sudah Presensi' : 'Isi Presensi Sekarang' }}
                            </button>
                        </div>
                    </div>

                    <!-- 3D Style Decorative Vector Cluster (Bottom Right) -->
                    <div
                        class="absolute -right-3 -bottom-3 w-32 h-32 rounded-full bg-white/35 flex items-center justify-center pointer-events-none"
                    >
                        <div
                            class="w-20 h-20 rounded-2xl bg-white/80 backdrop-blur-xs shadow-sm rotate-6 flex flex-col items-center justify-center p-2 border border-white"
                        >
                            <span
                                class="text-[10px] font-extrabold text-charcoal bg-sage px-2 py-0.5 rounded-full"
                            >
                                LARAVEL
                            </span>
                            <span class="text-lg font-extrabold text-charcoal mt-1">&lt;/&gt;</span>
                            <span class="text-[9px] font-bold text-subtext">Bab 06 Aktif</span>
                        </div>
                    </div>
                </div>

                <!-- Capsule Weekly Schedule -->
                <LmsCapsuleCalendar
                    title="Class Schedule"
                    :days="days"
                    :selectedDay="selectedDay"
                    @selectDay="(d) => $emit('selectDay', d)"
                />

                <!-- Daily Timeline -->
                <div class="mt-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-[16px] font-extrabold text-charcoal">Timeline</h3>
                        <span class="text-xs font-bold text-subtext">
                            Selasa, {{ selectedDay }} Okt
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="text-xs font-bold text-subtext w-11 pt-2.5">08:00</span>
                            <div class="flex-1 grid grid-cols-2 gap-2">
                                <div
                                    @click="$emit('selectTab', 'materi')"
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
                                    @click="$emit('selectTab', 'materi')"
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
        </template>

        <!-- ========================================== -->
        <!-- TAB 2: MATERI (SPOTLIGHT & DAFTAR BAB)    -->
        <!-- ========================================== -->
        <template v-else-if="activeTab === 'materi'">
            <LmsTopBar
                :role="role"
                title="Today's Class"
                @openMenu="$emit('openMenu')"
                @openProfile="$emit('openProfile')"
            />

            <div class="flex-1 overflow-y-auto no-scrollbar px-6 pb-28">
                <!-- Pastel Pink Spotlight Card -->
                <div class="hero-blossom rounded-[28px] p-5 relative overflow-hidden soft-shadow">
                    <div
                        class="inline-flex items-center gap-2 bg-white rounded-full px-3 py-1.5 shadow-2xs"
                    >
                        <span
                            class="w-5 h-5 rounded-full bg-blossom flex items-center justify-center text-charcoal"
                        >
                            <LmsIcon name="check" className="w-3 h-3" />
                        </span>
                        <span class="text-xs font-extrabold text-charcoal">08:00 - 10:30 WIB</span>
                    </div>

                    <h3 class="text-[21px] font-extrabold text-charcoal mt-3.5 leading-tight max-w-[70%]">
                        Rekayasa Web Modern
                    </h3>

                    <div class="flex items-center gap-3 mt-2 text-xs font-semibold text-charcoal/80">
                        <span>📅 20 Okt</span>
                        <span>•</span>
                        <span>🏷️ Praktik Intensif</span>
                    </div>

                    <div class="flex items-center gap-2.5 mt-4">
                        <img
                            src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80"
                            class="w-8 h-8 rounded-full object-cover border border-white"
                            alt="Guru"
                        />
                        <div>
                            <div class="text-xs font-extrabold text-charcoal leading-none">
                                Budi Santoso, S.Kom
                            </div>
                            <div class="text-[10px] font-medium text-charcoal/70 mt-0.5">
                                Guru Pengampu RPL
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Last Lessons Capsule List -->
                <div class="flex items-center justify-between mt-6 mb-3">
                    <h3 class="text-[16px] font-extrabold text-charcoal">Daftar Bab &amp; Modul</h3>
                    <span class="text-xs font-bold text-subtext">4 Bab</span>
                </div>

                <div class="space-y-2.5">
                    <div
                        v-for="l in lessons"
                        :key="l.id"
                        @click="!materialsLock[l.id] && $emit('openLessonSheet', l.title, l.subject)"
                        class="bg-white rounded-[24px] p-3.5 border border-[#ECEAE4] soft-shadow flex items-center justify-between transition-all"
                        :class="
                            materialsLock[l.id]
                                ? 'opacity-60 cursor-not-allowed'
                                : 'cursor-pointer active:scale-[0.99] hover:border-charcoal/30'
                        "
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 rounded-full flex items-center justify-center shrink-0"
                                :class="l.color"
                            >
                                <LmsIcon
                                    :name="materialsLock[l.id] ? 'lock' : 'book'"
                                    className="w-5 h-5"
                                />
                            </div>
                            <div>
                                <h4 class="text-sm font-extrabold text-charcoal leading-snug">
                                    {{ l.title }}
                                </h4>
                                <div class="text-[11px] font-medium text-subtext mt-0.5">
                                    {{ l.subject }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right shrink-0 ml-2">
                            <span
                                class="inline-block text-[10px] font-bold px-2.5 py-1 rounded-full"
                                :class="
                                    materialsLock[l.id]
                                        ? 'bg-muted text-subtext'
                                        : 'bg-canvas text-charcoal border border-[#E6E4DF]'
                                "
                            >
                                {{ materialsLock[l.id] ? 'Terkunci' : 'PDF • Video' }}
                            </span>
                            <div class="text-[10px] text-subtext mt-1">{{ l.date }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ========================================== -->
        <!-- TAB 3: TUGAS & AKTIVITAS                  -->
        <!-- ========================================== -->
        <template v-else-if="activeTab === 'tugas'">
            <LmsTopBar
                :role="role"
                title="Tugas & Aktivitas"
                @openMenu="$emit('openMenu')"
                @openProfile="$emit('openProfile')"
            />

            <div class="flex-1 overflow-y-auto no-scrollbar px-6 pb-28">
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
        </template>

        <!-- ========================================== -->
        <!-- TAB 4: PRESENSI & REKAP NILAI             -->
        <!-- ========================================== -->
        <template v-else-if="activeTab === 'presensi'">
            <LmsTopBar
                :role="role"
                title="Presensi & Transkrip"
                @openMenu="$emit('openMenu')"
                @openProfile="$emit('openProfile')"
            />

            <div class="flex-1 overflow-y-auto no-scrollbar px-6 pb-28">
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
        </template>
    </div>
</template>
