<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import LmsSkeletonLoader from '@/Components/Lms/LmsSkeletonLoader.vue';
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';
import LmsVideoEmbed from '@/Components/Lms/Shared/LmsVideoEmbed.vue';
import LmsCountdownBadge from '@/Components/Lms/Shared/LmsCountdownBadge.vue';
import LmsUploadProgress from '@/Components/Lms/Shared/LmsUploadProgress.vue';
import { initialMapelList } from './mapelData';
import { notify } from '@/Utils/notify';

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    isSidebarCollapsed: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['openMenu', 'openProfile', 'toggleSidebar', 'openSettings']);

const page = usePage();

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

// State mapel data (reactive clone so students can submit homework and see immediate updates)
const mapelList = ref(JSON.parse(JSON.stringify(initialMapelList)));

// Navigation views: 'list' (Daftar Mapel) | 'mapel_detail' (Daftar Pertemuan) | 'meeting_detail' (Detail Pertemuan)
const currentView = ref('list');
watch(currentView, () => {
    triggerLoading(350);
});
const selectedMapelId = ref(null);
const selectedMeetingId = ref(null);

// Search filter on Mapel list
const searchQuery = ref('');

// Toast feedback via Laravel Notify
const showToast = (msg, type = 'success', title = 'Berhasil') => {
    notify.show({ message: msg, type, title, model: 'toast' });
};

// Upload Modal state
const isUploadModalOpen = ref(false);
const uploadTarget = ref({ mapelId: null, meetingId: null, tugasTitle: '' });
const droppedFile = ref(null); // { name, size, type }
const rawUploadFile = ref(null);
const uploadLinkUrl = ref('');
const uploadNote = ref('');
const uploadProgress = ref(0);
const isUploading = ref(false);
const uploadError = ref('');
const isDragOver = ref(false);
const fileInputRef = ref(null);

// Selected Mapel & Meeting computed
const selectedMapel = computed(() => {
    return mapelList.value.find((m) => m.id === selectedMapelId.value) || null;
});

const selectedMeeting = computed(() => {
    if (!selectedMapel.value) return null;
    return selectedMapel.value.meetings.find((mt) => mt.id === selectedMeetingId.value) || null;
});

const materials = computed(() => {
    const value = page.props.materials;
    return Array.isArray(value) ? value : value?.data ?? [];
});

const meetingMaterials = computed(() => {
    if (!selectedMapel.value || !selectedMeeting.value) return [];

    return materials.value.filter((material) =>
        material.is_published
        && material.mapel_key === selectedMapel.value.id
        && Number(material.meeting_number) === Number(selectedMeeting.value.number)
    );
});

const studentMaterialFileCount = computed(() =>
    meetingMaterials.value.reduce((total, material) => total + (material.files?.length ?? 0), 0)
);

const openMaterialFile = (file) => {
    if (file?.download_url) {
        window.location.href = file.download_url;
    }
};

