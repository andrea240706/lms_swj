<script setup>
import { computed, ref } from "vue";
import LmsIcon from "@/Components/Lms/LmsIcon.vue";
import LmsTopBar from "@/Components/Lms/LmsTopBar.vue";
import { guruMapelList } from "./mapelGuruData";

const props = defineProps({
    role: { type: Object, required: true },
    isSidebarCollapsed: { type: Boolean, default: false },
});

const emit = defineEmits(["openMenu", "openProfile", "toggleSidebar"]);

// Reactive clone of data
const mapelList = ref(JSON.parse(JSON.stringify(guruMapelList)));

// Navigation state: 'list' | 'mapel_detail' | 'meeting_detail'
const currentView = ref("list");
const selectedMapelId = ref(null);
const selectedMeetingId = ref(null);

// Active sub-tab in meeting detail: 'tugas' | 'materi' | 'video'
const activeMeetingTab = ref("tugas");

// Grading modal state
const isGradingOpen = ref(false);
const gradingStudent = ref(null);
const gradingScore = ref(null);
const gradingFeedback = ref("");

// Toast
const toastMessage = ref("");
let toastTimer = null;
const showToast = (msg) => {
    toastMessage.value = msg;
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { toastMessage.value = ""; }, 2800);
};

// Computed
const selectedMapel = computed(() =>
    mapelList.value.find((m) => m.id === selectedMapelId.value) || null
);
const selectedMeeting = computed(() => {
    if (!selectedMapel.value) return null;
    return selectedMapel.value.meetings.find((mt) => mt.id === selectedMeetingId.value) || null;
});

const ungradedStudents = computed(() => {
    if (!selectedMeeting.value) return [];
    return selectedMeeting.value.students.filter((s) => s.submittedFile && s.score === null);
});

const gradedStudents = computed(() => {
    if (!selectedMeeting.value) return [];
    return selectedMeeting.value.students.filter((s) => s.score !== null);
});

const notSubmittedStudents = computed(() => {
    if (!selectedMeeting.value) return [];
    return selectedMeeting.value.students.filter((s) => !s.submittedFile);
});

// Navigation
const openMapel = (mapelId) => {
    selectedMapelId.value = mapelId;
    currentView.value = "mapel_detail";
};
const openMeeting = (meetingId) => {
    selectedMeetingId.value = meetingId;
    activeMeetingTab.value = "tugas";
    currentView.value = "meeting_detail";
};
const backToList = () => {
    currentView.value = "list";
    selectedMapelId.value = null;
    selectedMeetingId.value = null;
};
const backToMapelDetail = () => {
    currentView.value = "mapel_detail";
    selectedMeetingId.value = null;
};

// Toggle lock for a meeting
const toggleLock = (meetingId) => {
    const meeting = selectedMapel.value?.meetings.find((m) => m.id === meetingId);
    if (meeting) {
        meeting.isLocked = !meeting.isLocked;
        showToast(meeting.isLocked ? "Pertemuan dikunci untuk siswa." : "Pertemuan dibuka untuk siswa.");
    }
};

// Grading
const openGradeModal = (student) => {
    gradingStudent.value = student;
    gradingScore.value = student.score ?? null;
    gradingFeedback.value = student.feedback ?? "";
    isGradingOpen.value = true;
};
const saveGrade = () => {
    if (!gradingStudent.value || gradingScore.value === null) return;
    const score = Number(gradingScore.value);
    if (isNaN(score) || score < 0 || score > 100) {
        showToast("Nilai harus antara 0 - 100.");
        return;
    }
    gradingStudent.value.score = score;
    gradingStudent.value.grade = score >= 90 ? "A" : score >= 80 ? "B" : score >= 70 ? "C" : score >= 60 ? "D" : "E";
    gradingStudent.value.feedback = gradingFeedback.value;
    gradingStudent.value.gradedAt = new Date().toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });

    // Update tugas ungradedCount on meeting
    const meeting = selectedMeeting.value;
    if (meeting) {
        meeting.tugas.ungradedCount = meeting.students.filter((s) => s.submittedFile && s.score === null).length;
    }

    showToast(`Nilai ${gradingStudent.value.name}: ${score} berhasil disimpan!`);
    isGradingOpen.value = false;
    gradingStudent.value = null;
};

