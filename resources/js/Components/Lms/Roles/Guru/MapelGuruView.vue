<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import LmsIcon from "@/Components/Lms/LmsIcon.vue";
import LmsSkeletonLoader from "@/Components/Lms/LmsSkeletonLoader.vue";
import LmsTopBar from "@/Components/Lms/LmsTopBar.vue";
import LmsTinyMceEditor from "@/Components/Lms/Shared/LmsTinyMceEditor.vue";
import LmsVideoEmbed from "@/Components/Lms/Shared/LmsVideoEmbed.vue";
import LmsCountdownBadge from "@/Components/Lms/Shared/LmsCountdownBadge.vue";
import { guruMapelList } from "./mapelGuruData";
import { notify } from "@/Utils/notify";

const props = defineProps({
    role: { type: Object, required: true },
    isSidebarCollapsed: { type: Boolean, default: false },
});

const emit = defineEmits(["openMenu", "openProfile", "toggleSidebar", "openSettings"]);

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

// Reactive clone of data
const mapelList = ref(JSON.parse(JSON.stringify(guruMapelList)));

// Navigation state: 'list' | 'mapel_detail' | 'meeting_detail'
const currentView = ref("list");
watch(currentView, () => {
    triggerLoading(350);
});
const selectedMapelId = ref(null);
const selectedMeetingId = ref(null);

// Grading modal state
const isGradingOpen = ref(false);
const gradingStudent = ref(null);
const gradingScore = ref(null);
const gradingFeedback = ref("");

// Toast via Laravel Notify
const showToast = (msg, type = "success", title = "Berhasil") => {
    notify.show({ message: msg, type, title, model: "toast" });
};

// Computed
const selectedMapel = computed(() =>
    mapelList.value.find((m) => m.id === selectedMapelId.value) || null
);
const selectedMeeting = computed(() => {
    if (!selectedMapel.value) return null;
    return selectedMapel.value.meetings.find((mt) => mt.id === selectedMeetingId.value) || null;
});

// ===== MATERIALS =====
const materials = computed(() => {
    const value = page.props.materials;
    return Array.isArray(value) ? value : value?.data ?? [];
});

const canManageMaterials = computed(() => Boolean(page.props.canManageMaterials));

const materialConfig = computed(() => page.props.materialConfig ?? {
    max_files: 10,
    max_file_size_kb: 51200,
    max_total_size_kb: 204800,
    allowed_extensions: [],
});

const meetingMaterials = computed(() => {
    if (!selectedMapel.value || !selectedMeeting.value) return [];

    return materials.value
        .filter((material) =>
            material.mapel_key === selectedMapel.value.id
            && Number(material.meeting_number) === Number(selectedMeeting.value.number)
        )
        .sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
});

const materialFileCount = computed(() =>
    meetingMaterials.value.reduce((total, material) => total + (material.files?.length ?? 0), 0)
);

const materialAccept = computed(() =>
    (materialConfig.value.allowed_extensions ?? []).map((extension) => `.${extension}`).join(",")
);

const isMaterialModalOpen = ref(false);
const editingMaterial = ref(null);
const fileSelectionError = ref("");
const materiSourceType = ref("file"); // 'file' | 'link' | 'editor'
const addFileInput = ref(null);
const addFileTarget = ref(null);
const materialForm = useForm({
    meeting_id: null,
    mapel_key: "",
    meeting_number: null,
    title: "",
    description: "",
    content_html: "",
    link_url: "",
    available_from: "",
    is_published: true,
    files: [],
});
const fileUploadForm = useForm({ files: [] });

// Assignment Form & State
const isAssignmentModalOpen = ref(false);
const assignmentForm = useForm({
    meeting_id: null,
    title: "",
    instructions_html: "",
    attachment_link_url: "",
    submission_mode: "both",
    available_from: "",
    submission_opens_at: "",
    submission_due_at: "",
    allow_late_submission: true,
    allow_revision: true,
    is_published: true,
    files: [],
});

const openEditAssignment = () => {
    if (! selectedMeeting.value) return;
    assignmentForm.clearErrors();
    assignmentForm.meeting_id = selectedMeeting.value.id;
    assignmentForm.title = selectedMeeting.value.tugas?.title ?? `Tugas Praktik Pertemuan ${selectedMeeting.value.number}`;
    assignmentForm.instructions_html = selectedMeeting.value.tugas?.instructions_html ?? "<p>Kerjakan tugas ini sesuai petunjuk dan kumpulkan sebelum batas waktu berakhir.</p>";
    assignmentForm.attachment_link_url = selectedMeeting.value.tugas?.attachment_link_url ?? "";
    assignmentForm.submission_mode = selectedMeeting.value.tugas?.submission_mode ?? "both";
    assignmentForm.available_from = selectedMeeting.value.tugas?.available_from ? selectedMeeting.value.tugas.available_from.substring(0, 16) : "";
    assignmentForm.submission_due_at = selectedMeeting.value.tugas?.submission_due_at ? selectedMeeting.value.tugas.submission_due_at.substring(0, 16) : "";
    assignmentForm.allow_late_submission = selectedMeeting.value.tugas?.allow_late_submission ?? true;
    assignmentForm.allow_revision = selectedMeeting.value.tugas?.allow_revision ?? true;
    assignmentForm.is_published = true;
    assignmentForm.files = [];
    isAssignmentModalOpen.value = true;
};

const submitAssignment = () => {
    if (assignmentForm.processing) return;
    assignmentForm.post(route("guru.tugas.store"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            isAssignmentModalOpen.value = false;
            showToast("Pengaturan tugas berhasil disimpan.");
            if (selectedMeeting.value?.tugas) {
                selectedMeeting.value.tugas.title = assignmentForm.title;
                selectedMeeting.value.tugas.instructions_html = assignmentForm.instructions_html;
                selectedMeeting.value.tugas.submission_mode = assignmentForm.submission_mode;
                selectedMeeting.value.tugas.allow_late_submission = assignmentForm.allow_late_submission;
                selectedMeeting.value.tugas.deadline = assignmentForm.submission_due_at ? new Date(assignmentForm.submission_due_at).toLocaleDateString("id-ID", { day: "numeric", month: "short", year: "numeric", hour: "2-digit", minute: "2-digit" }) + " WIB" : selectedMeeting.value.tugas.deadline;
            }
        },
        onError: () => {
            showToast("Periksa kembali isian formulir tugas.", "error", "Gagal");
        },
    });
};

// Video Form & State
const isVideoModalOpen = ref(false);
const videoForm = useForm({
    meeting_id: null,
    title: "",
    url: "",
    available_from: "",
    is_published: true,
});

const openEditVideo = () => {
    if (! selectedMeeting.value) return;
    videoForm.clearErrors();
    videoForm.meeting_id = selectedMeeting.value.id;
    videoForm.title = selectedMeeting.value.video?.title ?? `Video Pembelajaran Pertemuan ${selectedMeeting.value.number}`;
    videoForm.url = selectedMeeting.value.video?.url ?? "";
    videoForm.available_from = selectedMeeting.value.video?.available_from ? selectedMeeting.value.video.available_from.substring(0, 16) : "";
    videoForm.is_published = true;
    isVideoModalOpen.value = true;
};

