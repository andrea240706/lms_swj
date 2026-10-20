<script setup>
import { computed, ref } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';
import { initialMapelList } from './mapelData';

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['openMenu', 'openProfile']);

// State mapel data (reactive clone so students can submit homework and see immediate updates)
const mapelList = ref(JSON.parse(JSON.stringify(initialMapelList)));

// Navigation views: 'list' (Daftar Mapel) | 'mapel_detail' (Daftar Pertemuan) | 'meeting_detail' (Detail Pertemuan)
const currentView = ref('list');
const selectedMapelId = ref(null);
const selectedMeetingId = ref(null);

// Category filter on Mapel list
const activeCategory = ref('Semua');
const searchQuery = ref('');

// Toast feedback
const toastMessage = ref('');
let toastTimer = null;
const showToast = (msg) => {
    toastMessage.value = msg;
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
        toastMessage.value = '';
    }, 2800);
};

// Upload Modal state
const isUploadModalOpen = ref(false);
const uploadTarget = ref({ mapelId: null, meetingId: null, tugasTitle: '' });
const simulatedFileName = ref('Tugas_Praktikum_NadiaPutri_XIIRPL1.zip');

// Selected Mapel & Meeting computed
const selectedMapel = computed(() => {
    return mapelList.value.find((m) => m.id === selectedMapelId.value) || null;
});

const selectedMeeting = computed(() => {
    if (!selectedMapel.value) return null;
    return selectedMapel.value.meetings.find((mt) => mt.id === selectedMeetingId.value) || null;
});

// Filtered Mapel list
const filteredMapelList = computed(() => {
    return mapelList.value.filter((m) => {
        const matchCategory =
            activeCategory.value === 'Semua' ||
            (activeCategory.value === 'Produktif' && m.category.includes('Produktif')) ||
            (activeCategory.value === 'Umum' && m.category.includes('Umum'));
        const query = searchQuery.value.trim().toLowerCase();
        const matchQuery =
            !query ||
            m.name.toLowerCase().includes(query) ||
            m.teacher.toLowerCase().includes(query) ||
            m.code.toLowerCase().includes(query);
        return matchCategory && matchQuery;
    });
});

// Navigation actions
const openMapel = (mapelId) => {
    selectedMapelId.value = mapelId;
    currentView.value = 'mapel_detail';
};

const openMeeting = (meetingId) => {
    selectedMeetingId.value = meetingId;
    currentView.value = 'meeting_detail';
};

const backToMapelList = () => {
    currentView.value = 'list';
    selectedMapelId.value = null;
    selectedMeetingId.value = null;
};

const backToMeetingList = () => {
    currentView.value = 'mapel_detail';
    selectedMeetingId.value = null;
};

// Open external video link
const openVideo = (videoUrl) => {
    if (videoUrl) {
        window.open(videoUrl, '_blank', 'noopener,noreferrer');
    }
};

// Download simulation
const handleDownload = (fileName) => {
    showToast(`Mengunduh berkas: ${fileName}`);
};

// Open Upload modal
const openUpload = (mapelId, meetingId, tugasTitle) => {
    uploadTarget.value = { mapelId, meetingId, tugasTitle };
    simulatedFileName.value = `Tugas_Pertemuan${meetingId}_NadiaPutri.zip`;
    isUploadModalOpen.value = true;
};

