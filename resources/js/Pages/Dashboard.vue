<script setup>
import { computed, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import LmsActionSheet from '@/Components/Lms/LmsActionSheet.vue';
import LmsBottomNav from '@/Components/Lms/LmsBottomNav.vue';
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
const selectedDay = ref(18);

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
    activeTab.value = tabId === 'materi' ? 'mapel' : tabId;
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

    <!-- Outer Page Container: Auto Responsive -->
    <div
        class="h-screen h-dvh max-h-screen bg-canvas flex flex-col items-center relative font-sans text-charcoal select-none overflow-hidden"
    >
        <!-- Warm Ambient Top-Right Radial Glow -->
        <div
            class="pointer-events-none fixed -top-24 -right-24 w-96 h-96 rounded-full bg-[#FFE8D6] opacity-60 blur-3xl z-0"
        ></div>

        <!-- Main Responsive Application Container (No fake phone frame, auto responsive) -->
        <main class="relative z-10 w-full max-w-2xl lg:max-w-4xl h-full flex flex-col overflow-hidden">
            <!-- Dynamic Role Viewport -->
            <div class="flex-1 flex flex-col h-full overflow-hidden">
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