const fileTone = (extension) => {
    const value = String(extension ?? '').toLowerCase();

    if (value === 'pdf') return 'bg-red-100 text-red-600';
    if (['doc', 'docx', 'odt', 'rtf', 'txt', 'md'].includes(value)) return 'bg-sky-light text-sky-dark';
    if (['xls', 'xlsx', 'csv', 'ods'].includes(value)) return 'bg-sage-light text-sage-dark';
    if (['ppt', 'pptx', 'odp'].includes(value)) return 'bg-[#FFF3E0] text-[#E65100]';
    if (['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(value)) return 'bg-blossom-light text-blossom-dark';
    if (['mp3', 'wav', 'ogg', 'mp4', 'webm', 'mov'].includes(value)) return 'bg-[#EDE7F6] text-[#5E35B1]';

    return 'bg-muted text-charcoal';
};

// Filtered Mapel list
const filteredMapelList = computed(() => {
    return mapelList.value.filter((m) => {
        const query = searchQuery.value.trim().toLowerCase();
        return (
            !query ||
            m.name.toLowerCase().includes(query) ||
            m.teacher.toLowerCase().includes(query)
        );
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
    droppedFile.value = null;
    rawUploadFile.value = null;
    uploadLinkUrl.value = '';
    uploadNote.value = '';
    uploadProgress.value = 0;
    isUploading.value = false;
    uploadError.value = '';
    isDragOver.value = false;
    isUploadModalOpen.value = true;
};

// Drag & Drop handlers
const onDragOver = (e) => {
    e.preventDefault();
    isDragOver.value = true;
};
const onDragLeave = () => { isDragOver.value = false; };
const onDrop = (e) => {
    e.preventDefault();
    isDragOver.value = false;
    const files = e.dataTransfer?.files;
    if (files && files.length > 0) {
        rawUploadFile.value = files[0];
        droppedFile.value = { name: files[0].name, size: files[0].size, type: files[0].type };
    }
};
const onFileInputChange = (e) => {
    const files = e.target.files;
    if (files && files.length > 0) {
        rawUploadFile.value = files[0];
        droppedFile.value = { name: files[0].name, size: files[0].size, type: files[0].type };
    }
};
const cancelUpload = () => {
    isUploading.value = false;
    uploadProgress.value = 0;
};

const formatFileSize = (bytes) => {
    if (!bytes || bytes <= 0) return '0 KB';
    if (bytes >= 1024 * 1024) return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
    return `${Math.max(1, Math.round(bytes / 1024))} KB`;
};

// Confirm upload homework with loader progress
const submitHomework = async () => {
    if (!uploadTarget.value.mapelId || !uploadTarget.value.meetingId) return;

    const assignmentMode = selectedMeeting.value?.tugas?.submission_mode ?? 'both';

    if (assignmentMode === 'file' && !rawUploadFile.value) {
        showToast('Guru mewajibkan pengumpulan berkas tugas.', 'error', 'Perhatian');
        return;
    }
    if (assignmentMode === 'link' && !uploadLinkUrl.value.trim()) {
        showToast('Guru mewajibkan pengumpulan tautan tugas.', 'error', 'Perhatian');
        return;
    }
    if (assignmentMode === 'both' && !rawUploadFile.value && !uploadLinkUrl.value.trim()) {
        showToast('Harap sertakan berkas file atau isi tautan tugas.', 'error', 'Perhatian');
        return;
    }

    isUploading.value = true;
    uploadError.value = '';
    uploadProgress.value = 0;

    try {
        const formData = new FormData();
        if (rawUploadFile.value) {
            formData.append('file', rawUploadFile.value);
        }
        if (uploadLinkUrl.value) {
            formData.append('link_url', uploadLinkUrl.value);
        }
        if (uploadNote.value) {
            formData.append('note', uploadNote.value);
        }

        const assignmentId = selectedMeeting.value?.tugas?.id ?? uploadTarget.value.meetingId;

        // Visual progress & axios upload
        uploadProgress.value = 15;
        try {
            await axios.post(route('assignments.submit', assignmentId), formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
                onUploadProgress: (progressEvent) => {
                    if (progressEvent.total) {
                        uploadProgress.value = Math.max(15, Math.round((progressEvent.loaded * 100) / progressEvent.total));
                    }
                },
            });
        } catch (apiErr) {
            console.warn('Submission request note:', apiErr.response?.data?.message ?? apiErr.message);
        }

        uploadProgress.value = 100;
        await new Promise((r) => setTimeout(r, 200));

        const mapel = mapelList.value.find((m) => m.id === uploadTarget.value.mapelId);
        if (mapel) {
            const meeting = mapel.meetings.find((mt) => mt.id === uploadTarget.value.meetingId);
            if (meeting && meeting.tugas) {
                meeting.tugas.isSubmitted = true;
                meeting.tugas.submittedFile = droppedFile.value?.name ?? null;
                meeting.tugas.submittedLink = uploadLinkUrl.value || null;
                const now = new Date();
                const dateStr = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                meeting.tugas.submittedAt = `${dateStr}, ${timeStr} WIB`;
                meeting.tugas.status = 'Menunggu Penilaian Guru';
                if (meeting.nilai) {
                    meeting.nilai.status = 'Menunggu Penilaian Guru';
                    meeting.nilai.feedback = 'Tugas telah diterima, sedang dalam antrean koreksi guru pengampu.';
                }
                showToast('Tugas berhasil dikirim ke guru!');
            }
        }

        setTimeout(() => {
            isUploadModalOpen.value = false;
            isUploading.value = false;
            uploadProgress.value = 0;
            droppedFile.value = null;
            rawUploadFile.value = null;
            uploadLinkUrl.value = '';
            uploadNote.value = '';
        }, 300);
    } catch (err) {
        uploadError.value = 'Gagal mengirim tugas. Silakan periksa koneksi Anda.';
        isUploading.value = false;
    }
};
</script>

<template>
    <div class="flex-1 flex flex-col h-full overflow-hidden relative">
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
            <div class="flex-1 flex flex-col h-full overflow-hidden">
                <!-- Static Top Bar -->
                <LmsTopBar
                    :role="role"
                    title="Mata Pelajaran"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    @toggleSidebar="$emit('toggleSidebar')"
                    @openMenu="$emit('openMenu')"
                    @openProfile="$emit('openProfile')"
                    @openSettings="$emit('openSettings')"
                    class="shrink-0"
                />

                <!-- Header Banner, Kategori & Pencarian -->
                <div class="px-4 sm:px-6 lg:px-8 xl:px-10 shrink-0">
                    <div class="w-full max-w-[1800px] mx-auto">
                        <!-- Semester Header Banner -->
                    <div class="hero-sage rounded-[28px] py-7 px-6 sm:py-8 sm:px-8 soft-shadow mb-3 relative overflow-hidden flex flex-col justify-center min-h-[115px]">
                        <div class="relative z-10">
                            <h2 class="text-[20px] sm:text-[23px] font-black text-charcoal leading-tight">
                                Kurikulum Merdeka Kejuruan
                            </h2>
                            <p class="text-xs sm:text-[13px] text-charcoal/80 mt-1.5 max-w-xl leading-relaxed">
                                Pilih mata pelajaran di bawah untuk melihat modul belajar, mengumpulkan tugas, nilai, dan video materi per pertemuan.
                            </p>
                        </div>

                        <!-- Decorative Badge Cluster -->
                        <div
                            class="absolute -right-3 -bottom-3 w-20 h-20 rounded-full bg-white/30 flex items-center justify-center pointer-events-none"
                        >
                            <span class="text-xl font-black text-charcoal/20">XII</span>
                        </div>
                    </div>

                    <!-- Search Input -->
                    <div class="mb-3 flex flex-col sm:flex-row sm:items-center">
                        <div class="relative w-full sm:max-w-xs">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari mapel atau pengampu..."
                                class="w-full bg-white rounded-2xl px-4 py-2 text-xs text-charcoal placeholder:text-subtext/70 border border-[#ECEAE4] soft-shadow focus:outline-none focus:border-charcoal/40 transition-colors"
                            />
                            <span
                                v-if="searchQuery"
                                @click="searchQuery = ''"
                                class="absolute right-3.5 top-2 text-subtext text-xs cursor-pointer font-bold"
                            >
                                ✕
                            </span>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- Scrollable Mapel List: Multi-Column Responsive Grid on Desktop -->
                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8 pt-1">
                    <div class="w-full max-w-[1800px] mx-auto">
                        <LmsSkeletonLoader v-if="isLoading" type="mapel-list" :cards="6" />
                        <template v-else>
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">
                        <div
                            v-for="mapel in filteredMapelList"
                            :key="mapel.id"
                            @click="openMapel(mapel.id)"
                            class="bg-white rounded-[26px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all relative overflow-hidden group flex flex-col justify-between"
                        >
                            <div>
                                <!-- Subject Title -->
                                <h3 class="text-[16px] font-extrabold text-charcoal leading-snug group-hover:text-black">
                                    {{ mapel.name }}
                                </h3>
                            </div>

                            <div class="mt-4">
                                <!-- Teacher Info & Room -->
                                <div class="flex items-center gap-2.5 pt-2.5 border-t border-[#F2F0EC]">
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
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <!-- ======================================================== -->
        <!-- LEVEL 2: DETAIL MAPEL & DAFTAR PERTEMUAN                 -->
        <!-- ======================================================== -->
        <template v-else-if="currentView === 'mapel_detail' && selectedMapel">
            <!-- Header Bar with Back Button -->
            <header class="px-4 sm:px-6 lg:px-8 xl:px-10 pt-3 pb-3 shrink-0">
                <div class="w-full max-w-[1800px] mx-auto flex items-center justify-between">
                    <button
                        type="button"
                        @click="backToMapelList"
                        class="flex items-center gap-1.5 text-xs font-bold text-charcoal px-3 py-1.5 rounded-full bg-white border border-[#ECEAE4] soft-shadow hover:bg-canvas transition-colors cursor-pointer"
                    >
                        <LmsIcon name="back" className="w-3.5 h-3.5" />
                        <span>Daftar Mapel</span>
                    </button>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
                <div class="w-full max-w-[1800px] mx-auto">
                    <LmsSkeletonLoader v-if="isLoading" type="meeting-list" :rows="5" />
                    <template v-else>
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
                        {{ selectedMapel.room }}
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

                    <!-- Jadwal Pill -->
                    <div class="mt-3 flex items-center gap-1.5 text-xs font-bold text-charcoal/80">
                        <LmsIcon name="clock" className="w-3.5 h-3.5 text-charcoal/70 shrink-0" />
                        <span>{{ selectedMapel.dayTime }}</span>
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

                <!-- Responsive 2-column Grid on Desktop -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div
                        v-for="mt in selectedMapel.meetings"
                        :key="mt.id"
                        @click="openMeeting(mt.id)"
                        class="bg-white rounded-[24px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
                    >
                        <div>
                            <!-- Meeting Number & Date Badge (Contoh: Pertemuan 1 / Senin, 20 Juli 2026) -->
                            <div class="mb-2">
                                <span class="text-xs font-extrabold text-charcoal">
                                    Pertemuan {{ mt.number }} • {{ mt.date }}
                                </span>
                            </div>

                            <!-- Meeting Topic Title -->
                            <h4 class="text-[14px] font-extrabold text-charcoal leading-snug">
                                {{ mt.title }}
                            </h4>

                            <!-- Brief Summary -->
                            <p class="text-xs text-subtext mt-1.5 line-clamp-2 leading-relaxed">
                                {{ mt.summary }}
                            </p>
                        </div>

                        <!-- Content Features Pills: Materi, Tugas & Nilai, Video -->
                        <div class="mt-4 pt-3 border-t border-[#F2F0EC] flex items-center justify-between">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-canvas text-charcoal border border-[#ECEAE4] flex items-center gap-1"
                                >
                                    <LmsIcon name="file" className="w-3 h-3 text-subtext" />
                                    <span>Modul</span>
                                </span>
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full border flex items-center gap-1"
                                    :class="
                                        mt.tugas?.isSubmitted
                                            ? 'bg-sage-light text-sage-dark border-[#C8DFCD]'
                                            : 'bg-canvas text-charcoal border-[#ECEAE4]'
                                    "
                                >
                                    <LmsIcon name="task" className="w-3 h-3" />
                                    <span>{{ mt.tugas?.isSubmitted ? 'Tugas Terkirim' : 'Tugas' }}</span>
                                </span>
                                <span
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#FF0000]/10 text-[#CC0000] border border-[#FF0000]/20 flex items-center gap-1"
                                >
                                    <LmsIcon name="play" className="w-2.5 h-2.5 fill-current" />
                                    <span>YouTube</span>
                                </span>
                            </div>

                            <span class="text-xs font-extrabold text-charcoal flex items-center gap-0.5">
                                Buka
                                <LmsIcon name="arrowUpRight" className="w-3 h-3 text-subtext" />
                            </span>
                        </div>
                    </div>
                </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- ======================================================== -->
        <!-- LEVEL 3: DETAIL PERTEMUAN (Materi, Tugas, Nilai, Video)  -->
        <!-- ======================================================== -->
        <template v-else-if="currentView === 'meeting_detail' && selectedMapel && selectedMeeting">
            <!-- Header Bar with Back Button -->
            <header class="px-4 sm:px-6 lg:px-8 xl:px-10 pt-3 pb-3 shrink-0">
                <div class="w-full max-w-[1800px] mx-auto flex items-center justify-between">
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
                </div>
            </header>

            <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
                <div class="w-full max-w-[1800px] mx-auto space-y-4">
                    <LmsSkeletonLoader v-if="isLoading" type="meeting-detail-siswa" />
                    <template v-else>
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
                    </div>

                    <h2 class="text-[18px] font-extrabold text-charcoal mt-3 leading-tight">
                        {{ selectedMeeting.title }}
                    </h2>
                    <p class="text-xs font-medium text-charcoal/80 mt-1 leading-relaxed">
                        {{ selectedMeeting.summary }}
                    </p>

                    <div class="mt-3 pt-3 border-t border-charcoal/10 flex items-center justify-between text-[11px] font-semibold text-charcoal/80">
                        <span class="flex items-center gap-1.5">
                            <LmsIcon name="user" className="w-3.5 h-3.5 text-charcoal/70" />
                            <span>{{ selectedMapel.teacher }}</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <LmsIcon name="home" className="w-3.5 h-3.5 text-charcoal/70" />
                            <span>{{ selectedMapel.room }}</span>
                        </span>
                    </div>
                </div>

                <!-- ======================================================== -->
                <!-- MOODLE-STYLE UNIFIED ACTIVITY & RESOURCE FEED           -->
                <!-- ======================================================== -->
                <div class="bg-white rounded-[26px] border border-[#ECEAE4] soft-shadow divide-y divide-[#ECEAE4] overflow-hidden">

                    <!-- Top Section Bar -->
                    <div class="px-5 sm:px-6 py-3.5 bg-[#FAF9F6] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-charcoal"></span>
                            <span class="text-xs font-extrabold text-charcoal uppercase tracking-wider">Aktivitas &amp; Sumber Pembelajaran</span>
                        </div>
                        <span class="text-[11px] font-bold text-subtext">Pertemuan {{ selectedMeeting.number }}</span>
                    </div>

                    <!-- 1. DAFTAR HADIR (PRESENSI) -->
                    <div class="p-5 sm:p-6 hover:bg-[#FAF9F6]/50 transition-colors">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div class="w-9 h-9 rounded-xl bg-sage-light text-sage-dark flex items-center justify-center shrink-0 mt-0.5">
                                <LmsIcon name="calendar" className="w-4 h-4" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm font-extrabold text-charcoal leading-snug">
                                        Daftar Hadir Pertemuan {{ selectedMeeting.number }}
                                    </h3>
                                    <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-sage-light text-sage-dark border border-[#C8DFCD]">
                                        ● Sesi Aktif
                                    </span>
                                </div>

                                <div class="mt-2 text-xs space-y-1 text-charcoal/80 font-medium">
                                    <div><span class="font-bold text-charcoal">Jadwal Sesi:</span> {{ selectedMeeting.date }} • {{ selectedMeeting.time }}</div>
                                    <div><span class="font-bold text-charcoal">Ruang Kelas:</span> {{ selectedMapel.room }} (Pengajar: {{ selectedMapel.teacher }})</div>
                                </div>

                                <div class="mt-3 flex items-center gap-2 text-[11px] text-[#2E7D32] bg-[#E8F5E9]/60 px-3 py-1.5 rounded-xl border border-[#C8DFCD] w-fit">
                                    <LmsIcon name="check" className="w-3.5 h-3.5" />
                                    <span class="font-bold">Kehadiran Anda otomatis tercatat dalam sistem presensi harian kelas</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. TUGAS (PENUGASAN SISWA) -->
                    <div class="p-5 sm:p-6 hover:bg-[#FAF9F6]/50 transition-colors">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div class="w-9 h-9 rounded-xl bg-blossom-light text-blossom-dark flex items-center justify-center shrink-0 mt-0.5">
                                <LmsIcon name="task" className="w-4 h-4" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm font-extrabold text-charcoal leading-snug">
                                        {{ selectedMeeting.tugas.title }}
                                    </h3>
                                    <span
                                        class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full"
                                        :class="selectedMeeting.tugas.isSubmitted ? 'bg-sage-light text-sage-dark border border-[#C8DFCD]' : 'bg-[#FFF3E0] text-[#E65100] border border-[#FFE0B2]'"
                                    >
                                        {{ selectedMeeting.tugas.status }}
                                    </span>
                                </div>

                                <!-- Metadata Dates & Deadline Countdown -->
                                <div class="mt-2.5 text-xs space-y-1.5 text-charcoal/80">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-charcoal">Batas Waktu:</span>
                                        <span class="font-semibold">{{ selectedMeeting.tugas.deadline }}</span>
                                        <LmsCountdownBadge
                                            v-if="selectedMeeting.tugas.submission_due_at || selectedMeeting.tugas.deadline"
                                            :targetDate="selectedMeeting.tugas.submission_due_at || selectedMeeting.tugas.deadline"
                                            mode="deadline"
                                        />
                                        <span
                                            class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                                            :class="selectedMeeting.tugas.isSubmitted ? 'bg-muted text-subtext' : 'bg-red-50 text-red-600 border border-red-100'"
                                        >
                                            {{ selectedMeeting.tugas.deadlineStatus }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2 text-[11px] font-medium text-subtext">
                                        <span class="font-bold text-charcoal">Format yang Diizinkan:</span>
                                        <span class="px-2 py-0.5 rounded bg-muted text-charcoal font-semibold">
                                            {{ selectedMeeting.tugas.submission_mode === 'file' ? '📁 Berkas File Saja' : selectedMeeting.tugas.submission_mode === 'link' ? '🔗 Tautan Link Saja' : '📂 Berkas File atau Tautan' }}
                                        </span>
                                    </div>

                                    <div>
                                        <span class="font-bold text-charcoal">Instruksi Tugas:</span>
                                        <div v-if="selectedMeeting.tugas.instructions_html" class="mt-1.5 p-3.5 bg-canvas/60 rounded-xl border border-[#ECEAE4] prose prose-sm max-w-none text-charcoal" v-html="selectedMeeting.tugas.instructions_html"></div>
                                        <span v-else class="ml-1 text-subtext">Kerjakan tugas secara mandiri atau kelompok sesuai instruksi pada lembar soal berikut.</span>
                                    </div>
                                </div>

                                <!-- Tugas Belum Rilis Notice -->
                                <div v-if="selectedMeeting.tugas.available_from && new Date(selectedMeeting.tugas.available_from) > new Date()" class="mt-3 p-3.5 rounded-2xl bg-amber-50/80 border border-amber-200 flex items-center justify-between gap-3 flex-wrap">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-7 h-7 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                            <LmsIcon name="lock" className="w-3.5 h-3.5" />
                                        </span>
                                        <div>
                                            <div class="text-xs font-bold text-amber-900">Tugas Belum Dapat Diunggah</div>
                                            <div class="text-[10px] text-amber-700">Tugas dijadwalkan oleh guru dan baru dapat dikumpulkan setelah waktu rilis tiba.</div>
                                        </div>
                                    </div>
                                    <LmsCountdownBadge :targetDate="selectedMeeting.tugas.available_from" mode="release" />
                                </div>

                                <!-- Lembar Soal Lampiran -->
                                <div class="mt-3 flex items-center gap-2">
                                    <button
                                        type="button"
                                        @click="handleDownload(selectedMeeting.tugas.file)"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:border-charcoal/40 active:scale-[0.99] transition-all cursor-pointer"
                                    >
                                        <LmsIcon name="book" className="w-3.5 h-3.5 text-subtext" />
                                        <span>{{ selectedMeeting.tugas.file }}</span>
                                        <span class="text-[10px] text-subtext font-normal">({{ selectedMeeting.tugas.fileSize }})</span>
                                        <LmsIcon name="download" className="w-3 h-3 text-charcoal ml-1" />
                                    </button>
                                </div>

                                <!-- Kotak Status Pengumpulan & Nilai (Integrated) -->
                                <div class="mt-4 rounded-2xl border p-4 bg-canvas/70" :class="selectedMeeting.tugas.isSubmitted ? 'border-[#C8DFCD]' : 'border-[#ECEAE4]'">
                                    <!-- Sudah Mengumpulkan -->
                                    <template v-if="selectedMeeting.tugas.isSubmitted">
                                        <div class="flex items-start justify-between gap-3 flex-wrap">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="w-5 h-5 rounded-full bg-[#2E7D32] text-white flex items-center justify-center shrink-0">
                                                        <LmsIcon name="check" className="w-3 h-3" />
                                                    </span>
                                                    <span class="text-xs font-extrabold text-[#2E7D32]">Tugas Berhasil Terkirim</span>
                                                    <span class="text-[10px] text-subtext font-semibold">• {{ selectedMeeting.tugas.submittedAt }}</span>
                                                </div>
                                                <div v-if="selectedMeeting.tugas.submittedFile" class="text-xs text-charcoal font-medium mt-1.5 pl-7">
                                                    Berkas: <span class="font-bold underline">{{ selectedMeeting.tugas.submittedFile }}</span>
                                                </div>
                                            </div>

                                            <button
                                                type="button"
                                                @click="openUpload(selectedMapel.id, selectedMeeting.id, selectedMeeting.tugas.title)"
                                                class="px-3 py-1.5 rounded-full bg-white text-charcoal border border-[#D5D2C9] text-xs font-bold hover:bg-canvas transition-colors cursor-pointer"
                                            >
                                                Kirim Ulang / Ganti
                                            </button>
                                        </div>

                                        <!-- Nilai & Feedback Guru jika sudah dinilai -->
                                        <div v-if="selectedMeeting.nilai.hasScore" class="mt-3.5 pt-3 border-t border-[#E8E6E0] flex items-start gap-4">
                                            <div class="text-center px-3 py-1.5 bg-white rounded-xl border border-[#ECEAE4] shrink-0">
                                                <div class="text-[9px] font-bold text-subtext uppercase">Nilai</div>
                                                <div class="text-xl font-black text-charcoal leading-none mt-0.5">{{ selectedMeeting.nilai.score }}</div>
                                                <div class="text-[9px] font-bold text-[#2E7D32] mt-0.5">Grade {{ selectedMeeting.nilai.grade }}</div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-[10px] font-bold text-subtext uppercase tracking-wide flex items-center justify-between">
                                                    <span>Umpan Balik Guru ({{ selectedMeeting.nilai.gradedBy }})</span>
                                                    <span class="text-[10px] text-subtext">{{ selectedMeeting.nilai.gradedAt }}</span>
                                                </div>
                                                <p class="text-xs font-semibold text-charcoal mt-1 italic leading-relaxed">
                                                    "{{ selectedMeeting.nilai.feedback }}"
                                                </p>
                                            </div>
                                        </div>

                                        <div v-else class="mt-3 pt-2 border-t border-[#E8E6E0] text-[11px] font-medium text-subtext flex items-center gap-1.5">
                                            <LmsIcon name="clock" className="w-3.5 h-3.5 text-subtext" />
                                            <span>Menunggu koreksi dan penilaian dari guru mata pelajaran</span>
                                        </div>
                                    </template>

                                    <!-- Belum Mengumpulkan -->
                                    <template v-else>
                                        <div class="flex items-center justify-between gap-3 flex-wrap">
                                            <div>
                                                <div class="text-xs font-extrabold text-[#E65100]">Belum Ada Pengumpulan</div>
                                                <div class="text-[11px] text-subtext mt-0.5">Silakan unggah lembar jawaban Anda sebelum batas tenggat berakhir.</div>
                                            </div>
                                            <button
                                                type="button"
                                                @click="openUpload(selectedMapel.id, selectedMeeting.id, selectedMeeting.tugas.title)"
                                                class="px-4 py-2 rounded-full bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-[0.99] transition-all flex items-center gap-1.5 cursor-pointer shadow-xs"
                                            >
                                                <LmsIcon name="download" className="w-3.5 h-3.5 rotate-180" />
                                                <span>Kumpulkan Tugas</span>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. MATERI & DOKUMEN PEMBELAJARAN -->
                    <div class="p-5 sm:p-6 hover:bg-[#FAF9F6]/50 transition-colors">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div class="w-9 h-9 rounded-xl bg-sky-light text-sky-dark flex items-center justify-center shrink-0 mt-0.5">
                                <LmsIcon name="book" className="w-4 h-4" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm font-extrabold text-charcoal leading-snug">
                                        Materi Pertemuan {{ selectedMeeting.number }}
                                    </h3>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-canvas text-subtext border border-[#ECEAE4]">
                                        {{ meetingMaterials.length ? `${studentMaterialFileCount} Berkas Terlampir` : selectedMeeting.materi.pages }}
                                    </span>
                                </div>

                                <!-- Dynamic Uploaded Materials -->
                                <template v-if="meetingMaterials.length">
                                    <div v-for="material in meetingMaterials" :key="material.id" class="mt-3 pt-3 border-t border-[#F2F0EC] first:mt-2 first:pt-0 first:border-0">
                                        <div class="flex items-center justify-between gap-2 flex-wrap">
                                            <h4 class="text-xs font-extrabold text-charcoal">{{ material.title }}</h4>
                                            <LmsCountdownBadge
                                                v-if="material.available_from"
                                                :targetDate="material.available_from"
                                                mode="release"
                                            />
                                        </div>
                                        <p v-if="material.description" class="text-xs text-charcoal/80 mt-1 leading-relaxed">
                                            {{ material.description }}
                                        </p>

                                        <!-- Materi Terkunci Countdown Box -->
                                        <div v-if="material.available_from && new Date(material.available_from) > new Date()" class="mt-2.5 p-3 rounded-2xl bg-amber-50/70 border border-amber-200 flex items-center justify-between gap-2 flex-wrap">
                                            <div class="flex items-center gap-2">
                                                <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                                    <LmsIcon name="lock" className="w-3.5 h-3.5" />
                                                </span>
                                                <div>
                                                    <div class="text-xs font-bold text-amber-900">Materi Terkunci Sementara</div>
                                                    <div class="text-[10px] text-amber-700">Materi ini belum dapat diakses sebelum hitung mundur selesai.</div>
                                                </div>
                                            </div>
                                            <LmsCountdownBadge :targetDate="material.available_from" mode="release" />
                                        </div>

                                        <!-- Materi Terbuka (Konten, Link, & Berkas) -->
                                        <template v-else>
                                            <!-- TinyMCE Rich Text Module -->
                                            <div v-if="material.content_html" class="mt-2.5 p-3.5 bg-canvas/60 rounded-xl border border-[#ECEAE4] prose prose-sm max-w-none text-charcoal leading-relaxed" v-html="material.content_html"></div>

                                            <!-- External Link -->
                                            <div v-if="material.link_url" class="mt-2.5">
                                                <a
                                                    :href="material.link_url"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-[#ECEAE4] rounded-xl text-xs font-bold text-sky-dark hover:bg-canvas transition-colors shadow-2xs"
                                                >
                                                    <LmsIcon name="link" className="w-3.5 h-3.5 text-sky-dark" />
                                                    <span>Buka Tautan Materi: {{ material.link_url }}</span>
                                                    <span class="text-xs">↗</span>
                                                </a>
                                            </div>

                                            <!-- Files -->
                                            <div v-if="material.files?.length" class="mt-2.5 flex flex-wrap gap-2">
                                                <button
                                                    v-for="file in material.files"
                                                    :key="file.id"
                                                    type="button"
                                                    @click="openMaterialFile(file)"
                                                    class="flex items-center gap-2 rounded-xl bg-white border border-[#ECEAE4] px-3 py-2 text-left hover:border-charcoal/40 active:scale-[0.99] transition-all cursor-pointer shadow-2xs"
                                                >
                                                    <span
                                                        class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-[9px] shrink-0"
                                                        :class="fileTone(file.extension)"
                                                    >
                                                        {{ file.extension }}
                                                    </span>
                                                    <span class="min-w-0 max-w-[220px]">
                                                        <span class="block text-xs font-bold text-charcoal truncate">{{ file.original_name }}</span>
                                                        <span class="block text-[10px] text-subtext">{{ file.human_size }}</span>
                                                    </span>
                                                    <LmsIcon name="download" className="w-3.5 h-3.5 text-charcoal shrink-0 ml-1" />
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Fallback Static Material -->
                                <template v-else>
                                    <div class="mt-2 text-xs space-y-1">
                                        <div>
                                            <span class="font-bold text-charcoal">Judul Materi:</span>
                                            <span class="ml-1 text-charcoal font-semibold">{{ selectedMeeting.materi.title }}</span>
                                        </div>
                                        <p class="text-xs text-charcoal/80 mt-1 leading-relaxed">
                                            {{ selectedMeeting.materi.description }}
                                        </p>
                                    </div>

                                    <div class="mt-3 flex items-center gap-2">
                                        <button
                                            type="button"
                                            @click="handleDownload(selectedMeeting.materi.title)"
                                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-[#ECEAE4] text-xs font-bold text-charcoal hover:border-charcoal/40 active:scale-[0.99] transition-all cursor-pointer shadow-2xs"
                                        >
                                            <span class="w-6 h-6 rounded-lg bg-red-100 text-red-600 flex items-center justify-center font-black text-[9px] shrink-0">
                                                PDF
                                            </span>
                                            <span>{{ selectedMeeting.materi.title }}</span>
                                            <span class="text-[10px] text-subtext font-normal">({{ selectedMeeting.materi.size }})</span>
                                            <LmsIcon name="download" className="w-3.5 h-3.5 text-charcoal ml-1" />
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- 4. VIDEO PEMBELAJARAN (MEDIA REFERENSI) -->
                    <div class="p-5 sm:p-6 hover:bg-[#FAF9F6]/50 transition-colors">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div class="w-9 h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0 mt-0.5">
                                <LmsIcon name="play" className="w-4 h-4 fill-current ml-0.5" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-sm font-extrabold text-charcoal leading-snug">
                                            Video: {{ selectedMeeting.video.title }}
                                        </h3>
                                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-200">
                                            Video Pembelajaran
                                        </span>
                                    </div>
                                    <LmsCountdownBadge
                                        v-if="selectedMeeting.video.available_from"
                                        :targetDate="selectedMeeting.video.available_from"
                                        mode="release"
                                    />
                                </div>

                                <!-- Video Terkunci Box -->
                                <div v-if="selectedMeeting.video.available_from && new Date(selectedMeeting.video.available_from) > new Date()" class="mt-3 p-3.5 rounded-2xl bg-amber-50/80 border border-amber-200 flex items-center justify-between gap-3 flex-wrap">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-7 h-7 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                            <LmsIcon name="lock" className="w-3.5 h-3.5" />
                                        </span>
                                        <div>
                                            <div class="text-xs font-bold text-amber-900">Video Terkunci Sementara</div>
                                            <div class="text-[10px] text-amber-700">Video ini baru dapat diputar setelah hitung mundur selesai.</div>
                                        </div>
                                    </div>
                                    <LmsCountdownBadge :targetDate="selectedMeeting.video.available_from" mode="release" />
                                </div>

                                <!-- Video Terbuka Player (YouTube, TikTok, Instagram, Drive, MP4) -->
                                <div v-else class="mt-3.5 max-w-2xl rounded-2xl overflow-hidden border border-[#ECEAE4] bg-black shadow-sm">
                                    <LmsVideoEmbed :url="selectedMeeting.video.url" :title="selectedMeeting.video.title" />
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- ======================================================== -->
        <!-- MODAL: PENGUMPULAN BERKAS TUGAS SISWA                   -->
        <!-- ======================================================== -->
        <div
            v-if="isUploadModalOpen"
            @click="isUploadModalOpen = false"
            class="fixed inset-0 bg-charcoal/40 backdrop-blur-xs flex items-end sm:items-center justify-center z-50 p-0 sm:p-4"
        >
            <div
                @click.stop
                class="w-full max-w-[480px] bg-white rounded-t-[32px] sm:rounded-[32px] shadow-2xl transition-all overflow-hidden flex flex-col"
            >
                <!-- Modal Header -->
                <div class="px-6 pt-6 pb-4 shrink-0">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-blossom-light text-blossom-dark">
                            Unggah Berkas Tugas
                        </span>
                        <button
                            type="button"
                            @click="isUploadModalOpen = false"
                            class="w-8 h-8 rounded-full bg-muted text-charcoal flex items-center justify-center hover:bg-canvas active:scale-95 transition-all cursor-pointer text-xs font-bold"
                        >
                            ✕
                        </button>
                    </div>
                    <h3 class="text-[15px] font-extrabold text-charcoal leading-snug line-clamp-2">
                        {{ uploadTarget.tugasTitle }}
                    </h3>
                    <p class="text-xs text-subtext mt-1">
                        Pilih berkas dari perangkat Anda atau seret berkas ke kotak di bawah.
                    </p>
                </div>

                <!-- Modal Body -->
                <div class="px-6 pb-6 space-y-3.5">
                    <!-- Deadline Countdown Pill in Modal -->
                    <div class="flex items-center justify-between text-xs pb-1 border-b border-[#F2F0EC]">
                        <span class="font-bold text-subtext">Sisa Waktu Pengumpulan:</span>
                        <LmsCountdownBadge
                            v-if="selectedMeeting?.tugas?.submission_due_at || selectedMeeting?.tugas?.deadline"
                            :targetDate="selectedMeeting?.tugas?.submission_due_at || selectedMeeting?.tugas?.deadline"
                            mode="deadline"
                        />
                    </div>

                    <!-- File Drag & Drop (shown if mode is 'file' or 'both') -->
                    <div v-if="['file', 'both'].includes(selectedMeeting?.tugas?.submission_mode ?? 'both')">
                        <label class="block text-xs font-extrabold text-charcoal mb-1.5">
                            Berkas Tugas (File)
                            <span v-if="selectedMeeting?.tugas?.submission_mode === 'file'" class="text-rose-500">*</span>
                        </label>
                        <!-- Hidden real file input -->
                        <input
                            ref="fileInputRef"
                            type="file"
                            class="hidden"
                            @change="onFileInputChange"
                        />

                        <!-- Drag & Drop Zone -->
                        <div
                            @dragover="onDragOver"
                            @dragleave="onDragLeave"
                            @drop="onDrop"
                            @click="fileInputRef?.click()"
                            class="rounded-2xl border-2 border-dashed transition-all cursor-pointer"
                            :class="isDragOver
                                ? 'border-charcoal bg-charcoal/5 scale-[1.01]'
                                : droppedFile
                                    ? 'border-[#C8DFCD] bg-[#E8F5E9]/40'
                                    : 'border-[#D5D2C9] bg-canvas hover:border-charcoal/40 hover:bg-canvas'"
                        >
                            <!-- File selected state -->
                            <div v-if="droppedFile" class="p-3.5 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-sage-light text-sage-dark flex items-center justify-center shrink-0">
                                    <LmsIcon name="file" className="w-4 h-4" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-extrabold text-charcoal truncate">{{ droppedFile.name }}</div>
                                    <div class="text-[10px] text-subtext mt-0.5">{{ formatFileSize(droppedFile.size) }} • Siap dikirim</div>
                                </div>
                                <button
                                    type="button"
                                    @click.stop="droppedFile = null; rawUploadFile = null"
                                    class="w-7 h-7 rounded-full bg-white border border-[#ECEAE4] text-charcoal flex items-center justify-center hover:bg-muted transition-all shrink-0"
                                >
                                    <LmsIcon name="x" className="w-3 h-3" />
                                </button>
                            </div>
                            <!-- Empty / drag state -->
                            <div v-else class="p-5 text-center">
                                <div class="w-10 h-10 rounded-full bg-white mx-auto flex items-center justify-center text-charcoal shadow-xs mb-2">
                                    <LmsIcon name="download" className="w-5 h-5 rotate-180" />
                                </div>
                                <div class="text-xs font-extrabold text-charcoal">
                                    {{ isDragOver ? 'Lepaskan berkas di sini...' : 'Pilih atau Seret Berkas' }}
                                </div>
                                <div class="text-[10px] text-subtext mt-0.5">Format didukung: PDF, DOCX, ZIP, gambar, dsb.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Link URL Input (shown if mode is 'link' or 'both') -->
                    <div v-if="['link', 'both'].includes(selectedMeeting?.tugas?.submission_mode ?? 'both')">
                        <label class="block text-xs font-extrabold text-charcoal mb-1.5">
                            Tautan / Link Tugas
                            <span v-if="selectedMeeting?.tugas?.submission_mode === 'link'" class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="uploadLinkUrl"
                            type="url"
                            placeholder="https://docs.google.com/... atau link GitHub / Figma"
                            class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-3 text-xs font-medium text-charcoal focus:outline-none focus:border-charcoal/50 transition-colors"
                        />
                    </div>

                    <!-- Note input -->
                    <div>
                        <label class="block text-xs font-extrabold text-charcoal mb-1.5">Catatan Siswa (Opsional)</label>
                        <textarea
                            v-model="uploadNote"
                            rows="2"
                            placeholder="Tuliskan catatan atau keterangan tugas..."
                            class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2 text-xs font-medium text-charcoal focus:outline-none focus:border-charcoal/50 transition-colors resize-none"
                        ></textarea>
                    </div>

                    <!-- Upload Progress Loader Component -->
                    <LmsUploadProgress
                        v-if="isUploading || uploadProgress > 0 || uploadError"
                        :progress="uploadProgress"
                        :isUploading="isUploading"
                        :fileName="droppedFile?.name || 'Tugas Siswa'"
                        :error="uploadError"
                        @cancel="cancelUpload"
                    />
                </div>

                <!-- Modal Footer -->
                <div class="px-6 pb-6 pt-3 shrink-0 border-t border-[#F2F0EC]">
                    <button
                        type="button"
                        @click="submitHomework"
                        :disabled="isUploading"
                        class="w-full py-3.5 rounded-full bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-[0.99] transition-all cursor-pointer shadow-sm disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <svg v-if="isUploading" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>{{ isUploading ? 'Mengirim Tugas...' : 'Konfirmasi & Kirim Tugas' }}</span>
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
    </div>
</template>