const openVideo = (url) => {
    if (url) window.open(url, "_blank", "noopener,noreferrer");
};

const handleDownload = (fileName) => showToast(`Mengunduh: ${fileName}`);

const statusColor = (status) => {
    if (status === "Selesai") return "bg-sage-light text-sage-dark";
    if (status === "Sedang Berlangsung") return "bg-sky-light text-sky-dark";
    return "bg-muted text-subtext";
};

const submissionStatusColor = (status) => {
    if (status === "Tepat Waktu") return "text-[#2E7D32]";
    if (status === "Terlambat") return "text-[#E65100]";
    return "text-subtext";
};

const gradeFromScore = (score) => {
    if (score === null || score === "") return "-";
    const n = Number(score);
    return n >= 90 ? "A" : n >= 80 ? "B" : n >= 70 ? "C" : n >= 60 ? "D" : "E";
};
</script>

<template>
    <div class="flex-1 flex flex-col h-full overflow-hidden relative">
        <!-- Toast Notification -->
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

        <!-- ============================================ -->
        <!-- LEVEL 1: DAFTAR MAPEL YANG DIAJAR           -->
        <!-- ============================================ -->
        <template v-if="currentView === 'list'">
            <div class="flex-1 flex flex-col h-full overflow-hidden">
                <LmsTopBar
                    :role="role"
                    title="Mata Pelajaran Ampu"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    @toggleSidebar="$emit('toggleSidebar')"
                    @openMenu="$emit('openMenu')"
                    @openProfile="$emit('openProfile')"
                    class="shrink-0"
                />
                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 pb-28 lg:pb-8">
                    <!-- Header Banner -->
                    <div class="hero-blossom rounded-[26px] p-5 soft-shadow mb-4 relative overflow-hidden">
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="inline-block bg-white/90 text-charcoal px-3 py-1 rounded-full text-[11px] font-extrabold shadow-2xs">
                                    Semester Ganjil 2026/2027
                                </span>
                                <span class="text-[11px] font-bold text-blossom-dark">
                                    • {{ mapelList.length }} Mapel Diampu
                                </span>
                            </div>
                            <h2 class="text-[19px] font-extrabold text-charcoal leading-tight">Kelola Mapel & Penilaian</h2>
                            <p class="text-xs text-charcoal/75 mt-1 max-w-[80%]">
                                Pilih mata pelajaran untuk mengelola materi, tugas, kunci bab, dan nilai siswa per pertemuan.
                            </p>
                        </div>
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 rounded-full bg-white/20 pointer-events-none"></div>
                    </div>

                    <!-- Mapel Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            v-for="mapel in mapelList"
                            :key="mapel.id"
                            @click="openMapel(mapel.id)"
                            class="bg-white rounded-[26px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all relative overflow-hidden group flex flex-col"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full border" :class="mapel.badgeColor">
                                    {{ mapel.code }} • {{ mapel.category }}
                                </span>
                                <span class="text-[11px] font-bold text-subtext">
                                    {{ mapel.completedMeetings }}/{{ mapel.totalMeetings }} Pertemuan
                                </span>
                            </div>
                            <h3 class="text-[16px] font-extrabold text-charcoal leading-snug group-hover:text-black mb-1">
                                {{ mapel.name }}
                            </h3>
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                <span
                                    v-for="r in mapel.rombel"
                                    :key="r"
                                    class="px-2 py-0.5 rounded-full bg-canvas text-[10px] font-bold text-charcoal border border-[#ECEAE4]"
                                >
                                    {{ r }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-[#F2F0EC] mt-auto">
                                <div class="text-[11px] text-subtext font-medium">
                                    {{ mapel.dayTime }}<br/>
                                    <span class="font-bold text-charcoal/70">{{ mapel.room }}</span>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-20 h-1.5 bg-muted rounded-full overflow-hidden">
                                            <div
                                                class="h-full bg-charcoal rounded-full transition-all"
                                                :style="`width:${(mapel.completedMeetings/mapel.totalMeetings)*100}%`"
                                            ></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-charcoal">
                                            {{ Math.round((mapel.completedMeetings/mapel.totalMeetings)*100) }}%
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-subtext">{{ mapel.totalStudents }} Siswa</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ============================================ -->
        <!-- LEVEL 2: DAFTAR PERTEMUAN DALAM MAPEL       -->
        <!-- ============================================ -->
        <template v-else-if="currentView === 'mapel_detail'">
            <div class="flex-1 flex flex-col h-full overflow-hidden">
                <header class="flex items-center justify-between px-4 sm:px-6 lg:px-8 pt-4 pb-3 shrink-0">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            @click="backToList"
                            class="w-10 h-10 rounded-2xl bg-white border border-[#ECEAE4] flex items-center justify-center text-charcoal soft-shadow hover:bg-canvas active:scale-95 transition-all cursor-pointer shrink-0"
                        >
                            <LmsIcon name="back" className="w-4 h-4" />
                        </button>
                        <div>
                            <h1 class="text-[16px] font-extrabold text-charcoal leading-tight line-clamp-1">
                                {{ selectedMapel?.shortName }}
                            </h1>
                            <span class="text-[11px] text-subtext font-medium">
                                {{ selectedMapel?.rombel.join(", ") }} • {{ selectedMapel?.room }}
                            </span>
                        </div>
                    </div>
                    <div class="hidden sm:flex items-center gap-2">
                        <span class="text-[11px] font-bold text-subtext bg-white border border-[#ECEAE4] px-3 py-1.5 rounded-xl">
                            KKM: {{ selectedMapel?.kkm }}
                        </span>
                        <span class="text-[11px] font-bold text-charcoal bg-white border border-[#ECEAE4] px-3 py-1.5 rounded-xl">
                            {{ selectedMapel?.totalStudents }} Siswa
                        </span>
                    </div>
                </header>

                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 pb-28 lg:pb-8">
                    <!-- Pending Alert -->
                    <div
                        v-if="selectedMapel?.meetings.some(m => m.tugas.ungradedCount > 0)"
                        class="bg-[#FFF3E0] border border-[#FFB74D] rounded-2xl p-3.5 mb-4 flex items-start gap-3"
                    >
                        <span class="text-lg mt-0.5">⚠️</span>
                        <div>
                            <div class="text-xs font-extrabold text-[#E65100]">Tugas Menunggu Penilaian</div>
                            <div class="text-[11px] text-[#BF360C] mt-0.5">
                                Ada {{ selectedMapel.meetings.reduce((a,m)=>a+m.tugas.ungradedCount,0) }} pengumpulan yang belum dinilai.
                            </div>
                        </div>
                    </div>

                    <h3 class="text-[14px] font-extrabold text-charcoal mb-3">Daftar Pertemuan</h3>
                    <div class="space-y-3">
                        <div
                            v-for="meeting in selectedMapel?.meetings"
                            :key="meeting.id"
                            @click="openMeeting(meeting.id)"
                            class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all flex items-center gap-4"
                        >
                            <div
                                class="w-11 h-11 rounded-full flex items-center justify-center font-black text-sm shrink-0"
                                :class="meeting.isLocked ? 'bg-muted text-subtext' : 'bg-charcoal text-white'"
                            >
                                {{ meeting.isLocked ? '🔒' : meeting.number }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" :class="statusColor(meeting.status)">
                                        {{ meeting.status }}
                                    </span>
                                    <span v-if="meeting.tugas.ungradedCount > 0" class="text-[10px] font-extrabold text-[#E65100]">
                                        • {{ meeting.tugas.ungradedCount }} Belum Dinilai
                                    </span>
                                </div>
                                <div class="text-sm font-extrabold text-charcoal leading-snug line-clamp-1">
                                    Pertemuan {{ meeting.number }}: {{ meeting.title }}
                                </div>
                                <div class="text-[11px] text-subtext mt-0.5">
                                    {{ meeting.date }} • {{ meeting.tugas.totalSubmitted }}/{{ meeting.tugas.totalStudents }} Mengumpulkan
                                </div>
                            </div>
                            <LmsIcon name="chevronRight" className="w-4 h-4 text-subtext shrink-0" />
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ============================================ -->
        <!-- LEVEL 3: DETAIL PERTEMUAN                   -->
        <!-- ============================================ -->
        <template v-else-if="currentView === 'meeting_detail'">
            <div class="flex-1 flex flex-col h-full overflow-hidden">
                <header class="flex items-center gap-3 px-4 sm:px-6 lg:px-8 pt-4 pb-3 shrink-0">
                    <button
                        type="button"
                        @click="backToMapelDetail"
                        class="w-10 h-10 rounded-2xl bg-white border border-[#ECEAE4] flex items-center justify-center text-charcoal soft-shadow hover:bg-canvas active:scale-95 transition-all cursor-pointer shrink-0"
                    >
                        <LmsIcon name="back" className="w-4 h-4" />
                    </button>
                    <div class="flex-1 min-w-0">
                        <div class="text-[11px] text-subtext font-medium">{{ selectedMapel?.shortName }}</div>
                        <h1 class="text-[15px] font-extrabold text-charcoal leading-tight line-clamp-1">
                            Pertemuan {{ selectedMeeting?.number }}: {{ selectedMeeting?.title }}
                        </h1>
                    </div>
                    <button
                        type="button"
                        @click="toggleLock(selectedMeeting?.id)"
                        class="w-10 h-10 rounded-2xl border flex items-center justify-center transition-all active:scale-95 cursor-pointer shrink-0"
                        :class="selectedMeeting?.isLocked ? 'bg-muted border-[#ECEAE4] text-charcoal' : 'bg-charcoal border-charcoal text-white'"
                        :title="selectedMeeting?.isLocked ? 'Buka akses ke siswa' : 'Kunci akses siswa'"
                    >
                        <LmsIcon :name="selectedMeeting?.isLocked ? 'lock' : 'unlock'" className="w-4 h-4" />
                    </button>
                </header>

                <!-- Sub-Tab Nav -->
                <div class="px-4 sm:px-6 lg:px-8 pb-3 shrink-0">
                    <div class="flex items-center gap-1 bg-muted/80 rounded-2xl p-1">
                        <button
                            v-for="tab in [
                                { id: 'tugas', label: 'Penilaian Tugas', icon: 'task' },
                                { id: 'materi', label: 'Materi', icon: 'book' },
                                { id: 'video', label: 'Video', icon: 'video' },
                            ]"
                            :key="tab.id"
                            type="button"
                            @click="activeMeetingTab = tab.id"
                            class="flex-1 flex items-center justify-center gap-1.5 px-2 py-2 rounded-xl text-[11px] font-bold transition-all cursor-pointer"
                            :class="activeMeetingTab === tab.id ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                        >
                            <LmsIcon :name="tab.icon" className="w-3.5 h-3.5 shrink-0" />
                            <span class="hidden sm:inline">{{ tab.label }}</span>
                            <span class="sm:hidden">{{ tab.id === 'tugas' ? 'Nilai' : tab.id === 'materi' ? 'Materi' : 'Video' }}</span>
                        </button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 pb-28 lg:pb-8">

                    <!-- ==== PENILAIAN TUGAS ==== -->
                    <template v-if="activeMeetingTab === 'tugas'">
                        <!-- Tugas Info -->
                        <div class="bg-white rounded-[22px] p-4 border border-[#ECEAE4] mb-4 soft-shadow">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="text-[10px] font-bold text-subtext uppercase tracking-wider">Tugas Aktif</div>
                                    <div class="text-sm font-extrabold text-charcoal mt-0.5 leading-snug">
                                        {{ selectedMeeting?.tugas.title }}
                                    </div>
                                    <div class="text-[11px] text-subtext mt-1">Deadline: {{ selectedMeeting?.tugas.deadline }}</div>
                                </div>
                                <span
                                    class="px-2.5 py-1 rounded-full text-[10px] font-extrabold shrink-0"
                                    :class="selectedMeeting?.tugas.deadlineStatus === 'Sudah Ditutup' ? 'bg-muted text-subtext' : 'bg-[#FFF3E0] text-[#E65100]'"
                                >
                                    {{ selectedMeeting?.tugas.deadlineStatus }}
                                </span>
                            </div>
                            <div class="mt-3 pt-3 border-t border-[#F2F0EC]">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-[11px] font-bold text-charcoal">
                                        {{ selectedMeeting?.tugas.totalSubmitted }}/{{ selectedMeeting?.tugas.totalStudents }} Mengumpulkan
                                    </span>
                                    <span
                                        class="text-[11px] font-bold"
                                        :class="selectedMeeting?.tugas.ungradedCount > 0 ? 'text-[#E65100]' : 'text-sage-dark'"
                                    >
                                        {{ selectedMeeting?.tugas.ungradedCount > 0 ? `${selectedMeeting?.tugas.ungradedCount} Belum Dinilai` : 'Semua Sudah Dinilai ✓' }}
                                    </span>
                                </div>
                                <div class="w-full h-1.5 bg-muted rounded-full overflow-hidden">
                                    <div
                                        class="h-full bg-charcoal rounded-full transition-all"
                                        :style="`width:${(selectedMeeting?.tugas.totalSubmitted/selectedMeeting?.tugas.totalStudents)*100}%`"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- Responsive 2-col on lg -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <!-- Belum Dinilai -->
                            <div class="space-y-3">
                                <h4 class="text-[13px] font-extrabold text-charcoal flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#E65100]"></span>
                                    Menunggu Penilaian ({{ ungradedStudents.length }})
                                </h4>
                                <div v-if="ungradedStudents.length === 0" class="text-center py-8 text-subtext text-xs font-medium bg-white rounded-[22px] border border-[#ECEAE4]">
                                    Tidak ada tugas yang perlu dinilai 🎉
                                </div>
                                <div
                                    v-for="student in ungradedStudents"
                                    :key="student.id"
                                    class="bg-white rounded-[22px] p-3.5 border border-[#ECEAE4] soft-shadow flex items-center gap-3"
                                >
                                    <img :src="student.avatar" :alt="student.name" class="w-9 h-9 rounded-full object-cover border border-[#E6E4DF] shrink-0" />
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-extrabold text-charcoal truncate">{{ student.name }}</div>
                                        <div class="text-[10px] mt-0.5 font-semibold" :class="submissionStatusColor(student.status)">
                                            {{ student.status }} • {{ student.submittedAt }}
                                        </div>
                                        <button type="button" @click.stop="handleDownload(student.submittedFile)" class="text-[10px] font-bold text-charcoal underline underline-offset-1 mt-0.5">
                                            📎 {{ student.submittedFile }}
                                        </button>
                                    </div>
                                    <button
                                        type="button"
                                        @click="openGradeModal(student)"
                                        class="px-3.5 py-2 rounded-full bg-charcoal text-white text-[11px] font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer shrink-0"
                                    >
                                        Beri Nilai
                                    </button>
                                </div>
                            </div>

                            <!-- Sudah Dinilai + Belum Kumpul -->
                            <div class="space-y-3">
                                <h4 class="text-[13px] font-extrabold text-charcoal flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-sage-dark"></span>
                                    Sudah Dinilai ({{ gradedStudents.length }})
                                </h4>
                                <div
                                    v-for="student in gradedStudents"
                                    :key="student.id"
                                    class="bg-white rounded-[22px] p-3.5 border border-[#ECEAE4] soft-shadow flex items-center gap-3"
                                >
                                    <img :src="student.avatar" :alt="student.name" class="w-9 h-9 rounded-full object-cover border border-[#E6E4DF] shrink-0" />
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-extrabold text-charcoal truncate">{{ student.name }}</div>
                                        <div class="text-[10px] text-subtext mt-0.5">Dinilai: {{ student.gradedAt }}</div>
                                        <div v-if="student.feedback" class="text-[10px] italic text-charcoal/70 mt-0.5 line-clamp-1">"{{ student.feedback }}"</div>
                                    </div>
                                    <div class="flex flex-col items-center shrink-0 mr-1">
                                        <span class="w-11 h-11 rounded-full bg-sage-light text-sage-dark font-black text-sm flex items-center justify-center">
                                            {{ student.score }}
                                        </span>
                                        <span class="text-[10px] font-bold text-sage-dark mt-0.5">Grade {{ student.grade }}</span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="openGradeModal(student)"
                                        class="w-8 h-8 rounded-full bg-muted text-charcoal flex items-center justify-center hover:bg-canvas active:scale-95 transition-all cursor-pointer shrink-0"
                                        title="Edit Nilai"
                                    >
                                        <LmsIcon name="task" className="w-3.5 h-3.5" />
                                    </button>
                                </div>

                                <template v-if="notSubmittedStudents.length > 0">
                                    <h4 class="text-[13px] font-extrabold text-charcoal flex items-center gap-2 mt-4">
                                        <span class="w-2 h-2 rounded-full bg-subtext"></span>
                                        Belum Mengumpulkan ({{ notSubmittedStudents.length }})
                                    </h4>
                                    <div
                                        v-for="student in notSubmittedStudents"
                                        :key="student.id"
                                        class="bg-muted rounded-[22px] p-3.5 border border-[#ECEAE4] flex items-center gap-3 opacity-70"
                                    >
                                        <img :src="student.avatar" :alt="student.name" class="w-9 h-9 rounded-full object-cover border border-[#E6E4DF] shrink-0 grayscale" />
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-extrabold text-charcoal truncate">{{ student.name }}</div>
                                            <div class="text-[10px] text-subtext mt-0.5">Belum ada pengumpulan</div>
                                        </div>
                                        <span class="text-[10px] font-bold text-subtext bg-white border border-[#ECEAE4] px-2.5 py-1 rounded-full shrink-0">-</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- ==== MATERI ==== -->
                    <template v-else-if="activeMeetingTab === 'materi'">
                        <div class="bg-white rounded-[22px] p-4 border border-[#ECEAE4] mb-4 soft-shadow">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="w-7 h-7 rounded-full bg-blossom-light text-blossom-dark flex items-center justify-center shrink-0">
                                    <LmsIcon name="book" className="w-3.5 h-3.5" />
                                </span>
                                <h3 class="text-[13px] font-extrabold text-charcoal">Ringkasan Pertemuan</h3>
                            </div>
                            <p class="text-xs text-charcoal/80 leading-relaxed">{{ selectedMeeting?.summary }}</p>
                        </div>

                        <div class="bg-white rounded-[26px] p-5 border border-[#ECEAE4] soft-shadow mb-4">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-full bg-blossom-light text-blossom-dark flex items-center justify-center">
                                        <LmsIcon name="book" className="w-4 h-4" />
                                    </span>
                                    <h3 class="text-[15px] font-extrabold text-charcoal">File Materi</h3>
                                </div>
                                <span
                                    class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full"
                                    :class="selectedMeeting?.isLocked ? 'bg-muted text-subtext' : 'bg-sage-light text-sage-dark'"
                                >
                                    {{ selectedMeeting?.isLocked ? 'Dikunci 🔒' : 'Terbuka untuk Siswa ✓' }}
                                </span>
                            </div>
                            <div class="bg-canvas rounded-2xl p-4 border border-[#ECEAE4] flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-[#FF4444]/10 text-[#FF4444] flex items-center justify-center font-black text-xs shrink-0">PDF</div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-extrabold text-charcoal truncate">{{ selectedMeeting?.materi.title }}</div>
                                    <div class="text-[11px] text-subtext mt-0.5">{{ selectedMeeting?.materi.pages }} • {{ selectedMeeting?.materi.size }}</div>
                                </div>
                                <button
                                    type="button"
                                    @click="handleDownload(selectedMeeting?.materi.title)"
                                    class="w-9 h-9 rounded-full bg-charcoal text-white flex items-center justify-center hover:opacity-90 active:scale-95 transition-all cursor-pointer shrink-0"
                                >
                                    <LmsIcon name="download" className="w-4 h-4" />
                                </button>
                            </div>
                            <button
                                type="button"
                                @click="showToast('Fitur unggah materi akan segera tersedia.')"
                                class="mt-3 w-full py-2.5 rounded-2xl border-2 border-dashed border-[#ECEAE4] text-xs font-bold text-subtext hover:border-charcoal/30 hover:text-charcoal transition-all cursor-pointer flex items-center justify-center gap-2"
                            >
                                <LmsIcon name="download" className="w-3.5 h-3.5 rotate-180" />
                                Ganti / Unggah File Materi Baru
                            </button>
                        </div>

                        <div class="bg-white rounded-[26px] p-5 border border-[#ECEAE4] soft-shadow">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="w-7 h-7 rounded-full bg-sage-light text-sage-dark flex items-center justify-center">
                                    <LmsIcon name="task" className="w-3.5 h-3.5" />
                                </span>
                                <h3 class="text-[15px] font-extrabold text-charcoal">File Lembar Tugas</h3>
                            </div>
                            <div class="bg-canvas rounded-2xl p-4 border border-[#ECEAE4] flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl bg-sage-light text-sage-dark flex items-center justify-center font-black text-xs shrink-0">PDF</div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-extrabold text-charcoal truncate">{{ selectedMeeting?.tugas.file }}</div>
                                    <div class="text-[11px] text-subtext mt-0.5">Deadline: {{ selectedMeeting?.tugas.deadline }}</div>
                                </div>
                                <button
                                    type="button"
                                    @click="handleDownload(selectedMeeting?.tugas.file)"
                                    class="w-9 h-9 rounded-full bg-charcoal text-white flex items-center justify-center hover:opacity-90 active:scale-95 transition-all cursor-pointer shrink-0"
                                >
                                    <LmsIcon name="download" className="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- ==== VIDEO ==== -->
                    <template v-else-if="activeMeetingTab === 'video'">
                        <div class="bg-white rounded-[26px] overflow-hidden border border-[#ECEAE4] soft-shadow">
                            <div
                                class="relative aspect-video w-full bg-charcoal cursor-pointer group"
                                @click="openVideo(selectedMeeting?.video.url)"
                            >
                                <img
                                    :src="selectedMeeting?.video.thumbnail"
                                    :alt="selectedMeeting?.video.title"
                                    class="w-full h-full object-cover opacity-80 group-hover:opacity-90 transition-opacity"
                                />
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-14 h-14 rounded-full bg-white/95 backdrop-blur-sm flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                        <span class="w-0 h-0 border-t-[10px] border-t-transparent border-l-[18px] border-l-charcoal border-b-[10px] border-b-transparent ml-1"></span>
                                    </div>
                                </div>
                                <div class="absolute top-3 left-3 bg-black/70 px-2.5 py-1 rounded-md text-[10px] font-black text-white flex items-center gap-1.5 border border-white/20">
                                    <span class="w-2.5 h-2 rounded-xs bg-[#FF0000] flex items-center justify-center">
                                        <span class="w-0 h-0 border-t-[2px] border-t-transparent border-l-[3.5px] border-l-white border-b-[2px] border-b-transparent"></span>
                                    </span>
                                    YouTube
                                </div>
                                <div class="absolute bottom-3 right-3 bg-black/80 px-2 py-0.5 rounded text-[10px] font-extrabold text-white">
                                    {{ selectedMeeting?.video.duration }}
                                </div>
                            </div>
                            <div class="p-4">
                                <h4 class="text-sm font-extrabold text-charcoal leading-snug">{{ selectedMeeting?.video.title }}</h4>
                                <div class="flex items-center justify-between mt-2 pt-2 border-t border-[#F2F0EC]">
                                    <span class="text-[11px] font-semibold text-subtext">📺 {{ selectedMeeting?.video.channel }}</span>
                                    <button type="button" @click="openVideo(selectedMeeting?.video.url)" class="text-xs font-bold text-red-600 hover:underline cursor-pointer">
                                        Tonton di YouTube ↗
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="showToast('Fitur ganti video referensi akan segera tersedia.')"
                            class="mt-4 w-full py-2.5 rounded-2xl border-2 border-dashed border-[#ECEAE4] text-xs font-bold text-subtext hover:border-charcoal/30 hover:text-charcoal transition-all cursor-pointer flex items-center justify-center gap-2"
                        >
                            <LmsIcon name="video" className="w-3.5 h-3.5" />
                            Ganti Referensi Video
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <!-- ============================================ -->
        <!-- GRADING MODAL                               -->
        <!-- ============================================ -->
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isGradingOpen"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-4"
                @click.self="isGradingOpen = false"
            >
                <div class="bg-white rounded-[28px] w-full max-w-md p-6 shadow-2xl">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <div class="text-[11px] font-bold text-subtext uppercase tracking-wider">Beri / Edit Nilai</div>
                            <h3 class="text-[17px] font-extrabold text-charcoal mt-0.5">{{ gradingStudent?.name }}</h3>
                        </div>
                        <button
                            type="button"
                            @click="isGradingOpen = false"
                            class="w-8 h-8 rounded-full bg-muted text-charcoal flex items-center justify-center hover:bg-canvas active:scale-95 transition-all cursor-pointer text-xs font-bold"
                        >
                            ✕
                        </button>
                    </div>

                    <div v-if="gradingStudent?.submittedFile" class="bg-canvas rounded-2xl p-3 border border-[#ECEAE4] mb-4 flex items-center gap-2">
                        <span class="text-xs font-bold text-charcoal/60">📎</span>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-extrabold text-charcoal truncate">{{ gradingStudent?.submittedFile }}</div>
                            <div class="text-[10px] text-subtext">{{ gradingStudent?.submittedAt }}</div>
                        </div>
                        <button type="button" @click="handleDownload(gradingStudent?.submittedFile)" class="text-[10px] font-bold text-charcoal underline cursor-pointer">Unduh</button>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-extrabold text-charcoal mb-2">Nilai (0 - 100)</label>
                        <div class="flex items-center gap-3">
                            <input
                                v-model="gradingScore"
                                type="number"
                                min="0"
                                max="100"
                                placeholder="0 - 100"
                                class="flex-1 bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-3 text-2xl font-extrabold text-charcoal text-center focus:outline-none focus:border-charcoal/50 transition-colors"
                            />
                            <div class="w-14 h-14 rounded-full bg-charcoal text-white flex items-center justify-center font-black text-lg shrink-0">
                                {{ gradeFromScore(gradingScore) }}
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-extrabold text-charcoal mb-2">Umpan Balik / Catatan (Opsional)</label>
                        <textarea
                            v-model="gradingFeedback"
                            rows="3"
                            placeholder="Tuliskan catatan atau umpan balik untuk siswa..."
                            class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-3 text-xs font-medium text-charcoal placeholder:text-subtext/70 focus:outline-none focus:border-charcoal/50 transition-colors resize-none"
                        ></textarea>
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="button"
                            @click="isGradingOpen = false"
                            class="flex-1 py-3 rounded-2xl bg-muted text-charcoal text-sm font-bold hover:bg-canvas active:scale-95 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="saveGrade"
                            class="flex-1 py-3 rounded-2xl bg-charcoal text-white text-sm font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer"
                        >
                            Simpan Nilai
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
input[type='number']::-webkit-inner-spin-button,
input[type='number']::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type='number'] {
    -moz-appearance: textfield;
}
</style>