const submitVideo = () => {
    if (videoForm.processing) return;
    videoForm.post(route("guru.video.store"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            isVideoModalOpen.value = false;
            showToast("Materi video berhasil disimpan.");
            if (selectedMeeting.value?.video) {
                selectedMeeting.value.video.title = videoForm.title;
                selectedMeeting.value.video.url = videoForm.url;
            }
        },
        onError: () => {
            showToast("Pastikan tautan video valid (YouTube, TikTok, Instagram, dsb).", "error", "Gagal");
        },
    });
};

const confirmState = ref({
    open: false,
    title: "",
    message: "",
    action: null,
});



const formatBytes = (bytes) => {
    const value = Number(bytes ?? 0);
    if (! Number.isFinite(value) || value <= 0) return "0 KB";
    if (value >= 1024 * 1024) return `${(value / (1024 * 1024)).toFixed(1)} MB`;
    return `${Math.max(1, Math.round(value / 1024))} KB`;
};

const formatMaterialDate = (value) => {
    if (! value) return "-";
    return new Date(value).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });
};

const fileTone = (extension) => {
    const value = String(extension ?? "").toLowerCase();

    if (value === "pdf") return "bg-[#FF4444]/10 text-[#FF4444]";
    if (["doc", "docx", "odt", "rtf", "txt", "md"].includes(value)) return "bg-sky-light text-sky-dark";
    if (["xls", "xlsx", "csv", "ods"].includes(value)) return "bg-sage-light text-sage-dark";
    if (["ppt", "pptx", "odp"].includes(value)) return "bg-[#FFF3E0] text-[#E65100]";
    if (["jpg", "jpeg", "png", "webp", "gif"].includes(value)) return "bg-blossom-light text-blossom-dark";
    if (["mp3", "wav", "ogg", "mp4", "webm", "mov"].includes(value)) return "bg-[#EDE7F6] text-[#5E35B1]";

    return "bg-muted text-charcoal";
};

const fileExtensionOf = (file) => {
    const parts = String(file.name ?? "").split(".");
    return parts.length > 1 ? parts.pop().toLowerCase() : "";
};

const validateSelectedFiles = (files, material = null) => {
    const allowedExtensions = materialConfig.value.allowed_extensions ?? [];
    const maxFiles = Number(materialConfig.value.max_files ?? 10);
    const maxFileSize = Number(materialConfig.value.max_file_size_kb ?? 51200) * 1024;
    const maxTotalSize = Number(materialConfig.value.max_total_size_kb ?? 204800) * 1024;
    const existingCount = material?.files?.length ?? 0;
    const existingSize = (material?.files ?? []).reduce((total, file) => total + Number(file.size ?? 0), 0);

    if (existingCount + files.length > maxFiles) {
        return `Maksimal ${maxFiles} file per materi. Saat ini sudah ada ${existingCount} file.`;
    }

    const invalidFile = files.find((file) => ! allowedExtensions.includes(fileExtensionOf(file)));

    if (invalidFile) {
        return `Format .${fileExtensionOf(invalidFile) || "tidak dikenal"} tidak diizinkan.`;
    }

    const oversizedFile = files.find((file) => file.size > maxFileSize);

    if (oversizedFile) {
        return `Ukuran ${oversizedFile.name} melebihi batas ${formatBytes(maxFileSize)}.`;
    }

    const totalSize = existingSize + files.reduce((total, file) => total + Number(file.size ?? 0), 0);

    if (totalSize > maxTotalSize) {
        return `Total ukuran file melebihi batas ${formatBytes(maxTotalSize)}.`;
    }

    return "";
};

const openCreateMaterial = () => {
    editingMaterial.value = null;
    materialForm.reset();
    materialForm.clearErrors();
    materialForm.meeting_id = selectedMeeting.value?.id ?? null;
    materialForm.mapel_key = selectedMapel.value?.id ?? "";
    materialForm.meeting_number = selectedMeeting.value?.number ?? null;
    materialForm.content_html = "";
    materialForm.link_url = "";
    materialForm.available_from = "";
    materialForm.is_published = true;
    materialForm.files = [];
    materiSourceType.value = "file";
    fileSelectionError.value = "";
    isMaterialModalOpen.value = true;
};

const openEditMaterial = (material) => {
    editingMaterial.value = material;
    materialForm.clearErrors();
    materialForm.meeting_id = material.meeting_id ?? selectedMeeting.value?.id ?? null;
    materialForm.mapel_key = material.mapel_key;
    materialForm.meeting_number = material.meeting_number;
    materialForm.title = material.title;
    materialForm.description = material.description ?? "";
    materialForm.content_html = material.content_html ?? "";
    materialForm.link_url = material.link_url ?? "";
    materialForm.available_from = material.available_from ? material.available_from.substring(0, 16) : "";
    materialForm.is_published = Boolean(material.is_published);
    materialForm.files = [];
    if (material.content_html) {
        materiSourceType.value = "editor";
    } else if (material.link_url) {
        materiSourceType.value = "link";
    } else {
        materiSourceType.value = "file";
    }
    fileSelectionError.value = "";
    isMaterialModalOpen.value = true;
};

const closeMaterialModal = () => {
    if (materialForm.processing) return;
    isMaterialModalOpen.value = false;
};

const handleMaterialFiles = (event) => {
    const files = Array.from(event.target.files ?? []);
    const error = validateSelectedFiles(files);

    if (error) {
        fileSelectionError.value = error;
        materialForm.files = [];
        event.target.value = "";
        return;
    }

    fileSelectionError.value = "";
    materialForm.files = files;
};

const removeSelectedMaterialFile = (index) => {
    materialForm.files = materialForm.files.filter((_, fileIndex) => fileIndex !== index);
};

const submitMaterial = () => {
    if (materialForm.processing) return;

    if (materiSourceType.value === "link") {
        materialForm.files = [];
        materialForm.content_html = "";
    } else if (materiSourceType.value === "editor") {
        materialForm.files = [];
        materialForm.link_url = "";
    } else if (materiSourceType.value === "file") {
        materialForm.link_url = "";
        materialForm.content_html = "";
    }

    const options = {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            isMaterialModalOpen.value = false;
            materialForm.reset();
        },
        onError: (errors) => {
            const firstError = Object.values(errors)[0];
            if (firstError) showToast(firstError, "error", "Gagal");
        },
    };

    if (editingMaterial.value) {
        materialForm.patch(route("guru.materi.update", editingMaterial.value.id), options);
        return;
    }

    materialForm.post(route("guru.materi.store"), {
        ...options,
        forceFormData: true,
    });
};

const askConfirm = (title, message, action) => {
    confirmState.value = {
        open: true,
        title,
        message,
        action,
    };
};

const closeConfirm = () => {
    confirmState.value.open = false;
};

const confirmAction = () => {
    const action = confirmState.value.action;
    closeConfirm();
    if (action) action();
};

const requestDeleteMaterial = (material) => {
    askConfirm(
        "Hapus materi?",
        `Materi "${material.title}" beserta semua filenya akan dihapus.`,
        () => {
            router.delete(route("guru.materi.destroy", material.id), {
                preserveScroll: true,
                preserveState: true,
                onError: () => showToast("Materi gagal dihapus."),
            });
        }
    );
};

const requestDeleteFile = (material, file) => {
    askConfirm(
        "Hapus file?",
        `File "${file.original_name}" akan dihapus dari materi.`,
        () => {
            router.delete(route("guru.materi.files.destroy", [material.id, file.id]), {
                preserveScroll: true,
                preserveState: true,
                onError: (errors) => {
                    const firstError = Object.values(errors)[0];
                    showToast(firstError ?? "File gagal dihapus.");
                },
            });
        }
    );
};