// Confirm upload homework
const submitHomework = () => {
    if (!uploadTarget.value.mapelId || !uploadTarget.value.meetingId) return;

    const mapel = mapelList.value.find((m) => m.id === uploadTarget.value.mapelId);
    if (mapel) {
        const meeting = mapel.meetings.find((mt) => mt.id === uploadTarget.value.meetingId);
        if (meeting && meeting.tugas) {
            meeting.tugas.isSubmitted = true;
            meeting.tugas.submittedFile = simulatedFileName.value;
            const now = new Date();
            const dateStr = now.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            });
            const timeStr = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
            });
            meeting.tugas.submittedAt = `${dateStr}, ${timeStr} WIB`;
            meeting.tugas.status = 'Menunggu Penilaian Guru';
            if (meeting.nilai) {
                meeting.nilai.status = 'Menunggu Penilaian Guru';
                meeting.nilai.feedback = 'Berkas tugas telah diterima, sedang dalam antrean koreksi guru pengampu.';
            }
            showToast('Tugas berhasil dikirim ke guru!');
        }
    }
    isUploadModalOpen.value = false;
};
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden relative">
        <!-- Floating Toast Notification -->
        <transition
            enter-active-class="transform ease-out duration-200 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0"
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

        <!-- ======================================================== -->
        <!-- LEVEL 1: DAFTAR MATA PELAJARAN (1 SEMESTER)              -->
        <!-- ======================================================== -->
        <template v-if="currentView === 'list'">
            <LmsTopBar
                :role="role"
                title="Mata Pelajaran"
                @openMenu="$emit('openMenu')"
                @openProfile="$emit('openProfile')"
            />

            <div class="flex-1 overflow-y-auto no-scrollbar px-6 pb-28">
                <!-- Semester Header Banner -->
                <div class="hero-sage rounded-[28px] p-5 relative overflow-hidden soft-shadow mb-4">
                    <div class="relative z-10">
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-block bg-white/90 text-charcoal px-3 py-1 rounded-full text-[11px] font-extrabold shadow-2xs"
                            >
                                Semester Ganjil 2026/2027
                            </span>
                            <span class="text-[11px] font-bold text-sage-dark">
                                • 5 Mapel Terdaftar
                            </span>
                        </div>
                        <h2 class="text-[20px] font-extrabold text-charcoal mt-2 leading-tight">
                            Kurikulum Merdeka Kejuruan
                        </h2>
                        <p class="text-xs font-medium text-charcoal/80 mt-1 max-w-[85%]">
                            Pilih mata pelajaran di bawah untuk melihat modul belajar, mengumpulkan tugas, nilai, dan video materi per pertemuan.
                        </p>
                    </div>

                    <!-- Decorative Badge Cluster -->
                    <div
                        class="absolute -right-4 -bottom-4 w-24 h-24 rounded-full bg-white/30 flex items-center justify-center pointer-events-none"
                    >
                        <span class="text-2xl font-black text-charcoal/20">XII</span>
                    </div>
                </div>

                <!-- Category Pills Filter & Search -->
                <div class="mb-4 space-y-2.5">
                    <!-- Category Pills -->
                    <div class="flex items-center gap-2">
                        <button
                            v-for="cat in ['Semua', 'Produktif', 'Umum']"
                            :key="cat"
                            type="button"
                            @click="activeCategory = cat"
                            class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer border"
                            :class="
                                activeCategory === cat
                                    ? 'bg-charcoal text-white border-charcoal shadow-xs'
                                    : 'bg-white text-subtext border-[#ECEAE4] hover:border-charcoal/30'
                            "
                        >
                            {{ cat }}
                        </button>
                    </div>

                    <!-- Search Input -->
                    <div class="relative">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari mapel atau guru pengampu..."
                            class="w-full bg-white rounded-2xl px-4 py-2.5 text-xs text-charcoal placeholder:text-subtext/70 border border-[#ECEAE4] soft-shadow focus:outline-none focus:border-charcoal/40 transition-colors"
                        />
                        <span
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="absolute right-3.5 top-2.5 text-subtext text-xs cursor-pointer font-bold"
                        >
                            ✕
                        </span>
                    </div>
                </div>

                <!-- List of 5 Mapel Cards -->
                <div class="space-y-3">
                    <div
                        v-for="mapel in filteredMapelList"
                        :key="mapel.id"
                        @click="openMapel(mapel.id)"
                        class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all relative overflow-hidden group"
                    >
                        <!-- Top Row: Code & Category Pill -->
                        <div class="flex items-center justify-between mb-2">
                            <span
                                class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full border"
                                :class="mapel.badgeColor"
                            >
                                {{ mapel.code }} • {{ mapel.category }}
                            </span>
                            <span class="text-[11px] font-bold text-subtext">
                                {{ mapel.completedMeetings }}/{{ mapel.totalMeetings }} Selesai
                            </span>
                        </div>

                        <!-- Subject Title -->
                        <h3 class="text-[16px] font-extrabold text-charcoal leading-snug group-hover:text-black">
                            {{ mapel.name }}
                        </h3>

                        <!-- Teacher Info & Room -->
                        <div class="flex items-center gap-2.5 mt-3 pt-2.5 border-t border-[#F2F0EC]">
                            <img
                                :src="mapel.teacherAvatar"
                                :alt="mapel.teacher"
                                class="w-8 h-8 rounded-full object-cover border border-[#E6E4DF] shrink-0"
                            />
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-charcoal truncate">
                                    {{ mapel.teacher }}
                                </div>
                                <div class="text-[10px] text-subtext truncate">
                                    {{ mapel.dayTime }} • {{ mapel.room }}
                                </div>
                            </div>
                            <div
                                class="w-7 h-7 rounded-full bg-canvas flex items-center justify-center text-charcoal group-hover:bg-charcoal group-hover:text-white transition-colors shrink-0"
                            >
                                <LmsIcon name="arrowUpRight" className="w-3.5 h-3.5" />
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-3">
                            <div class="w-full h-1.5 bg-[#EFECE6] rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-charcoal rounded-full transition-all duration-300"
                                    :style="{ width: `${mapel.progress}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="filteredMapelList.length === 0"
                        class="text-center py-10 bg-white rounded-3xl border border-dashed border-[#ECEAE4] p-6"
                    >
                        <p class="text-xs font-bold text-subtext">
                            Tidak ada mata pelajaran yang cocok dengan pencarian.
                        </p>
                    </div>
                </div>
            </div>
        </template>

        <!-- ======================================================== -->
        <!-- LEVEL 2: DETAIL MAPEL & DAFTAR PERTEMUAN                 -->
        <!-- ======================================================== -->
        <template v-else-if="currentView === 'mapel_detail' && selectedMapel">
            <!-- Header Bar with Back Button -->
            <header class="px-6 pt-2 pb-3 flex items-center justify-between shrink-0">
                <button
                    type="button"
                    @click="backToMapelList"
                    class="flex items-center gap-1.5 text-xs font-bold text-charcoal px-3 py-1.5 rounded-full bg-white border border-[#ECEAE4] soft-shadow hover:bg-canvas transition-colors cursor-pointer"
                >
                    <LmsIcon name="back" className="w-3.5 h-3.5" />
                    <span>Daftar Mapel</span>
                </button>
                <span class="text-xs font-extrabold text-subtext">
                    {{ selectedMapel.code }}
                </span>
            </header>

            <div class="flex-1 overflow-y-auto no-scrollbar px-6 pb-28">
                <!-- CARD UI DENGAN JUDUL DARI SETIAP MAPEL (Di Bagian Atas) -->
                <div
                    class="rounded-[28px] p-5 relative overflow-hidden soft-shadow mb-5"
                    :class="selectedMapel.heroBg"
                >
                    <!-- Subject Badge -->
                    <div class="flex items-center justify-between">
                        <span
                            class="inline-block bg-white/95 px-3 py-1 rounded-full text-[10px] font-extrabold text-charcoal shadow-2xs"
                        >
                            {{ selectedMapel.semester }}
                        </span>
                        <span class="text-[11px] font-bold text-charcoal/80">
                            {{ selectedMapel.totalMeetings }} Pertemuan
                        </span>
                    </div>

                    <!-- Subject Title -->
                    <h2 class="text-[20px] font-extrabold text-charcoal mt-3 leading-tight">
                        {{ selectedMapel.name }}
                    </h2>
                    <p class="text-xs font-semibold text-charcoal/75 mt-1">
                        {{ selectedMapel.category }} • {{ selectedMapel.room }}
                    </p>

                    <!-- Teacher Pill -->
                    <div class="flex items-center gap-2.5 mt-4 bg-white/80 backdrop-blur-xs p-2.5 rounded-2xl border border-white/60">
                        <img
                            :src="selectedMapel.teacherAvatar"
                            :alt="selectedMapel.teacher"
                            class="w-9 h-9 rounded-full object-cover border border-white shrink-0"
                        />
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-extrabold text-charcoal leading-tight truncate">
                                {{ selectedMapel.teacher }}
                            </div>
                            <div class="text-[10px] font-medium text-charcoal/70 mt-0.5 truncate">
                                {{ selectedMapel.teacherRole }}
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal & Progress Pill -->
                    <div class="mt-3 flex items-center justify-between text-xs font-bold text-charcoal/80">
                        <span>🕒 {{ selectedMapel.dayTime }}</span>
                        <span>{{ selectedMapel.progress }}% Tuntas</span>
                    </div>
                </div>

                <!-- Section: DAFTAR BEBERAPA PERTEMUAN (Misal Pertemuan 1 / Senin, 19 Juli 2026) -->
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-[16px] font-extrabold text-charcoal">
                        Daftar Pertemuan
                    </h3>
                    <span class="text-xs font-bold text-subtext">
                        5 Sesi Terjadwal
                    </span>
                </div>

                <div class="space-y-3">
                    <div
                        v-for="mt in selectedMapel.meetings"
                        :key="mt.id"
                        @click="openMeeting(mt.id)"
                        class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all"
                    >
                        <!-- Meeting Number & Date Badge (Contoh: Pertemuan 1 / Senin, 20 Juli 2026) -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-extrabold text-charcoal">
                                Pertemuan {{ mt.number }} • {{ mt.date }}
                            </span>
                            <span
                                class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full"
                                :class="
                                    mt.status === 'Selesai'
                                        ? 'bg-sage-light text-sage-dark'
                                        : mt.status === 'Sedang Berlangsung'
                                          ? 'bg-amber-light text-amber-dark'
                                          : 'bg-muted text-subtext'
                                "
                            >
                                {{ mt.status }}
                            </span>
                        </div>

                        <!-- Meeting Topic Title -->
                        <h4 class="text-[14px] font-extrabold text-charcoal leading-snug">
                            {{ mt.title }}
                        </h4>

                        <!-- Brief Summary -->
                        <p class="text-xs text-subtext mt-1 line-clamp-2 leading-relaxed">
                            {{ mt.summary }}
                        </p>

                        <!-- Content Features Pills: Materi, Tugas & Nilai, Video -->
                        <div class="mt-3.5 pt-2.5 border-t border-[#F2F0EC] flex items-center justify-between">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-canvas text-charcoal border border-[#ECEAE4]"
                                >
                                    📄 Modul
                                </span>
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full border"
                                    :class="
                                        mt.tugas?.isSubmitted
                                            ? 'bg-sage-light text-sage-dark border-[#C8DFCD]'
                                            : 'bg-canvas text-charcoal border-[#ECEAE4]'
                                    "
                                >
                                    📝 {{ mt.tugas?.isSubmitted ? 'Tugas Terkirim' : 'Tugas' }}
                                </span>
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#FF0000]/10 text-[#CC0000] border border-[#FF0000]/20 flex items-center gap-1"
                                >
                                    ▶ YouTube
                                </span>
                            </div>

                            <span class="text-xs font-extrabold text-charcoal flex items-center gap-0.5">
                                Buka
                                <LmsIcon name="arrowUpRight" className="w-3 h-3 text-subtext" />
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ======================================================== -->
        <!-- LEVEL 3: DETAIL PERTEMUAN (Materi, Tugas, Nilai, Video)  -->
        <!-- ======================================================== -->
        <template v-else-if="currentView === 'meeting_detail' && selectedMapel && selectedMeeting">
            <!-- Header Bar with Back Button -->
            <header class="px-6 pt-2 pb-3 flex items-center justify-between shrink-0">
                <button
                    type="button"
                    @click="backToMeetingList"
                    class="flex items-center gap-1.5 text-xs font-bold text-charcoal px-3 py-1.5 rounded-full bg-white border border-[#ECEAE4] soft-shadow hover:bg-canvas transition-colors cursor-pointer"
                >
                    <LmsIcon name="back" className="w-3.5 h-3.5" />
                    <span>Pertemuan {{ selectedMapel.shortName }}</span>
                </button>
                <span class="text-xs font-extrabold text-subtext">
                    {{ selectedMeeting.time }}
                </span>
            </header>

            <div class="flex-1 overflow-y-auto no-scrollbar px-6 pb-28 space-y-4">
                <!-- TOP CARD UI: Pertemuan Info Header -->
                <div
                    class="rounded-[28px] p-5 relative overflow-hidden soft-shadow"
                    :class="selectedMapel.heroBg"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="inline-block bg-white/95 px-3 py-1 rounded-full text-[11px] font-extrabold text-charcoal shadow-2xs"
                        >
                            Pertemuan {{ selectedMeeting.number }} • {{ selectedMeeting.date }}
                        </span>
                        <span
                            class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full"
                            :class="
                                selectedMeeting.status === 'Selesai'
                                    ? 'bg-[#2E7D32] text-white'
                                    : selectedMeeting.status === 'Sedang Berlangsung'
                                      ? 'bg-amber-600 text-white'
                                      : 'bg-charcoal/70 text-white'
                            "
                        >
                            {{ selectedMeeting.status }}
                        </span>
                    </div>

                    <h2 class="text-[18px] font-extrabold text-charcoal mt-3 leading-tight">
                        {{ selectedMeeting.title }}
                    </h2>
                    <p class="text-xs font-medium text-charcoal/80 mt-1 leading-relaxed">
                        {{ selectedMeeting.summary }}
                    </p>

                    <div class="mt-3 pt-3 border-t border-charcoal/10 flex items-center justify-between text-[11px] font-semibold text-charcoal/80">
                        <span>👨‍🏫 {{ selectedMapel.teacher }}</span>
                        <span>🏫 {{ selectedMapel.room }}</span>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- 1. KOLOM FILE MATERI                           -->
                <!-- ============================================== -->
                <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-sky-light text-sky-dark flex items-center justify-center">
                                <LmsIcon name="book" className="w-4 h-4" />
                            </span>
                            <h3 class="text-[15px] font-extrabold text-charcoal">
                                Kolom Berkas Materi
                            </h3>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-canvas text-subtext border border-[#ECEAE4]">
                            {{ selectedMeeting.materi.pages }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-canvas border border-[#ECEAE4]">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center font-extrabold text-xs shrink-0">
                                PDF
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-extrabold text-charcoal truncate leading-snug">
                                    {{ selectedMeeting.materi.title }}
                                </h4>
                                <div class="text-[10px] text-subtext mt-0.5 font-medium">
                                    Ukuran: {{ selectedMeeting.materi.size }} • Format Dokumen Resmi
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-charcoal/80 mt-2.5 font-normal leading-relaxed">
                            {{ selectedMeeting.materi.description }}
                        </p>

                        <!-- Download & Read Action -->
                        <div class="mt-3.5 flex items-center gap-2">
                            <button
                                type="button"
                                @click="handleDownload(selectedMeeting.materi.title)"
                                class="flex-1 py-2.5 rounded-full bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-[0.99] transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-xs"
                            >
                                <LmsIcon name="download" className="w-3.5 h-3.5" />
                                <span>Unduh Berkas Materi</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- 2. KOLOM FILE TUGAS & PENGUMPULAN TUGAS         -->
                <!-- ============================================== -->
                <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-blossom-light text-blossom-dark flex items-center justify-center">
                                <LmsIcon name="task" className="w-4 h-4" />
                            </span>
                            <h3 class="text-[15px] font-extrabold text-charcoal">
                                Kolom Tugas &amp; Pengumpulan
                            </h3>
                        </div>
                        <span
                            class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full"
                            :class="
                                selectedMeeting.tugas.isSubmitted
                                    ? 'bg-sage-light text-sage-dark'
                                    : 'bg-blossom-light text-blossom-dark'
                            "
                        >
                            {{ selectedMeeting.tugas.status }}
                        </span>
                    </div>

                    <!-- Tugas Description & Lembar Soal -->
                    <div class="p-3.5 rounded-2xl bg-canvas border border-[#ECEAE4] space-y-3">
                        <div>
                            <h4 class="text-xs font-extrabold text-charcoal leading-snug">
                                {{ selectedMeeting.tugas.title }}
                            </h4>
                            <div class="flex items-center justify-between mt-2 pt-2 border-t border-[#E8E6E0]">
                                <span class="text-[11px] font-semibold text-subtext">
                                    📄 {{ selectedMeeting.tugas.file }} ({{ selectedMeeting.tugas.fileSize }})
                                </span>
                                <button
                                    type="button"
                                    @click="handleDownload(selectedMeeting.tugas.file)"
                                    class="text-xs font-extrabold text-charcoal underline cursor-pointer hover:opacity-80"
                                >
                                    Unduh Soal
                                </button>
                            </div>
                        </div>

                        <!-- Informasi Deadline Pengumpulan Tugas -->
                        <div class="p-3 rounded-xl bg-white border border-[#ECEAE4] flex items-center justify-between">
                            <div>
                                <div class="text-[10px] font-bold text-subtext uppercase tracking-wide">
                                    Batas Waktu Pengumpulan
                                </div>
                                <div class="text-xs font-extrabold text-charcoal mt-0.5">
                                    {{ selectedMeeting.tugas.deadline }}
                                </div>
                            </div>
                            <span
                                class="text-[10px] font-extrabold px-2 py-1 rounded-lg"
                                :class="
                                    selectedMeeting.tugas.isSubmitted
                                        ? 'bg-sage-light text-sage-dark'
                                        : 'bg-red-50 text-red-600 border border-red-100'
                                "
                            >
                                {{ selectedMeeting.tugas.deadlineStatus }}
                            </span>
                        </div>

                        <!-- Pengumpulan Tugas Status & Upload Action -->
                        <div class="pt-1">
                            <!-- Sudah Dikumpulkan State -->
                            <div
                                v-if="selectedMeeting.tugas.isSubmitted"
                                class="p-3 rounded-xl bg-sage-light/40 border border-[#C8DFCD]"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-full bg-[#2E7D32] text-white flex items-center justify-center">
                                            <LmsIcon name="check" className="w-3 h-3" />
                                        </span>
                                        <span class="text-xs font-extrabold text-[#2E7D32]">
                                            Tugas Berhasil Terkirim
                                        </span>
                                    </div>
                                    <span class="text-[10px] font-semibold text-subtext">
                                        {{ selectedMeeting.tugas.submittedAt }}
                                    </span>
                                </div>
                                <div class="text-xs font-medium text-charcoal mt-1.5 pl-7">
                                    Berkas: <span class="font-bold underline">{{ selectedMeeting.tugas.submittedFile }}</span>
                                </div>

                                <button
                                    type="button"
                                    @click="openUpload(selectedMapel.id, selectedMeeting.id, selectedMeeting.tugas.title)"
                                    class="mt-3 w-full py-2 rounded-full bg-white text-charcoal border border-[#D5D2C9] text-xs font-bold hover:bg-canvas transition-colors cursor-pointer"
                                >
                                    Kirim Ulang / Perbarui Berkas Jawaban
                                </button>
                            </div>

                            <!-- Belum Dikumpulkan State -->
                            <div v-else>
                                <button
                                    type="button"
                                    @click="openUpload(selectedMapel.id, selectedMeeting.id, selectedMeeting.tugas.title)"
                                    class="w-full py-3 rounded-full bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-[0.99] transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-xs"
                                >
                                    <LmsIcon name="download" className="w-3.5 h-3.5 rotate-180" />
                                    <span>Kumpulkan Jawaban Tugas Sekarang</span>
                                </button>
                                <p class="text-[10px] text-center text-subtext mt-1.5 font-medium">
                                    Mendukung berkas format PDF, ZIP, RAR, atau Gambar (Maks 20 MB)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- 3. KOLOM KHUSUS MELIHAT INFORMASI NILAI        -->
                <!-- ============================================== -->
                <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-sage-light text-sage-dark flex items-center justify-center">
                                <LmsIcon name="chart" className="w-4 h-4" />
                            </span>
                            <h3 class="text-[15px] font-extrabold text-charcoal">
                                Kolom Informasi Nilai
                            </h3>
                        </div>
                        <span class="text-xs font-bold text-subtext">
                            KKM: {{ selectedMeeting.nilai.kkm }}
                        </span>
                    </div>

                    <!-- Graded State -->
                    <div
                        v-if="selectedMeeting.nilai.hasScore"
                        class="p-4 rounded-2xl bg-canvas border border-[#ECEAE4] space-y-3"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-subtext uppercase tracking-wider">
                                    Hasil Penilaian Resmi
                                </span>
                                <div class="flex items-baseline gap-2 mt-0.5">
                                    <span class="text-3xl font-black text-charcoal">
                                        {{ selectedMeeting.nilai.score }}
                                    </span>
                                    <span class="text-xs font-bold text-subtext">/ 100</span>
                                </div>
                            </div>

                            <div class="text-right">
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-xs font-extrabold bg-[#2E7D32]/10 text-[#2E7D32] border border-[#2E7D32]/30"
                                >
                                    ✓ {{ selectedMeeting.nilai.status }} (Grade {{ selectedMeeting.nilai.grade }})
                                </span>
                                <div class="text-[10px] text-subtext mt-1">
                                    Dinilai: {{ selectedMeeting.nilai.gradedAt }}
                                </div>
                            </div>
                        </div>

                        <!-- Feedback Note from Teacher -->
                        <div class="p-3 rounded-xl bg-white border border-[#ECEAE4]">
                            <div class="text-[10px] font-bold text-subtext uppercase tracking-wide flex items-center justify-between">
                                <span>Catatan / Umpan Balik Guru</span>
                                <span class="text-[10px] font-bold text-charcoal">{{ selectedMeeting.nilai.gradedBy }}</span>
                            </div>
                            <p class="text-xs font-semibold text-charcoal mt-1.5 italic leading-relaxed">
                                "{{ selectedMeeting.nilai.feedback }}"
                            </p>
                        </div>
                    </div>

                    <!-- Not Graded State -->
                    <div
                        v-else
                        class="p-4 rounded-2xl bg-canvas border border-dashed border-[#ECEAE4] text-center"
                    >
                        <div class="w-10 h-10 rounded-full bg-muted mx-auto flex items-center justify-center text-subtext mb-2">
                            <LmsIcon name="clock" className="w-5 h-5" />
                        </div>
                        <div class="text-xs font-extrabold text-charcoal">
                            {{ selectedMeeting.nilai.status }}
                        </div>
                        <p class="text-[11px] text-subtext mt-1 max-w-[85%] mx-auto leading-relaxed">
                            {{ selectedMeeting.nilai.feedback }}
                        </p>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- 4. MATERI VIDEO (YOUTUBE PREVIEW & REDIRECT)   -->
                <!-- ============================================== -->
                <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-extrabold text-xs">
                                ▶
                            </span>
                            <h3 class="text-[15px] font-extrabold text-charcoal">
                                Video Pembelajaran
                            </h3>
                        </div>
                        <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-[#FF0000]/10 text-[#CC0000] border border-[#FF0000]/20">
                            YouTube
                        </span>
                    </div>

                    <!-- YouTube Preview Card (Click redirects directly to YouTube) -->
                    <div
                        @click="openVideo(selectedMeeting.video.url)"
                        class="group cursor-pointer rounded-2xl overflow-hidden border border-[#ECEAE4] soft-shadow hover:border-charcoal/40 transition-all bg-black relative"
                    >
                        <!-- Thumbnail Preview with 16:9 Aspect Ratio -->
                        <div class="relative w-full aspect-video overflow-hidden">
                            <img
                                :src="selectedMeeting.video.thumbnail"
                                :alt="selectedMeeting.video.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 opacity-90 group-hover:opacity-100"
                            />
                            <!-- Dark Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                            <!-- Big Play Button Overlay -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div
                                    class="w-13 h-13 rounded-full bg-[#FF0000] text-white flex items-center justify-center shadow-2xl group-hover:scale-110 group-active:scale-95 transition-transform"
                                >
                                    <LmsIcon name="play" className="w-5 h-5 ml-0.5 fill-current" />
                                </div>
                            </div>

                            <!-- YouTube Logo Pill Top Left -->
                            <div class="absolute top-2.5 left-2.5 bg-black/70 backdrop-blur-xs px-2.5 py-1 rounded-md text-[10px] font-black text-white flex items-center gap-1.5 border border-white/20">
                                <span class="w-2.5 h-2 rounded-xs bg-[#FF0000] flex items-center justify-center">
                                    <span class="w-0 h-0 border-t-[2px] border-t-transparent border-l-[3.5px] border-l-white border-b-[2px] border-b-transparent"></span>
                                </span>
                                <span>YouTube</span>
                            </div>

                            <!-- Duration Pill Bottom Right -->
                            <div class="absolute bottom-2.5 right-2.5 bg-black/80 px-2 py-0.5 rounded text-[10px] font-extrabold text-white">
                                {{ selectedMeeting.video.duration }}
                            </div>
                        </div>

                        <!-- Video Title & Channel Info -->
                        <div class="p-3 bg-white border-t border-[#ECEAE4]">
                            <h4 class="text-xs font-extrabold text-charcoal leading-snug group-hover:text-black line-clamp-2">
                                {{ selectedMeeting.video.title }}
                            </h4>
                            <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-[#F2F0EC]">
                                <span class="text-[11px] font-semibold text-subtext truncate">
                                    📺 {{ selectedMeeting.video.channel }}
                                </span>
                                <span class="text-xs font-bold text-red-600 flex items-center gap-1 shrink-0 group-hover:underline">
                                    Tonton di YouTube ↗
                                </span>
                            </div>
                        </div>
                    </div>

                    <p class="text-[10px] text-subtext text-center mt-2.5 font-medium">
                        Ketuk pratinjau di atas untuk membuka video secara otomatis di laman YouTube.
                    </p>
                </div>
            </div>
        </template>

        <!-- ======================================================== -->
        <!-- MODAL: UNGGAH / PENGUMPULAN TUGAS SISWA                  -->
        <!-- ======================================================== -->
        <div
            v-if="isUploadModalOpen"
            @click="isUploadModalOpen = false"
            class="fixed inset-0 bg-charcoal/40 backdrop-blur-xs flex items-end sm:items-center justify-center z-50 p-0 sm:p-4"
        >
            <div
                @click.stop
                class="w-full max-w-[400px] bg-white rounded-t-[32px] sm:rounded-[32px] p-6 shadow-2xl transition-all"
            >
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-blossom-light text-blossom-dark">
                        Unggah Tugas
                    </span>
                    <button
                        type="button"
                        @click="isUploadModalOpen = false"
                        class="text-subtext hover:text-charcoal font-bold text-sm cursor-pointer"
                    >
                        ✕
                    </button>
                </div>

                <h3 class="text-lg font-extrabold text-charcoal leading-snug">
                    {{ uploadTarget.tugasTitle }}
                </h3>
                <p class="text-xs text-subtext mt-1">
                    Silakan pilih berkas jawaban tugas kamu dari penyimpanan perangkat.
                </p>

                <!-- Simulated File Picker Box -->
                <div class="mt-4 p-4 rounded-2xl border-2 border-dashed border-[#D5D2C9] bg-canvas text-center">
                    <div class="w-10 h-10 rounded-full bg-white mx-auto flex items-center justify-center text-charcoal shadow-xs mb-2">
                        <LmsIcon name="task" className="w-5 h-5" />
                    </div>
                    <div class="text-xs font-extrabold text-charcoal">
                        {{ simulatedFileName }}
                    </div>
                    <div class="text-[10px] text-subtext mt-0.5 font-semibold">
                        Ukuran: 4.2 MB • Siap diunggah
                    </div>
                </div>

                <!-- Action Button -->
                <button
                    type="button"
                    @click="submitHomework"
                    class="mt-5 w-full py-3.5 rounded-full bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-[0.99] transition-all cursor-pointer shadow-sm"
                >
                    Konfirmasi &amp; Kirim Tugas
                </button>
                <button
                    type="button"
                    @click="isUploadModalOpen = false"
                    class="mt-2 w-full py-2.5 rounded-full text-xs font-bold text-subtext hover:text-charcoal transition-colors cursor-pointer"
                >
                    Batal
                </button>
            </div>
        </div>
    </div>
</template>
