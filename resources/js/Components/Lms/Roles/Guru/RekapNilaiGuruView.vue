<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import LmsSkeletonLoader from '@/Components/Lms/LmsSkeletonLoader.vue';
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';
import { guruMapelList } from './mapelGuruData';

const props = defineProps({
    role: { type: Object, required: true },
    isSidebarCollapsed: { type: Boolean, default: false },
});

defineEmits(['openMenu', 'openProfile', 'toggleSidebar']);

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

// ========= STATE =========
const selectedMapelId = ref(null);
watch(selectedMapelId, () => {
    triggerLoading(350);
});
const searchQuery = ref('');
const activeFilter = ref('semua'); // 'semua' | 'tuntas' | 'belum' | 'terlambat'

const selectedMapel = computed(() => guruMapelList.find(m => m.id === selectedMapelId.value) || null);

// ========= AGGREGATED STUDENT SCORES =========
// Collect all students across all meetings for a given mapel and compute avg
const buildStudentRekap = (mapel) => {
    if (!mapel) return [];

    // Collect unique students from all meetings
    const studentMap = {};

    mapel.meetings.forEach((meeting) => {
        meeting.students.forEach((s) => {
            if (!studentMap[s.id]) {
                studentMap[s.id] = {
                    id: s.id,
                    name: s.name,
                    nisn: s.nisn,
                    avatar: s.avatar,
                    scores: [],
                    submitted: 0,
                    notSubmitted: 0,
                };
            }
            if (s.score !== null) {
                studentMap[s.id].scores.push(s.score);
                studentMap[s.id].submitted++;
            } else if (s.submittedFile) {
                studentMap[s.id].submitted++;
            } else {
                studentMap[s.id].notSubmitted++;
            }
        });
    });

    return Object.values(studentMap).map((s) => {
        const avg = s.scores.length > 0 ? Math.round(s.scores.reduce((a, b) => a + b, 0) / s.scores.length) : null;
        const grade = avg === null ? '-' : avg >= 90 ? 'A' : avg >= 80 ? 'B' : avg >= 70 ? 'C' : avg >= 60 ? 'D' : 'E';
        const kkm = mapel.kkm || 75;
        const tuntas = avg !== null && avg >= kkm;
        return { ...s, avg, grade, kkm, tuntas, totalTugas: s.scores.length + (s.submitted - s.scores.length) + s.notSubmitted };
    }).sort((a, b) => (b.avg ?? -1) - (a.avg ?? -1));
};

const allStudents = computed(() => buildStudentRekap(selectedMapel.value));

const filteredStudents = computed(() => {
    let list = allStudents.value;

    if (searchQuery.value.trim()) {
        const q = searchQuery.value.toLowerCase();
        list = list.filter(s => s.name.toLowerCase().includes(q) || s.nisn?.includes(q));
    }

    if (activeFilter.value === 'tuntas') return list.filter(s => s.tuntas);
    if (activeFilter.value === 'belum') return list.filter(s => !s.tuntas);
    if (activeFilter.value === 'terlambat') return list.filter(s => s.notSubmitted > 0);
    return list;
});

// Summary stats per mapel card
const mapelStats = computed(() =>
    guruMapelList.map((m) => {
        const students = buildStudentRekap(m);
        const dinilai = students.filter(s => s.avg !== null).length;
        const tuntas = students.filter(s => s.tuntas).length;
        const avgKelas = students.length > 0 && dinilai > 0
            ? Math.round(students.filter(s => s.avg !== null).reduce((a, s) => a + s.avg, 0) / dinilai)
            : null;
        return { ...m, totalStudentRekap: students.length, dinilai, tuntas, avgKelas };
    })
);

// Grade ring color
const gradeRingColor = (grade) => {
    if (grade === 'A') return 'bg-sage-light text-sage-dark';
    if (grade === 'B') return 'bg-sky-light text-sky-dark';
    if (grade === 'C') return 'bg-[#FFF3E0] text-[#E65100]';
    if (grade === 'D' || grade === 'E') return 'bg-blossom-light text-blossom-dark';
    return 'bg-muted text-subtext';
};