const triggerAddFile = (material) => {
    addFileTarget.value = material;
    addFileInput.value?.click();
};

const handleAddFiles = (event) => {
    const files = Array.from(event.target.files ?? []);
    const material = addFileTarget.value;

    if (! material || files.length === 0) {
        event.target.value = "";
        addFileTarget.value = null;
        return;
    }

    const error = validateSelectedFiles(files, material);

    if (error) {
        showToast(error);
        event.target.value = "";
        addFileTarget.value = null;
        return;
    }

    fileUploadForm.clearErrors();
    fileUploadForm.files = files;
    fileUploadForm.post(route("guru.materi.files.store", material.id), {
        forceFormData: true,
        preserveScroll: true,
        preserveState: true,
        onError: (errors) => {
            const firstError = Object.values(errors)[0];
            showToast(firstError ?? "File gagal diunggah.");
        },
        onFinish: () => {
            fileUploadForm.reset();
            event.target.value = "";
            addFileTarget.value = null;
        },
    });
};

const ungradedStudents = computed(() => {
    if (!selectedMeeting.value) return [];
    return selectedMeeting.value.students.filter((s) => {
        return Boolean(s.submittedFile) && s.score === null;
    });
});

const gradedStudents = computed(() => {
    if (!selectedMeeting.value) return [];
    return selectedMeeting.value.students.filter((s) => s.score !== null);
});

const notSubmittedStudents = computed(() => {
    if (!selectedMeeting.value) return [];
    return selectedMeeting.value.students.filter((s) => {
        return !s.submittedFile;
    });
});

