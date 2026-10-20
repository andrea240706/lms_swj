<script setup>
import { computed, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import LmsActionSheet from '@/Components/Lms/LmsActionSheet.vue';
import LmsBottomNav from '@/Components/Lms/LmsBottomNav.vue';
import LmsRoleStudio from '@/Components/Lms/LmsRoleStudio.vue';
import SiswaView from '@/Components/Lms/Roles/Siswa/SiswaView.vue';
import GuruView from '@/Components/Lms/Roles/Guru/GuruView.vue';
import WaliKelasView from '@/Components/Lms/Roles/WaliKelas/WaliKelasView.vue';
import KepsekView from '@/Components/Lms/Roles/Kepsek/KepsekView.vue';
import AdminView from '@/Components/Lms/Roles/Admin/AdminView.vue';
import SuperAdminView from '@/Components/Lms/Roles/SuperAdmin/SuperAdminView.vue';

const props = defineProps({
    roles: {
        type: Object,
        required: true,
    },
    initialRole: {
        type: String,
        default: 'siswa',
    },
    days: {
        type: Array,
        default: () => [],
    },
    initialAssignments: {
        type: Array,
        default: () => [],
    },
    currentUser: {
        type: Object,
        default: null,
    },
});

// Active Role & Tab state
const currentRoleKey = ref(props.initialRole);
const activeTab = ref(props.roles[props.initialRole]?.tabs[0]?.id || 'home');
const selectedDay = ref(17);

// Interactive features state
const studentAttendanceDone = ref(false);
const teacherGateOpen = ref(true);
const materialsLock = ref({ 1: false, 2: false, 3: true, 4: true });
const assignments = ref([...props.initialAssignments]);

// Modal / Action Sheet state
const actionSheet = ref({
    isOpen: false,
    type: '',
    data: {},
});

// View mode: 'dual' (Studio Inspector + Simulator) or 'phone_only'
const viewMode = ref('dual');

// Watch role change to reset activeTab
watch(currentRoleKey, (newRole) => {
    if (props.roles[newRole]?.tabs?.length) {
        activeTab.value = props.roles[newRole].tabs[0].id;
    }
});

const currentRole = computed(() => {
    return props.roles[currentRoleKey.value] || props.roles.siswa;
});

const selectRole = (roleKey) => {
    if (props.roles[roleKey]) {
        currentRoleKey.value = roleKey;
    }
    closeActionSheet();
};

const selectTab = (tabId) => {
    activeTab.value = tabId;
};

const selectDay = (dayNum) => {
    selectedDay.value = dayNum;
};

// Action Sheet triggers
const openActionSheet = (type, data = {}) => {
    actionSheet.value = {
        isOpen: true,
        type,
        data,
    };
};

const closeActionSheet = () => {
    actionSheet.value.isOpen = false;
};

const handleConfirmAttendance = () => {
    studentAttendanceDone.value = true;
    closeActionSheet();
};

const handleLessonSheet = (title, subject) => {
    openActionSheet('lesson', { title, subject });
};

const handleUploadModal = (assignmentId) => {
    openActionSheet('upload_assignment', { assignmentId });
};

const handleSubmitAssignment = (assignmentId) => {
    const item = assignments.value.find((a) => a.id === assignmentId);
    if (item) {
        item.status = 'Menunggu Penilaian Guru';
    }
    closeActionSheet();
};

const handleGradeModal = (studentName) => {
    openActionSheet('teacher_grade', {
        studentName,
        score: 90,
        feedback: 'Pengerjaan modul sangat rapi dan struktur endpoint konsisten.',
    });
};

const handleSubmitGrade = (sheetData) => {
    const item = assignments.value[0];
    if (item) {
        item.score = sheetData.score;
        item.feedback = sheetData.feedback;
    }
    closeActionSheet();
};

const toggleMaterialLock = (id) => {
    materialsLock.value[id] = !materialsLock.value[id];
};

const toggleTeacherGate = () => {
    teacherGateOpen.value = !teacherGateOpen.value;
};

const handleShowToast = (message) => {
    openActionSheet('toast', { message });
};

const handleLogout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <Head :title="`LMS SMK — ${currentRole.label} (${currentRole.userName})`" />

    <!-- Outer Page Container -->
    <div
        class="min-h-screen bg-[#E4E2DD] flex flex-col items-center justify-center p-0 sm:p-4 lg:p-8 gap-6 relative select-none font-sans"
    >
        <!-- Desktop Top View Switcher (when screen is large) -->
        <header
            class="hidden lg:flex items-center justify-between w-full max-w-[800px] bg-white/70 backdrop-blur-md px-5 py-2.5 rounded-full border border-white/60 shadow-xs"
        >
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 live-dot"></span>
                <span class="text-xs font-bold text-charcoal">
                    Sistem Pembelajaran SMK Terpadu
                </span>
                <span class="text-[11px] text-subtext">• Mode: {{ currentRole.label }}</span>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="viewMode = viewMode === 'dual' ? 'phone_only' : 'dual'"
                    class="text-xs font-semibold px-3 py-1 rounded-full bg-canvas hover:bg-muted transition-colors cursor-pointer text-charcoal border border-[#ECEAE4]"
                >
                    {{ viewMode === 'dual' ? 'Sembunyikan Inspector' : 'Tampilkan Inspector' }}
                </button>
                <button
                    type="button"
                    @click="handleLogout"
                    class="text-xs font-bold px-3 py-1 rounded-full bg-charcoal text-white hover:opacity-90 transition-opacity cursor-pointer"
                >
                    Logout
                </button>
            </div>
        </header>

        <!-- Main Workspace (Studio Inspector + Phone Simulator) -->
        <div class="w-full flex flex-col lg:flex-row items-center justify-center gap-6">
            <!-- LEFT CONTROL PANEL: Studio Inspector (Desktop) -->
            <LmsRoleStudio
                v-if="viewMode === 'dual'"
                :roles="roles"
                :activeRole="currentRoleKey"
                @selectRole="selectRole"
                class="hidden lg:block"
            />

            <!-- MOBILE PHONE SIMULATOR FRAME (Matches Prototype Perfectly) -->
            <main
                class="relative w-full max-w-[400px] h-[844px] bg-canvas sm:rounded-[48px] shadow-2xl border-[10px] border-white overflow-hidden flex flex-col"
            >
                <!-- Warm Top-Right Radial Glow -->
                <div
                    class="pointer-events-none absolute -top-16 -right-16 w-64 h-64 rounded-full bg-[#FFE8D6] opacity-70 blur-3xl z-0"
                ></div>

                <!-- iOS Status Bar -->
                <div
                    class="relative z-20 flex items-center justify-between px-7 pt-3 pb-2 text-charcoal text-[13px] font-bold select-none"
                >
                    <span>9:30</span>
                    <div class="flex items-center gap-1.5">
                        <!-- Signal Icon -->
                        <svg class="w-4 h-3.5" viewBox="0 0 16 14" fill="currentColor">
                            <rect x="1" y="9" width="2.2" height="4" rx="1" />
                            <rect x="4.5" y="6.5" width="2.2" height="6.5" rx="1" />
                            <rect x="8" y="3.5" width="2.2" height="9.5" rx="1" />
                            <rect x="11.5" y="1" width="2.2" height="12" rx="1" />
                        </svg>
                        <!-- Wifi -->
                        <svg
                            class="w-4 h-3.5"
                            viewBox="0 0 16 12"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                        >
                            <path d="M1.5 4.5C5.2 1.2 10.8 1.2 14.5 4.5" />
                            <path d="M4 7.5C6.3 5.5 9.7 5.5 12 7.5" />
                            <circle cx="8" cy="10.5" r="1" fill="currentColor" stroke="none" />
                        </svg>
                        <!-- Battery -->
                        <div class="flex items-center">
                            <div class="w-6 h-3 rounded-[4px] border border-charcoal/80 p-0.5 flex items-center">
                                <div class="w-full h-full bg-charcoal rounded-[2px]"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Role Viewport -->
                <div class="relative z-10 flex-1 flex flex-col overflow-hidden">
                    <!-- 1. SISWA -->
                    <SiswaView
                        v-if="currentRoleKey === 'siswa'"
                        :role="currentRole"
                        :activeTab="activeTab"
                        :selectedDay="selectedDay"
                        :days="days"
                        :assignments="assignments"
                        :studentAttendanceDone="studentAttendanceDone"
                        :materialsLock="materialsLock"
                        @selectTab="selectTab"
                        @selectDay="selectDay"
                        @openAttendanceModal="openActionSheet('student_attendance')"
                        @openLessonSheet="handleLessonSheet"
                        @openUploadModal="handleUploadModal"
                        @openMenu="openActionSheet('role_switcher')"
                        @openProfile="openActionSheet('profile', currentRole)"
                    />

                    <!-- 2. GURU -->
                    <GuruView
                        v-else-if="currentRoleKey === 'guru'"
                        :role="currentRole"
                        :activeTab="activeTab"
                        :selectedDay="selectedDay"
                        :days="days"
                        :teacherGateOpen="teacherGateOpen"
                        :materialsLock="materialsLock"
                        @selectTab="selectTab"
                        @selectDay="selectDay"
                        @toggleGate="toggleTeacherGate"
                        @toggleLock="toggleMaterialLock"
                        @openGradeModal="handleGradeModal"
                        @openMenu="openActionSheet('role_switcher')"
                        @openProfile="openActionSheet('profile', currentRole)"
                    />

                    <!-- 3. WALI KELAS -->
                    <WaliKelasView
                        v-else-if="currentRoleKey === 'wali_kelas'"
                        :role="currentRole"
                        :activeTab="activeTab"
                        @openMenu="openActionSheet('role_switcher')"
                        @openProfile="openActionSheet('profile', currentRole)"
                        @showToast="handleShowToast"
                    />

                    <!-- 4. KEPALA SEKOLAH -->
                    <KepsekView
                        v-else-if="currentRoleKey === 'kepsek'"
                        :role="currentRole"
                        :activeTab="activeTab"
                        @openMenu="openActionSheet('role_switcher')"
                        @openProfile="openActionSheet('profile', currentRole)"
                        @showToast="handleShowToast"
                    />

                    <!-- 5. ADMIN OPERATOR -->
                    <AdminView
                        v-else-if="currentRoleKey === 'admin'"
                        :role="currentRole"
                        :activeTab="activeTab"
                        @openMenu="openActionSheet('role_switcher')"
                        @openProfile="openActionSheet('profile', currentRole)"
                        @showToast="handleShowToast"
                    />

                    <!-- 6. SUPER ADMIN -->
                    <SuperAdminView
                        v-else-if="currentRoleKey === 'super_admin'"
                        :role="currentRole"
                        :activeTab="activeTab"
                        @openMenu="openActionSheet('role_switcher')"
                        @openProfile="openActionSheet('profile', currentRole)"
                        @showToast="handleShowToast"
                    />

                    <!-- Floating Pill Bottom Navigation Bar -->
                    <LmsBottomNav
                        :tabs="currentRole.tabs"
                        :activeTab="activeTab"
                        @selectTab="selectTab"
                    />
                </div>
            </main>
        </div>

        <!-- Dynamic Action Sheet / Bottom Modal -->
        <LmsActionSheet
            :isOpen="actionSheet.isOpen"
            :sheetType="actionSheet.type"
            :sheetData="actionSheet.data"
            :roles="roles"
            :activeRole="currentRoleKey"
            @close="closeActionSheet"
            @confirmAttendance="handleConfirmAttendance"
            @submitAssignment="handleSubmitAssignment"
            @submitGrade="handleSubmitGrade"
            @selectRole="selectRole"
            @logout="handleLogout"
        />
    </div>
</template>