const avgRingColor = (avg, kkm) => {
    if (avg === null) return 'bg-muted text-subtext';
    if (avg >= kkm + 10) return 'bg-sage-light text-sage-dark';
    if (avg >= kkm) return 'bg-sky-light text-sky-dark';
    return 'bg-blossom-light text-blossom-dark';
};

// Toast
const toastMessage = ref('');
let toastTimer = null;
const showToast = (msg) => {
    toastMessage.value = msg;
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { toastMessage.value = ''; }, 2800);
};
</script>

<template>
    <div class="flex-1 flex flex-col h-full overflow-hidden relative">

        <!-- Toast -->
        <transition
            enter-active-class="transform ease-out duration-200 transition"
            enter-from-class="translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="toastMessage"
                class="absolute top-14 left-1/2 -translate-x-1/2 z-50 bg-charcoal text-white text-xs font-semibold px-4 py-2 rounded-full shadow-lg border border-white/20 flex items-center gap-2 pointer-events-none"
            >
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span>{{ toastMessage }}</span>
            </div>
        </transition>

        <LmsTopBar
            :role="role"
            title="Rekap Nilai Mapel"
            :isSidebarCollapsed="isSidebarCollapsed"
            @toggleSidebar="$emit('toggleSidebar')"
            @openMenu="$emit('openMenu')"
            @openProfile="$emit('openProfile')"
        />

        <!-- ============================================================ -->
        <!-- LEVEL 1: PILIH MAPEL — RINGKASAN STATISTIK PER MAPEL         -->
        <!-- ============================================================ -->
        <template v-if="!selectedMapelId">
            <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
                <div class="w-full max-w-[1800px] mx-auto">
                    <LmsSkeletonLoader v-if="isLoading" type="mapel-list" :cards="3" />
                    <template v-else>

                    <!-- Header Banner -->
                    <div class="hero-blossom rounded-[28px] p-5 soft-shadow mb-5 relative overflow-hidden">
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="bg-white/90 text-charcoal px-3 py-1 rounded-full text-[11px] font-extrabold shadow-2xs">
                                    Semester Ganjil 2026/2027
                                </span>
                                <span class="text-[11px] font-bold text-blossom-dark">• {{ guruMapelList.length }} Mapel</span>
                            </div>
                            <h2 class="text-[19px] font-extrabold text-charcoal leading-tight">Rekap Nilai Siswa</h2>
                            <p class="text-xs text-charcoal/75 mt-1 max-w-[85%]">
                                Pilih mata pelajaran untuk melihat rekap nilai akhir, ketuntasan, dan rata-rata kelas per pertemuan.
                            </p>
                        </div>
                        <div class="absolute -right-6 -bottom-6 w-32 h-32 rounded-full bg-white/15 pointer-events-none"></div>
                    </div>

                    <!-- Mapel Summary Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        <div
                            v-for="m in mapelStats"
                            :key="m.id"
                            @click="selectedMapelId = m.id"
                            class="bg-white rounded-[26px] p-5 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all group"
                        >
                            <!-- Top: Badge & Progress -->
                            <div class="flex items-start justify-between mb-3">
                                <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full border" :class="m.badgeColor">
                                    {{ m.code }}
                                </span>
                                <span class="text-[11px] font-bold text-subtext">{{ m.completedMeetings }}/{{ m.totalMeetings }} Pertemuan</span>
                            </div>

                            <h3 class="text-[16px] font-extrabold text-charcoal leading-snug group-hover:text-black mb-1">
                                {{ m.name }}
                            </h3>
                            <p class="text-[11px] text-subtext mb-4">{{ m.rombel.join(' • ') }}</p>

                            <!-- Stat Grid -->
                            <div class="grid grid-cols-3 gap-2 mb-4">
                                <div class="bg-canvas rounded-2xl p-3 text-center border border-[#ECEAE4]">
                                    <div class="text-xl font-extrabold text-charcoal">{{ m.totalStudentRekap }}</div>
                                    <div class="text-[10px] font-bold text-subtext mt-0.5">Siswa</div>
                                </div>
                                <div class="bg-canvas rounded-2xl p-3 text-center border border-[#ECEAE4]">
                                    <div class="text-xl font-extrabold" :class="m.avgKelas !== null ? 'text-charcoal' : 'text-subtext'">
                                        {{ m.avgKelas ?? '–' }}
                                    </div>
                                    <div class="text-[10px] font-bold text-subtext mt-0.5">Rata-rata</div>
                                </div>
                                <div class="bg-canvas rounded-2xl p-3 text-center border border-[#ECEAE4]">
                                    <div class="text-xl font-extrabold text-[#2E7D32]">{{ m.tuntas }}</div>
                                    <div class="text-[10px] font-bold text-subtext mt-0.5">Tuntas</div>
                                </div>
                            </div>

                            <!-- Ketuntasan Bar -->
                            <div>
                                <div class="flex items-center justify-between text-[11px] font-bold text-charcoal mb-1">
                                    <span>Ketuntasan</span>
                                    <span>{{ m.totalStudentRekap > 0 ? Math.round((m.tuntas / m.totalStudentRekap) * 100) : 0 }}%</span>
                                </div>
                                <div class="w-full h-2 bg-muted rounded-full overflow-hidden">
                                    <div
                                        class="h-full rounded-full transition-all"
                                        :class="(m.totalStudentRekap > 0 && (m.tuntas / m.totalStudentRekap) >= 0.7) ? 'bg-[#4CAF50]' : 'bg-[#E65100]'"
                                        :style="`width:${m.totalStudentRekap > 0 ? (m.tuntas / m.totalStudentRekap) * 100 : 0}%`"
                                    ></div>
                                </div>
                                <div class="flex items-center justify-between mt-1.5">
                                    <span class="text-[10px] text-subtext">KKM: {{ m.kkm }}</span>
                                    <span class="text-[10px] font-bold" :class="(m.totalStudentRekap - m.tuntas) > 0 ? 'text-[#E65100]' : 'text-sage-dark'">
                                        {{ m.totalStudentRekap - m.tuntas }} Belum Tuntas
                                    </span>
                                </div>
                            </div>

                            <!-- Footer: CTA -->
                            <div class="mt-4 pt-4 border-t border-[#F2F0EC] flex items-center justify-between">
                                <span class="text-[11px] text-subtext">{{ m.dayTime }}</span>
                                <span class="text-xs font-extrabold text-charcoal group-hover:text-blossom-dark transition-colors flex items-center gap-1">
                                    Lihat Detail <LmsIcon name="chevronRight" className="w-3.5 h-3.5" />
                                </span>
                            </div>
                        </div>
                    </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- ============================================================ -->
        <!-- LEVEL 2: DETAIL REKAP NILAI SISWA PER MAPEL                  -->
        <!-- ============================================================ -->
        <template v-else>
            <!-- Header -->
            <header class="flex items-center gap-3 px-4 sm:px-6 lg:px-8 xl:px-10 pt-4 pb-3 shrink-0">
                <div class="w-full max-w-[1800px] mx-auto flex items-center gap-3">
                    <button
                        type="button"
                        @click="selectedMapelId = null; searchQuery = ''; activeFilter = 'semua'"
                        class="w-10 h-10 rounded-2xl bg-white border border-[#ECEAE4] flex items-center justify-center text-charcoal soft-shadow hover:bg-canvas active:scale-95 transition-all cursor-pointer shrink-0"
                    >
                        <LmsIcon name="back" className="w-4 h-4" />
                    </button>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-[16px] font-extrabold text-charcoal leading-tight line-clamp-1">
                            {{ selectedMapel?.shortName }}
                        </h1>
                        <span class="text-[11px] text-subtext font-medium">
                            {{ selectedMapel?.rombel.join(', ') }} • KKM {{ selectedMapel?.kkm }}
                        </span>
                    </div>
                    <!-- Export Button -->
                    <button
                        type="button"
                        @click="showToast('Ekspor rekap nilai ke Excel akan segera tersedia.')"
                        class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-2xl bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer shrink-0"
                    >
                        <LmsIcon name="download" className="w-3.5 h-3.5" />
                        Ekspor Excel
                    </button>
                </div>
            </header>

            <!-- Stats Summary Row -->
            <div class="px-4 sm:px-6 lg:px-8 xl:px-10 pb-3 shrink-0">
                <div class="w-full max-w-[1800px] mx-auto">
                    <div class="grid grid-cols-4 gap-2.5">
                        <div class="bg-white rounded-[20px] p-3 border border-[#ECEAE4] soft-shadow text-center">
                            <div class="text-xl font-extrabold text-charcoal">{{ allStudents.length }}</div>
                            <div class="text-[10px] font-bold text-subtext mt-0.5">Total Siswa</div>
                        </div>
                        <div class="bg-white rounded-[20px] p-3 border border-[#ECEAE4] soft-shadow text-center">
                            <div class="text-xl font-extrabold text-charcoal">
                                {{ allStudents.filter(s => s.avg !== null).length > 0
                                    ? Math.round(allStudents.filter(s => s.avg !== null).reduce((a,s) => a + s.avg, 0) / allStudents.filter(s => s.avg !== null).length)
                                    : '–' }}
                            </div>
                            <div class="text-[10px] font-bold text-subtext mt-0.5">Rata-rata</div>
                        </div>
                        <div class="bg-[#E8F5E9] rounded-[20px] p-3 border border-[#C8DFCD] text-center">
                            <div class="text-xl font-extrabold text-[#2E7D32]">{{ allStudents.filter(s => s.tuntas).length }}</div>
                            <div class="text-[10px] font-bold text-[#2E7D32]/70 mt-0.5">Tuntas</div>
                        </div>
                        <div class="bg-blossom-light rounded-[20px] p-3 border border-[#F3C6C6] text-center">
                            <div class="text-xl font-extrabold text-blossom-dark">{{ allStudents.filter(s => !s.tuntas).length }}</div>
                            <div class="text-[10px] font-bold text-blossom-dark/70 mt-0.5">Belum Tuntas</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search & Filter -->
            <div class="px-4 sm:px-6 lg:px-8 xl:px-10 pb-3 shrink-0">
                <div class="w-full max-w-[1800px] mx-auto flex flex-col sm:flex-row gap-2.5">
                    <!-- Search -->
                    <div class="relative flex-1">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-subtext" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7" /><path d="M21 21l-4.35-4.35" />
                        </svg>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari nama atau NISN siswa..."
                            class="w-full bg-white border border-[#ECEAE4] rounded-2xl pl-10 pr-4 py-2.5 text-xs font-medium text-charcoal placeholder:text-subtext/70 focus:outline-none focus:border-charcoal/40 transition-colors"
                        />
                    </div>
                    <!-- Filter Chips -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button
                            v-for="f in [{ id: 'semua', label: 'Semua' }, { id: 'tuntas', label: 'Tuntas' }, { id: 'belum', label: 'Belum Tuntas' }, { id: 'terlambat', label: 'Ada Kosong' }]"
                            :key="f.id"
                            type="button"
                            @click="activeFilter = f.id"
                            class="px-3 py-1.5 rounded-full text-[11px] font-bold transition-all cursor-pointer"
                            :class="activeFilter === f.id ? 'bg-charcoal text-white' : 'bg-white border border-[#ECEAE4] text-charcoal/70 hover:text-charcoal'"
                        >
                            {{ f.label }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Student Rekap List -->
            <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
                <div class="w-full max-w-[1800px] mx-auto">
                    <LmsSkeletonLoader v-if="isLoading" type="rekap-list" :rows="6" />
                    <template v-else>

                    <!-- Column Headers (Desktop) -->
                    <div class="hidden lg:grid lg:grid-cols-12 gap-3 px-4 py-2 mb-2">
                        <div class="col-span-4 text-[10px] font-bold text-subtext uppercase tracking-wider">Nama Siswa</div>
                        <div class="col-span-2 text-[10px] font-bold text-subtext uppercase tracking-wider text-center">Tugas Dikumpul</div>
                        <div class="col-span-2 text-[10px] font-bold text-subtext uppercase tracking-wider text-center">Kosong</div>
                        <div class="col-span-2 text-[10px] font-bold text-subtext uppercase tracking-wider text-center">Rata-rata</div>
                        <div class="col-span-1 text-[10px] font-bold text-subtext uppercase tracking-wider text-center">Grade</div>
                        <div class="col-span-1 text-[10px] font-bold text-subtext uppercase tracking-wider text-center">Status</div>
                    </div>

                    <div class="space-y-2.5">
                        <div
                            v-for="(student, idx) in filteredStudents"
                            :key="student.id"
                            class="bg-white rounded-[22px] p-3.5 border border-[#ECEAE4] soft-shadow transition-all hover:border-charcoal/20"
                        >
                            <!-- Mobile Layout -->
                            <div class="flex items-center gap-3 lg:hidden">
                                <!-- Rank & Avatar -->
                                <div class="relative shrink-0">
                                    <img :src="student.avatar" :alt="student.name" class="w-10 h-10 rounded-full object-cover border border-[#E6E4DF]" />
                                    <span class="absolute -top-1 -left-1 w-4 h-4 rounded-full bg-charcoal text-white text-[8px] font-extrabold flex items-center justify-center">
                                        {{ idx + 1 }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-extrabold text-charcoal truncate">{{ student.name }}</div>
                                    <div class="text-[10px] text-subtext mt-0.5">NISN: {{ student.nisn }}</div>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="text-[10px] font-semibold text-subtext">{{ student.submitted }} kumpul</span>
                                        <span v-if="student.notSubmitted > 0" class="text-[10px] font-bold text-[#E65100]">{{ student.notSubmitted }} kosong</span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-center gap-1 shrink-0">
                                    <div
                                        class="w-12 h-12 rounded-full flex items-center justify-center font-extrabold text-sm"
                                        :class="avgRingColor(student.avg, student.kkm)"
                                    >
                                        {{ student.avg ?? '–' }}
                                    </div>
                                    <span class="text-[10px] font-bold" :class="student.tuntas ? 'text-[#2E7D32]' : student.avg === null ? 'text-subtext' : 'text-[#E65100]'">
                                        {{ student.avg === null ? 'Belum' : student.tuntas ? '✓ Tuntas' : '✗ Remedi' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Desktop Layout -->
                            <div class="hidden lg:grid lg:grid-cols-12 gap-3 items-center">
                                <!-- Name & NISN (4 cols) -->
                                <div class="col-span-4 flex items-center gap-3">
                                    <div class="relative shrink-0">
                                        <img :src="student.avatar" :alt="student.name" class="w-9 h-9 rounded-full object-cover border border-[#E6E4DF]" />
                                        <span class="absolute -top-1 -left-1 w-4 h-4 rounded-full bg-charcoal text-white text-[8px] font-extrabold flex items-center justify-center">
                                            {{ idx + 1 }}
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-extrabold text-charcoal truncate">{{ student.name }}</div>
                                        <div class="text-[10px] text-subtext">{{ student.nisn }}</div>
                                    </div>
                                </div>

                                <!-- Tugas Dikumpul (2 cols) -->
                                <div class="col-span-2 text-center">
                                    <span class="text-sm font-extrabold text-charcoal">{{ student.submitted }}</span>
                                    <span class="text-[10px] text-subtext"> tugas</span>
                                </div>

                                <!-- Kosong (2 cols) -->
                                <div class="col-span-2 text-center">
                                    <span
                                        class="text-sm font-extrabold"
                                        :class="student.notSubmitted > 0 ? 'text-[#E65100]' : 'text-sage-dark'"
                                    >
                                        {{ student.notSubmitted }}
                                    </span>
                                    <span class="text-[10px] text-subtext"> tugas</span>
                                </div>

                                <!-- Rata-rata (2 cols) -->
                                <div class="col-span-2 flex items-center justify-center">
                                    <div
                                        class="w-12 h-12 rounded-full flex items-center justify-center font-extrabold text-sm"
                                        :class="avgRingColor(student.avg, student.kkm)"
                                    >
                                        {{ student.avg ?? '–' }}
                                    </div>
                                </div>

                                <!-- Grade (1 col) -->
                                <div class="col-span-1 flex justify-center">
                                    <span
                                        class="w-8 h-8 rounded-full flex items-center justify-center font-extrabold text-xs"
                                        :class="gradeRingColor(student.grade)"
                                    >
                                        {{ student.grade }}
                                    </span>
                                </div>

                                <!-- Status (1 col) -->
                                <div class="col-span-1 flex justify-center">
                                    <span
                                        class="px-2 py-1 rounded-full text-[10px] font-extrabold"
                                        :class="student.avg === null ? 'bg-muted text-subtext' : student.tuntas ? 'bg-[#E8F5E9] text-[#2E7D32]' : 'bg-blossom-light text-blossom-dark'"
                                    >
                                        {{ student.avg === null ? 'Belum' : student.tuntas ? 'Tuntas' : 'Remedi' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div
                        v-if="filteredStudents.length === 0"
                        class="text-center py-12 text-subtext"
                    >
                        <div class="w-14 h-14 rounded-full bg-muted flex items-center justify-center mx-auto mb-3">
                            <LmsIcon name="user" className="w-6 h-6 text-subtext" />
                        </div>
                        <p class="text-sm font-bold text-charcoal">Tidak ada siswa ditemukan</p>
                        <p class="text-xs mt-1">Coba ubah filter atau kata kunci pencarian.</p>
                    </div>

                    <!-- Per-Pertemuan Breakdown -->
                    <div class="mt-6">
                        <h3 class="text-[14px] font-extrabold text-charcoal mb-3">Rekap per Pertemuan</h3>
                        <div class="space-y-2.5">
                            <div
                                v-for="meeting in selectedMapel?.meetings"
                                :key="meeting.id"
                                class="bg-white rounded-[22px] p-4 border border-[#ECEAE4] soft-shadow"
                            >
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <div class="flex items-center gap-2 mb-0.5">
                                            <span
                                                class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                                :class="meeting.status === 'Selesai' ? 'bg-sage-light text-sage-dark' : meeting.status === 'Sedang Berlangsung' ? 'bg-sky-light text-sky-dark' : 'bg-muted text-subtext'"
                                            >
                                                {{ meeting.status }}
                                            </span>
                                            <span v-if="meeting.tugas.ungradedCount > 0" class="text-[10px] font-extrabold text-[#E65100]">
                                                • {{ meeting.tugas.ungradedCount }} Belum Dinilai
                                            </span>
                                        </div>
                                        <h4 class="text-xs font-extrabold text-charcoal">Pertemuan {{ meeting.number }}: {{ meeting.title }}</h4>
                                        <p class="text-[11px] text-subtext mt-0.5">{{ meeting.date }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-extrabold text-charcoal">{{ meeting.tugas.totalSubmitted }}/{{ meeting.tugas.totalStudents }}</div>
                                        <div class="text-[10px] text-subtext">Mengumpulkan</div>
                                    </div>
                                </div>

                                <!-- Mini student score bar -->
                                <div class="flex items-center gap-1 flex-wrap">
                                    <div
                                        v-for="student in meeting.students"
                                        :key="student.id"
                                        class="flex flex-col items-center gap-0.5 cursor-default group relative"
                                        :title="`${student.name}: ${student.score ?? 'Belum Dinilai'}`"
                                    >
                                        <div
                                            class="w-7 h-7 rounded-full flex items-center justify-center text-[9px] font-extrabold"
                                            :class="student.score !== null ? avgRingColor(student.score, selectedMapel?.kkm) : student.submittedFile ? 'bg-sky-light text-sky-dark' : 'bg-muted text-subtext'"
                                        >
                                            {{ student.score ?? (student.submittedFile ? '?' : '–') }}
                                        </div>
                                    </div>
                                    <div v-if="meeting.students.length === 0" class="text-[11px] text-subtext italic">Belum ada data siswa</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </template>
                </div>
            </div>
        </template>
    </div>
</template>