// Navigation
const openMapel = (mapelId) => {
    selectedMapelId.value = mapelId;
    currentView.value = "mapel_detail";
};
const openMeeting = (meetingId) => {
    selectedMeetingId.value = meetingId;
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
        meeting.tugas.ungradedCount = meeting.students.filter((s) => {
            return Boolean(s.submittedFile) && s.score === null;
        }).length;
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
                    @openSettings="$emit('openSettings')"
                    class="shrink-0"
                />
                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
                    <div class="w-full max-w-[1800px] mx-auto">
                    <LmsSkeletonLoader v-if="isLoading" type="mapel-list" :cards="4" />
                    <template v-else>
                    <!-- Header Banner -->
                    <div class="hero-blossom rounded-[28px] py-7 px-6 sm:py-8 sm:px-8 soft-shadow mb-4 relative overflow-hidden flex flex-col justify-center min-h-[115px]">
                        <div class="relative z-10">
                            <h2 class="text-[20px] sm:text-[23px] font-black text-charcoal leading-tight">Kelola Mapel & Penilaian</h2>
                            <p class="text-xs sm:text-[13px] text-charcoal/80 mt-1.5 max-w-xl leading-relaxed">
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
                    </template>
                    </div>
                </div>
            </div>
        </template>

        <!-- ============================================ -->
        <!-- LEVEL 2: DAFTAR PERTEMUAN DALAM MAPEL       -->
        <!-- ============================================ -->
        <template v-else-if="currentView === 'mapel_detail'">
            <div class="flex-1 flex flex-col h-full overflow-hidden">
                <header class="px-4 sm:px-6 lg:px-8 xl:px-10 pt-4 pb-3 shrink-0">
                <div class="w-full max-w-[1800px] mx-auto flex items-center justify-between">
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
                </div>
                </header>

                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
                <div class="w-full max-w-[1800px] mx-auto">
                    <LmsSkeletonLoader v-if="isLoading" type="meeting-list" :rows="5" />
                    <template v-else>
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
                    </template>
                </div>
                </div>
            </div>
        </template>

        <!-- ============================================ -->
        <!-- LEVEL 3: DETAIL PERTEMUAN                   -->
        <!-- ============================================ -->
        <template v-else-if="currentView === 'meeting_detail'">
            <div class="flex-1 flex flex-col h-full overflow-hidden">
                <header class="px-4 sm:px-6 lg:px-8 xl:px-10 pt-4 pb-3 shrink-0">
                <div class="w-full max-w-[1800px] mx-auto flex items-center gap-3">
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
                </div>
                </header>



            <!-- Meeting Detail Content: Unified Feed Layout -->
            <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
                <div class="w-full max-w-[1800px] mx-auto space-y-4">
                    <LmsSkeletonLoader v-if="isLoading" type="meeting-detail" />
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
                                <span
                                    class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full"
                                    :class="selectedMeeting.isLocked ? 'bg-muted text-subtext' : 'bg-sage-light text-sage-dark'"
                                >
                                    {{ selectedMeeting.isLocked ? 'Akses Siswa Dikunci' : 'Akses Terbuka' }}
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
                                    <span>Kelas {{ selectedMeeting.rombel || selectedMapel.rombel?.[0] || 'XII RPL' }}</span>
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
                                            <div><span class="font-bold text-charcoal">Jadwal Sesi:</span> {{ selectedMeeting.date }} • {{ selectedMeeting.time || selectedMapel.dayTime }}</div>
                                            <div><span class="font-bold text-charcoal">Ruang Kelas:</span> {{ selectedMapel.room }} (Pengajar: {{ $page.props.auth?.user?.name || 'Budi Santoso, S.Kom' }})</div>
                                        </div>

                                        <div class="mt-3 flex items-center gap-2 text-[11px] text-[#2E7D32] bg-[#E8F5E9]/60 px-3 py-1.5 rounded-xl border border-[#C8DFCD] w-fit">
                                            <LmsIcon name="check" className="w-3.5 h-3.5" />
                                            <span class="font-bold">Kehadiran siswa otomatis tercatat dalam sistem presensi harian kelas</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. TUGAS (PENUGASAN & PENILAIAN SISWA) -->
                            <div class="p-5 sm:p-6 hover:bg-[#FAF9F6]/50 transition-colors">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="w-9 h-9 rounded-xl bg-blossom-light text-blossom-dark flex items-center justify-center shrink-0 mt-0.5">
                                        <LmsIcon name="task" className="w-4 h-4" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-3 flex-wrap">
                                            <div class="flex items-center gap-2 flex-wrap min-w-0">
                                                <h3 class="text-sm font-extrabold text-charcoal leading-snug">
                                                    {{ selectedMeeting.tugas?.title || ('Tugas Pertemuan ' + selectedMeeting.number) }}
                                                </h3>
                                                <span
                                                    class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full"
                                                    :class="selectedMeeting.tugas?.deadlineStatus === 'Sudah Ditutup' ? 'bg-muted text-subtext' : 'bg-[#FFF3E0] text-[#E65100] border border-[#FFE0B2]'"
                                                >
                                                    {{ selectedMeeting.tugas?.deadlineStatus || 'Tugas Aktif' }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                                                <LmsCountdownBadge
                                                    v-if="selectedMeeting.tugas?.submission_due_at || selectedMeeting.tugas?.deadline"
                                                    :targetDate="selectedMeeting.tugas?.submission_due_at || selectedMeeting.tugas?.deadline"
                                                    mode="deadline"
                                                />
                                                <button
                                                    type="button"
                                                    @click="openEditAssignment"
                                                    class="px-3 py-1.5 rounded-full bg-charcoal text-white text-[11px] font-bold hover:opacity-90 active:scale-95 transition-all flex items-center gap-1.5 cursor-pointer"
                                                >
                                                    <LmsIcon name="edit" className="w-3 h-3" />
                                                    <span>Atur Tugas</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Metadata Dates & Instructions -->
                                        <div class="mt-2.5 text-xs space-y-1.5 text-charcoal/80">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-bold text-charcoal">Batas Waktu (Due):</span>
                                                <span class="font-semibold">{{ selectedMeeting.tugas?.deadline }}</span>
                                                <span
                                                    class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                                                    :class="selectedMeeting.tugas?.deadlineStatus === 'Sudah Ditutup' ? 'bg-muted text-subtext' : 'bg-red-50 text-red-600 border border-red-100'"
                                                >
                                                    {{ selectedMeeting.tugas?.deadlineStatus }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2 text-[11px] font-medium text-subtext">
                                                <span class="font-bold text-charcoal">Format yang Diizinkan:</span>
                                                <span class="px-2 py-0.5 rounded bg-muted text-charcoal font-semibold">
                                                    {{ selectedMeeting.tugas?.submission_mode === 'file' ? '📁 Berkas File Saja' : selectedMeeting.tugas?.submission_mode === 'link' ? '🔗 Tautan Link Saja' : '📂 Berkas File atau Tautan' }}
                                                </span>
                                            </div>

                                            <div>
                                                <span class="font-bold text-charcoal">Instruksi Tugas:</span>
                                                <div v-if="selectedMeeting.tugas?.instructions_html" class="mt-1.5 p-3.5 bg-canvas/60 rounded-xl border border-[#ECEAE4] prose prose-sm max-w-none text-charcoal" v-html="selectedMeeting.tugas.instructions_html"></div>
                                                <span v-else class="ml-1 text-subtext">Kerjakan tugas secara mandiri atau kelompok sesuai instruksi pada lembar soal berikut.</span>
                                            </div>
                                        </div>

                                        <!-- Lembar Soal Lampiran -->
                                        <div v-if="selectedMeeting.tugas?.file" class="mt-3 flex items-center gap-2">
                                            <button
                                                type="button"
                                                @click="handleDownload(selectedMeeting.tugas.file)"
                                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:border-charcoal/40 active:scale-[0.99] transition-all cursor-pointer"
                                            >
                                                <LmsIcon name="book" className="w-3.5 h-3.5 text-subtext" />
                                                <span>{{ selectedMeeting.tugas.file }}</span>
                                                <span class="text-[10px] text-subtext font-normal">(1.2 MB)</span>
                                                <LmsIcon name="download" className="w-3 h-3 text-charcoal ml-1" />
                                            </button>
                                        </div>

                                        <!-- Panel Rekapitulasi & Penilaian Pengumpulan Siswa -->
                                        <div class="mt-4 rounded-2xl border border-[#ECEAE4] p-4 bg-canvas/70">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-xs font-extrabold text-charcoal">
                                                    Status Pengumpulan: {{ selectedMeeting.tugas?.totalSubmitted }}/{{ selectedMeeting.tugas?.totalStudents }} Siswa
                                                </span>
                                                <span
                                                    class="text-[11px] font-bold"
                                                    :class="selectedMeeting.tugas?.ungradedCount > 0 ? 'text-[#E65100]' : 'text-sage-dark'"
                                                >
                                                    {{ selectedMeeting.tugas?.ungradedCount > 0 ? selectedMeeting.tugas.ungradedCount + ' Belum Dinilai' : 'Semua Sudah Dinilai ✓' }}
                                                </span>
                                            </div>
                                            <div class="w-full h-1.5 bg-muted rounded-full overflow-hidden mb-4">
                                                <div
                                                    class="h-full bg-charcoal rounded-full transition-all"
                                                    :style="'width:' + (selectedMeeting.tugas?.totalStudents ? (selectedMeeting.tugas.totalSubmitted / selectedMeeting.tugas.totalStudents) * 100 : 0) + '%'"
                                                ></div>
                                            </div>

                                            <!-- Submissions Grid: Ungraded & Graded -->
                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3.5">
                                                <!-- Menunggu Penilaian -->
                                                <div class="space-y-2.5">
                                                    <h4 class="text-xs font-extrabold text-charcoal flex items-center gap-1.5">
                                                        <span class="w-2 h-2 rounded-full bg-[#E65100]"></span>
                                                        Menunggu Penilaian ({{ ungradedStudents.length }})
                                                    </h4>
                                                    <div v-if="ungradedStudents.length === 0" class="text-center py-5 text-subtext text-xs font-medium bg-white rounded-xl border border-[#ECEAE4]">
                                                        Tidak ada tugas yang perlu dinilai 🎉
                                                    </div>
                                                    <div
                                                        v-for="student in ungradedStudents"
                                                        :key="student.id"
                                                        class="bg-white rounded-xl p-3 border border-[#ECEAE4] soft-shadow"
                                                    >
                                                        <div class="flex items-center gap-2.5">
                                                            <img :src="student.avatar" :alt="student.name" class="w-8 h-8 rounded-full object-cover border border-[#E6E4DF] shrink-0" />
                                                            <div class="flex-1 min-w-0">
                                                                <div class="text-xs font-extrabold text-charcoal truncate">{{ student.name }}</div>
                                                                <div class="text-[10px] mt-0.5 font-semibold" :class="submissionStatusColor(student.status)">
                                                                    {{ student.status }} • {{ student.submittedAt }}
                                                                </div>
                                                                <div v-if="student.submittedFile" class="flex items-center gap-2 mt-0.5">
                                                                    <button type="button" @click.stop="handleDownload(student.submittedFile)" class="text-[10px] font-bold text-charcoal underline underline-offset-1 truncate max-w-[170px]">
                                                                        📎 {{ student.submittedFile }}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <button
                                                                type="button"
                                                                @click="openGradeModal(student)"
                                                                class="px-3 py-1.5 rounded-full bg-charcoal text-white text-[10px] font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer shrink-0"
                                                            >
                                                                Beri Nilai
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Sudah Dinilai + Belum Mengumpulkan -->
                                                <div class="space-y-2.5">
                                                    <h4 class="text-xs font-extrabold text-charcoal flex items-center gap-1.5">
                                                        <span class="w-2 h-2 rounded-full bg-sage-dark"></span>
                                                        Sudah Dinilai ({{ gradedStudents.length }})
                                                    </h4>
                                                    <div
                                                        v-for="student in gradedStudents"
                                                        :key="student.id"
                                                        class="bg-white rounded-xl p-3 border border-[#ECEAE4] soft-shadow flex items-center gap-2.5"
                                                    >
                                                        <img :src="student.avatar" :alt="student.name" class="w-8 h-8 rounded-full object-cover border border-[#E6E4DF] shrink-0" />
                                                        <div class="flex-1 min-w-0">
                                                            <div class="text-xs font-extrabold text-charcoal truncate">{{ student.name }}</div>
                                                            <div class="text-[10px] text-subtext mt-0.5">Dinilai: {{ student.gradedAt }}</div>
                                                            <div v-if="student.feedback" class="text-[10px] italic text-charcoal/70 mt-0.5 line-clamp-1">"{{ student.feedback }}"</div>
                                                        </div>
                                                        <div class="flex flex-col items-center shrink-0 mr-1">
                                                            <span class="w-8 h-8 rounded-full bg-sage-light text-sage-dark font-black text-xs flex items-center justify-center">
                                                                {{ student.score }}
                                                            </span>
                                                            <span class="text-[9px] font-bold text-sage-dark">Grade {{ student.grade }}</span>
                                                        </div>
                                                        <button
                                                            type="button"
                                                            @click="openGradeModal(student)"
                                                            class="w-7 h-7 rounded-full bg-muted text-charcoal flex items-center justify-center hover:bg-canvas active:scale-95 transition-all cursor-pointer shrink-0"
                                                            title="Edit Nilai"
                                                        >
                                                            <LmsIcon name="task" className="w-3 h-3" />
                                                        </button>
                                                    </div>

                                                    <template v-if="notSubmittedStudents.length > 0">
                                                        <h4 class="text-xs font-extrabold text-charcoal flex items-center gap-1.5 mt-3">
                                                            <span class="w-2 h-2 rounded-full bg-subtext"></span>
                                                            Belum Mengumpulkan ({{ notSubmittedStudents.length }})
                                                        </h4>
                                                        <div
                                                            v-for="student in notSubmittedStudents"
                                                            :key="student.id"
                                                            class="bg-muted rounded-xl p-3 border border-[#ECEAE4] flex items-center gap-2.5 opacity-70"
                                                        >
                                                            <img :src="student.avatar" :alt="student.name" class="w-8 h-8 rounded-full object-cover border border-[#E6E4DF] shrink-0 grayscale" />
                                                            <div class="flex-1 min-w-0">
                                                                <div class="text-xs font-extrabold text-charcoal truncate">{{ student.name }}</div>
                                                                <div class="text-[10px] text-subtext mt-0.5">Belum ada pengumpulan</div>
                                                            </div>
                                                            <span class="text-[10px] font-bold text-subtext bg-white border border-[#ECEAE4] px-2 py-0.5 rounded-full shrink-0">-</span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. MATERI PEMBELAJARAN -->
                            <div class="p-5 sm:p-6 hover:bg-[#FAF9F6]/50 transition-colors">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="w-9 h-9 rounded-xl bg-sky-light text-sky-dark flex items-center justify-center shrink-0 mt-0.5">
                                        <LmsIcon name="book" className="w-4 h-4" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-3 flex-wrap">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h3 class="text-sm font-extrabold text-charcoal leading-snug">
                                                    Materi Pertemuan {{ selectedMeeting.number }}
                                                </h3>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-canvas text-subtext border border-[#ECEAE4]">
                                                    {{ meetingMaterials.length ? materialFileCount + ' Berkas Terlampir' : selectedMeeting.materi?.pages || 'Materi Belajar' }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <button
                                                    v-if="canManageMaterials"
                                                    type="button"
                                                    @click="openCreateMaterial"
                                                    class="px-3 py-1.5 rounded-full bg-charcoal text-white text-[11px] font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer flex items-center gap-1.5"
                                                >
                                                    <LmsIcon name="plus" className="w-3.5 h-3.5" />
                                                    <span>Tambah Materi</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Dynamic Uploaded Materials -->
                                        <template v-if="meetingMaterials.length">
                                            <div v-for="material in meetingMaterials" :key="material.id" class="mt-3.5 pt-3.5 border-t border-[#F2F0EC] first:mt-3 first:pt-0 first:border-0">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <h4 class="text-xs font-extrabold text-charcoal">{{ material.title }}</h4>
                                                            <span
                                                                class="text-[9px] font-extrabold px-2 py-0.5 rounded-full"
                                                                :class="material.is_published ? 'bg-sage-light text-sage-dark' : 'bg-muted text-subtext'"
                                                            >
                                                                {{ material.is_published ? 'Publik' : 'Draft' }}
                                                            </span>
                                                            <LmsCountdownBadge
                                                                v-if="material.available_from"
                                                                :targetDate="material.available_from"
                                                                mode="release"
                                                            />
                                                        </div>
                                                        <p v-if="material.description" class="text-xs text-charcoal/80 mt-1 leading-relaxed">
                                                            {{ material.description }}
                                                        </p>
                                                        <div v-if="material.link_url" class="mt-2">
                                                            <a
                                                                :href="material.link_url"
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-[#ECEAE4] rounded-xl text-[11px] font-bold text-sky-dark hover:bg-canvas transition-colors"
                                                            >
                                                                <LmsIcon name="link" className="w-3 h-3 text-sky-dark" />
                                                                <span>{{ material.link_url }}</span>
                                                                <span class="text-[10px]">↗</span>
                                                            </a>
                                                        </div>
                                                        <div v-if="material.content_html" class="mt-2 p-3 bg-white rounded-xl border border-[#ECEAE4] text-xs text-charcoal prose prose-sm max-w-none">
                                                            <div v-html="material.content_html"></div>
                                                        </div>
                                                        <div class="text-[10px] font-semibold text-subtext mt-1.5">
                                                            {{ material.files?.length ?? 0 }} file &middot; {{ material.human_total_size || '0 KB' }}
                                                            <span class="mx-1">&middot;</span>
                                                            Diperbarui {{ formatMaterialDate(material.updated_at) }}
                                                        </div>
                                                    </div>
                                                    <div v-if="canManageMaterials" class="flex items-center gap-1 shrink-0">
                                                        <button
                                                            type="button"
                                                            @click="openEditMaterial(material)"
                                                            class="w-7 h-7 rounded-full bg-white text-charcoal border border-[#ECEAE4] flex items-center justify-center hover:bg-muted active:scale-95 transition-all cursor-pointer"
                                                            title="Edit materi"
                                                        >
                                                            <LmsIcon name="edit" className="w-3 h-3" />
                                                        </button>
                                                        <button
                                                            type="button"
                                                            @click="triggerAddFile(material)"
                                                            class="w-7 h-7 rounded-full bg-white text-charcoal border border-[#ECEAE4] flex items-center justify-center hover:bg-muted active:scale-95 transition-all cursor-pointer"
                                                            title="Tambah file"
                                                        >
                                                            <LmsIcon name="upload" className="w-3 h-3" />
                                                        </button>
                                                        <button
                                                            type="button"
                                                            @click="requestDeleteMaterial(material)"
                                                            class="w-7 h-7 rounded-full bg-white text-[#C62828] border border-[#ECEAE4] flex items-center justify-center hover:bg-[#FFEBEE] active:scale-95 transition-all cursor-pointer"
                                                            title="Hapus materi"
                                                        >
                                                            <LmsIcon name="trash" className="w-3 h-3" />
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Files of Material -->
                                                <div v-if="material.files?.length" class="mt-2.5 flex flex-wrap gap-2">
                                                    <div
                                                        v-for="file in material.files"
                                                        :key="file.id"
                                                        class="flex items-center gap-2 rounded-xl bg-white border border-[#ECEAE4] px-3 py-1.5 shadow-2xs"
                                                    >
                                                        <span
                                                            class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-[9px] shrink-0"
                                                            :class="fileTone(file.extension)"
                                                        >
                                                            {{ file.extension }}
                                                        </span>
                                                        <div class="min-w-0 max-w-[180px]">
                                                            <div class="text-xs font-bold text-charcoal truncate">{{ file.original_name }}</div>
                                                            <div class="text-[10px] text-subtext">{{ file.human_size }}</div>
                                                        </div>
                                                        <a
                                                            :href="file.download_url"
                                                            class="w-7 h-7 rounded-full bg-muted text-charcoal flex items-center justify-center hover:bg-canvas active:scale-95 transition-all cursor-pointer shrink-0 ml-1"
                                                            title="Unduh file"
                                                        >
                                                            <LmsIcon name="download" className="w-3 h-3" />
                                                        </a>
                                                        <button
                                                            v-if="canManageMaterials"
                                                            type="button"
                                                            @click="requestDeleteFile(material, file)"
                                                            class="w-7 h-7 rounded-full bg-white text-[#C62828] border border-[#ECEAE4] flex items-center justify-center hover:bg-[#FFEBEE] active:scale-95 transition-all cursor-pointer shrink-0"
                                                            title="Hapus file"
                                                        >
                                                            <LmsIcon name="trash" className="w-3 h-3" />
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Fallback Static Material if no DB materials -->
                                        <template v-else>
                                            <div class="mt-2 text-xs space-y-1">
                                                <div>
                                                    <span class="font-bold text-charcoal">Judul Materi:</span>
                                                    <span class="ml-1 text-charcoal font-semibold">{{ selectedMeeting.materi?.title }}</span>
                                                </div>
                                                <p class="text-xs text-charcoal/80 mt-1 leading-relaxed">
                                                    {{ selectedMeeting.summary || 'Panduan komprehensif alur materi pembelajaran untuk pertemuan ini.' }}
                                                </p>
                                            </div>

                                            <div class="mt-3 flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    @click="handleDownload(selectedMeeting.materi?.title)"
                                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-[#ECEAE4] text-xs font-bold text-charcoal hover:border-charcoal/40 active:scale-[0.99] transition-all cursor-pointer shadow-2xs"
                                                >
                                                    <span class="w-6 h-6 rounded-lg bg-red-100 text-red-600 flex items-center justify-center font-black text-[9px] shrink-0">
                                                        PDF
                                                    </span>
                                                    <span>{{ selectedMeeting.materi?.title }}</span>
                                                    <span class="text-[10px] text-subtext font-normal">({{ selectedMeeting.materi?.size }})</span>
                                                    <LmsIcon name="download" className="w-3.5 h-3.5 text-charcoal ml-1" />
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. VIDEO PEMBELAJARAN -->
                            <div class="p-5 sm:p-6 hover:bg-[#FAF9F6]/50 transition-colors">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="w-9 h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0 mt-0.5">
                                        <LmsIcon name="play" className="w-4 h-4 fill-current ml-0.5" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-3 flex-wrap">
                                            <div class="flex items-center gap-2 flex-wrap min-w-0">
                                                <h3 class="text-sm font-extrabold text-charcoal leading-snug">
                                                    Video: {{ selectedMeeting.video?.title || 'Video Pembelajaran' }}
                                                </h3>
                                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-200">
                                                    {{ selectedMeeting.video?.channel ? 'YouTube' : 'Video' }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <LmsCountdownBadge
                                                    v-if="selectedMeeting.video?.available_from"
                                                    :targetDate="selectedMeeting.video?.available_from"
                                                    mode="release"
                                                />
                                                <button
                                                    type="button"
                                                    @click="openEditVideo"
                                                    class="px-3 py-1.5 rounded-full bg-charcoal text-white text-[11px] font-bold hover:opacity-90 active:scale-95 transition-all flex items-center gap-1.5 cursor-pointer shrink-0"
                                                >
                                                    <LmsIcon name="edit" className="w-3 h-3" />
                                                    <span>{{ selectedMeeting.video?.url ? 'Ubah Video' : 'Tambah Video' }}</span>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="mt-1 text-xs text-subtext font-medium flex items-center gap-2 flex-wrap">
                                            <span v-if="selectedMeeting.video?.channel">🎥 {{ selectedMeeting.video.channel }}</span>
                                            <span v-if="selectedMeeting.video?.duration">• Durasi {{ selectedMeeting.video.duration }}</span>
                                            <span v-else class="text-[11px]">Mendukung YouTube, TikTok, Instagram, Google Drive, &amp; MP4</span>
                                        </div>

                                        <!-- Video Embed / Player -->
                                        <div v-if="selectedMeeting.video?.url" class="mt-3.5 max-w-2xl rounded-2xl overflow-hidden border border-[#ECEAE4] bg-black shadow-sm">
                                            <LmsVideoEmbed :url="selectedMeeting.video.url" :title="selectedMeeting.video.title" />
                                        </div>

                                        <div v-else class="mt-3 rounded-2xl border-2 border-dashed border-[#ECEAE4] bg-canvas/60 px-4 py-6 text-center">
                                            <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto mb-2">
                                                <LmsIcon name="play" className="w-4 h-4 fill-current ml-0.5" />
                                            </div>
                                            <p class="text-xs font-extrabold text-charcoal">Belum ada video pembelajaran</p>
                                            <p class="text-[11px] text-subtext mt-0.5">Sematkan tautan video dari YouTube, TikTok, Instagram, atau Google Drive.</p>
                                            <button
                                                type="button"
                                                @click="openEditVideo"
                                                class="mt-2.5 px-3.5 py-1.5 rounded-full bg-charcoal text-white text-[11px] font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer inline-flex items-center gap-1.5"
                                            >
                                                <LmsIcon name="plus" className="w-3 h-3" />
                                                <span>Tambah Video</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>
            </div>
            </div>
        </template>

        <input
            ref="addFileInput"
            type="file"
            multiple
            :accept="materialAccept"
            class="hidden"
            @change="handleAddFiles"
        />

        <!-- ============================================ -->
        <!-- MATERIAL FORM MODAL                         -->
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
                v-if="isMaterialModalOpen"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-4"
                @click.self="closeMaterialModal"
            >
                <div class="bg-white rounded-[28px] w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 shadow-2xl">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div>
                            <div class="text-[11px] font-bold text-subtext uppercase tracking-wider">
                                {{ editingMaterial ? 'Edit Materi' : 'Materi Baru' }}
                            </div>
                            <h3 class="text-[17px] font-extrabold text-charcoal mt-0.5">
                                {{ editingMaterial ? editingMaterial.title : 'Unggah Materi Pembelajaran' }}
                            </h3>
                            <p class="text-[11px] text-subtext mt-1">
                                {{ selectedMapel?.name }} &middot; Pertemuan {{ selectedMeeting?.number }}
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="closeMaterialModal"
                            class="w-8 h-8 rounded-full bg-muted text-charcoal flex items-center justify-center hover:bg-canvas active:scale-95 transition-all cursor-pointer shrink-0"
                            title="Tutup"
                        >
                            <LmsIcon name="x" className="w-4 h-4" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-extrabold text-charcoal mb-2">Judul Materi</label>
                            <input
                                v-model="materialForm.title"
                                type="text"
                                maxlength="150"
                                placeholder="Contoh: Modul Routing & Controller Laravel"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-3 text-xs font-medium text-charcoal placeholder:text-subtext/70 focus:outline-none focus:border-charcoal/50 transition-colors"
                            />
                            <p v-if="materialForm.errors.title" class="text-[10px] font-semibold text-[#C62828] mt-1">
                                {{ materialForm.errors.title }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-charcoal mb-2">Deskripsi (Opsional)</label>
                            <textarea
                                v-model="materialForm.description"
                                rows="3"
                                maxlength="2000"
                                placeholder="Ringkasan singkat isi materi..."
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-3 text-xs font-medium text-charcoal placeholder:text-subtext/70 focus:outline-none focus:border-charcoal/50 transition-colors resize-none"
                            ></textarea>
                            <p v-if="materialForm.errors.description" class="text-[10px] font-semibold text-[#C62828] mt-1">
                                {{ materialForm.errors.description }}
                            </p>
                        </div>

                        <label class="flex items-center justify-between gap-3 bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-3 cursor-pointer">
                            <span>
                                <span class="block text-xs font-extrabold text-charcoal">Publikasikan ke siswa</span>
                                <span class="block text-[10px] text-subtext mt-0.5">Materi draft hanya terlihat oleh guru.</span>
                            </span>
                            <input v-model="materialForm.is_published" type="checkbox" class="w-4 h-4 rounded border-[#D7D4CD] text-charcoal focus:ring-charcoal" />
                        </label>

                        <div v-if="!editingMaterial">
                            <label class="block text-xs font-extrabold text-charcoal mb-2">File Materi</label>
                            <label class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-[#ECEAE4] bg-canvas/60 px-4 py-7 text-center cursor-pointer hover:border-charcoal/30 transition-colors">
                                <span class="w-10 h-10 rounded-full bg-white text-charcoal border border-[#ECEAE4] flex items-center justify-center">
                                    <LmsIcon name="upload" className="w-4 h-4" />
                                </span>
                                <span class="text-xs font-extrabold text-charcoal">Pilih file materi</span>
                                <span class="text-[10px] text-subtext">Bisa pilih beberapa file sekaligus</span>
                                <input
                                    type="file"
                                    multiple
                                    :accept="materialAccept"
                                    class="hidden"
                                    @change="handleMaterialFiles"
                                />
                            </label>
                            <p class="text-[10px] text-subtext mt-2">
                                Maksimal {{ materialConfig.max_files }} file, {{ formatBytes((materialConfig.max_file_size_kb ?? 51200) * 1024) }} per file.
                            </p>
                            <p v-if="fileSelectionError" class="text-[10px] font-semibold text-[#C62828] mt-1">
                                {{ fileSelectionError }}
                            </p>
                            <p v-if="materialForm.errors.files" class="text-[10px] font-semibold text-[#C62828] mt-1">
                                {{ materialForm.errors.files }}
                            </p>
                            <p v-if="materialForm.errors['files.0']" class="text-[10px] font-semibold text-[#C62828] mt-1">
                                {{ materialForm.errors['files.0'] }}
                            </p>

                            <div v-if="materialForm.files.length" class="mt-3 space-y-1.5">
                                <div
                                    v-for="(file, index) in materialForm.files"
                                    :key="`${file.name}-${index}`"
                                    class="flex items-center gap-2 rounded-xl bg-white border border-[#ECEAE4] px-2.5 py-2"
                                >
                                    <span class="w-8 h-8 rounded-lg bg-muted text-charcoal flex items-center justify-center shrink-0">
                                        <LmsIcon name="file" className="w-4 h-4" />
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-[11px] font-bold text-charcoal truncate">{{ file.name }}</div>
                                        <div class="text-[10px] text-subtext">{{ formatBytes(file.size) }}</div>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removeSelectedMaterialFile(index)"
                                        class="w-7 h-7 rounded-full bg-muted text-charcoal flex items-center justify-center hover:bg-canvas active:scale-95 transition-all cursor-pointer shrink-0"
                                        title="Hapus dari pilihan"
                                    >
                                        <LmsIcon name="x" className="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-else class="rounded-2xl bg-canvas border border-[#ECEAE4] px-4 py-3 text-[11px] text-subtext">
                            Untuk menambah file, gunakan tombol unggah pada kartu materi setelah menyimpan perubahan.
                        </div>

                        <div v-if="materialForm.progress" class="space-y-1">
                            <div class="flex items-center justify-between text-[10px] font-bold text-subtext">
                                <span>Mengunggah...</span>
                                <span>{{ materialForm.progress.percentage }}%</span>
                            </div>
                            <div class="w-full h-1.5 bg-muted rounded-full overflow-hidden">
                                <div class="h-full bg-charcoal rounded-full transition-all" :style="{ width: `${materialForm.progress.percentage}%` }"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-6">
                        <button
                            type="button"
                            @click="closeMaterialModal"
                            class="flex-1 py-3 rounded-2xl bg-muted text-charcoal text-sm font-bold hover:bg-canvas active:scale-95 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="submitMaterial"
                            :disabled="materialForm.processing"
                            class="flex-1 py-3 rounded-2xl bg-charcoal text-white text-sm font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            {{ materialForm.processing ? 'Menyimpan...' : (editingMaterial ? 'Simpan Perubahan' : 'Unggah Materi') }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- ============================================ -->
        <!-- CONFIRM MODAL                               -->
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
                v-if="confirmState.open"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-4"
                @click.self="closeConfirm"
            >
                <div class="bg-white rounded-[28px] w-full max-w-sm p-6 shadow-2xl">
                    <div class="w-11 h-11 rounded-full bg-[#FFEBEE] text-[#C62828] flex items-center justify-center mb-4">
                        <LmsIcon name="trash" className="w-5 h-5" />
                    </div>
                    <h3 class="text-[17px] font-extrabold text-charcoal">{{ confirmState.title }}</h3>
                    <p class="text-xs text-subtext mt-2 leading-relaxed">{{ confirmState.message }}</p>
                    <div class="flex gap-2 mt-6">
                        <button
                            type="button"
                            @click="closeConfirm"
                            class="flex-1 py-3 rounded-2xl bg-muted text-charcoal text-sm font-bold hover:bg-canvas active:scale-95 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="confirmAction"
                            class="flex-1 py-3 rounded-2xl bg-[#C62828] text-white text-sm font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

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

                    <!-- File Submission -->
                    <div v-if="gradingStudent?.submittedFile" class="bg-canvas rounded-2xl p-3 border border-[#ECEAE4] mb-3 flex items-center gap-2">
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

        <!-- ============================================ -->
        <!-- ASSIGNMENT SETTINGS MODAL                   -->
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
                v-if="isAssignmentModalOpen"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-4"
                @click.self="isAssignmentModalOpen = false"
            >
                <div class="bg-white rounded-[28px] w-full max-w-xl max-h-[90vh] overflow-y-auto p-6 shadow-2xl">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div>
                            <div class="text-[11px] font-bold text-subtext uppercase tracking-wider">
                                Kelola Tugas Pertemuan
                            </div>
                            <h3 class="text-[17px] font-extrabold text-charcoal mt-0.5">
                                Pengaturan Tugas & Batas Waktu
                            </h3>
                            <p class="text-[11px] text-subtext mt-1">
                                Pertemuan {{ selectedMeeting?.number }} &middot; {{ selectedMapel?.name }}
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="isAssignmentModalOpen = false"
                            class="w-8 h-8 rounded-full bg-muted text-charcoal flex items-center justify-center hover:bg-canvas active:scale-95 transition-all cursor-pointer shrink-0"
                        >
                            <LmsIcon name="x" className="w-4 h-4" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-extrabold text-charcoal mb-2">Judul Tugas</label>
                            <input
                                v-model="assignmentForm.title"
                                type="text"
                                placeholder="Contoh: Tugas Praktik 01 - Pembuatan Controller"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-3 text-xs font-medium text-charcoal focus:outline-none focus:border-charcoal/50 transition-colors"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-charcoal mb-2">Instruksi Tugas (TinyMCE)</label>
                            <LmsTinyMceEditor v-model="assignmentForm.instructions_html" height="260" placeholder="Tuliskan petunjuk pengerjaan tugas secara rinci..." />
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-charcoal mb-2">Format Pengumpulan Siswa</label>
                            <div class="grid grid-cols-3 gap-2 bg-canvas p-1 rounded-2xl border border-[#ECEAE4]">
                                <button
                                    type="button"
                                    @click="assignmentForm.submission_mode = 'file'"
                                    class="py-2.5 rounded-xl text-xs font-bold transition-all flex flex-col items-center justify-center gap-1 cursor-pointer"
                                    :class="assignmentForm.submission_mode === 'file' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    <span>📁 Berkas Saja</span>
                                    <span class="text-[9px] font-medium opacity-70">Wajib upload file</span>
                                </button>
                                <button
                                    type="button"
                                    @click="assignmentForm.submission_mode = 'link'"
                                    class="py-2.5 rounded-xl text-xs font-bold transition-all flex flex-col items-center justify-center gap-1 cursor-pointer"
                                    :class="assignmentForm.submission_mode === 'link' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    <span>🔗 Tautan Saja</span>
                                    <span class="text-[9px] font-medium opacity-70">Wajib input link</span>
                                </button>
                                <button
                                    type="button"
                                    @click="assignmentForm.submission_mode = 'both'"
                                    class="py-2.5 rounded-xl text-xs font-bold transition-all flex flex-col items-center justify-center gap-1 cursor-pointer"
                                    :class="assignmentForm.submission_mode === 'both' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    <span>📂 Fleksibel</span>
                                    <span class="text-[9px] font-medium opacity-70">File atau Link</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-extrabold text-charcoal mb-2">Buka Akses Tugas</label>
                                <input
                                    v-model="assignmentForm.available_from"
                                    type="datetime-local"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-3 text-xs font-medium text-charcoal focus:outline-none focus:border-charcoal/50 transition-colors"
                                />
                                <span class="text-[10px] text-subtext mt-1 block">Kapan tugas mulai bisa diakses siswa</span>
                            </div>
                            <div>
                                <label class="block text-xs font-extrabold text-charcoal mb-2">Batas Tenggat (Deadline)</label>
                                <input
                                    v-model="assignmentForm.submission_due_at"
                                    type="datetime-local"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-3 text-xs font-medium text-charcoal focus:outline-none focus:border-charcoal/50 transition-colors"
                                />
                                <span class="text-[10px] text-subtext mt-1 block">Waktu hitung mundur bagi siswa</span>
                            </div>
                        </div>

                        <div class="space-y-2 pt-1">
                            <label class="flex items-center justify-between gap-3 bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-3 cursor-pointer">
                                <div>
                                    <span class="block text-xs font-extrabold text-charcoal">Izinkan Pengumpulan Terlambat</span>
                                    <span class="block text-[10px] text-subtext mt-0.5">Siswa tetap dapat mengirim setelah deadline dengan status "Terlambat".</span>
                                </div>
                                <input v-model="assignmentForm.allow_late_submission" type="checkbox" class="w-4 h-4 rounded border-[#D7D4CD] text-charcoal focus:ring-charcoal" />
                            </label>

                            <label class="flex items-center justify-between gap-3 bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-3 cursor-pointer">
                                <div>
                                    <span class="block text-xs font-extrabold text-charcoal">Izinkan Revisi Jawaban</span>
                                    <span class="block text-[10px] text-subtext mt-0.5">Siswa dapat mengunggah ulang tugas sebelum dinilai guru.</span>
                                </div>
                                <input v-model="assignmentForm.allow_revision" type="checkbox" class="w-4 h-4 rounded border-[#D7D4CD] text-charcoal focus:ring-charcoal" />
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-6">
                        <button
                            type="button"
                            @click="isAssignmentModalOpen = false"
                            class="flex-1 py-3 rounded-2xl bg-muted text-charcoal text-sm font-bold hover:bg-canvas active:scale-95 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="submitAssignment"
                            :disabled="assignmentForm.processing"
                            class="flex-1 py-3 rounded-2xl bg-charcoal text-white text-sm font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer disabled:opacity-60"
                        >
                            {{ assignmentForm.processing ? 'Menyimpan...' : 'Simpan Tugas' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- ============================================ -->
        <!-- VIDEO SETTINGS MODAL                        -->
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
                v-if="isVideoModalOpen"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-4"
                @click.self="isVideoModalOpen = false"
            >
                <div class="bg-white rounded-[28px] w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 shadow-2xl">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div>
                            <div class="text-[11px] font-bold text-subtext uppercase tracking-wider">
                                Materi Video Pembelajaran
                            </div>
                            <h3 class="text-[17px] font-extrabold text-charcoal mt-0.5">
                                Tambah / Ubah Video
                            </h3>
                            <p class="text-[11px] text-subtext mt-1">
                                Mendukung YouTube, TikTok, Instagram Reels, Google Drive, & MP4
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="isVideoModalOpen = false"
                            class="w-8 h-8 rounded-full bg-muted text-charcoal flex items-center justify-center hover:bg-canvas active:scale-95 transition-all cursor-pointer shrink-0"
                        >
                            <LmsIcon name="x" className="w-4 h-4" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-extrabold text-charcoal mb-2">Judul Video</label>
                            <input
                                v-model="videoForm.title"
                                type="text"
                                placeholder="Contoh: Tutorial Praktik Routing & Controller"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-3 text-xs font-medium text-charcoal focus:outline-none focus:border-charcoal/50 transition-colors"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-charcoal mb-2">Tautan URL Video</label>
                            <input
                                v-model="videoForm.url"
                                type="url"
                                placeholder="https://www.youtube.com/watch?v=... atau link TikTok / IG"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-3 text-xs font-medium text-charcoal focus:outline-none focus:border-charcoal/50 transition-colors"
                            />
                        </div>

                        <!-- Live Preview Video -->
                        <div v-if="videoForm.url" class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-subtext">Pratinjau Pemutar Video (Live Preview):</label>
                            <LmsVideoEmbed :url="videoForm.url" :title="videoForm.title || 'Pratinjau Video'" />
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-charcoal mb-2">Jadwal Akses Video Siswa</label>
                            <input
                                v-model="videoForm.available_from"
                                type="datetime-local"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-3 text-xs font-medium text-charcoal focus:outline-none focus:border-charcoal/50 transition-colors"
                            />
                            <p class="text-[10px] text-subtext mt-1">
                                Jika diatur, siswa dapat melihat video ini di daftar namun belum bisa memutarnya sebelum waktu tiba.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-6">
                        <button
                            type="button"
                            @click="isVideoModalOpen = false"
                            class="flex-1 py-3 rounded-2xl bg-muted text-charcoal text-sm font-bold hover:bg-canvas active:scale-95 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="submitVideo"
                            :disabled="videoForm.processing"
                            class="flex-1 py-3 rounded-2xl bg-charcoal text-white text-sm font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer disabled:opacity-60"
                        >
                            {{ videoForm.processing ? 'Menyimpan...' : 'Simpan Video' }}
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
