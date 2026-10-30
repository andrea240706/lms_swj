<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import LmsActionSheet from '@/Components/Lms/LmsActionSheet.vue';
import LmsBottomNav from '@/Components/Lms/LmsBottomNav.vue';
import LmsSidebar from '@/Components/Lms/LmsSidebar.vue';
import SiswaView from '@/Components/Lms/Roles/Siswa/SiswaView.vue';
import GuruView from '@/Components/Lms/Roles/Guru/GuruView.vue';
import WaliKelasView from '@/Components/Lms/Roles/WaliKelas/WaliKelasView.vue';
import KepsekView from '@/Components/Lms/Roles/Kepsek/KepsekView.vue';
import AdminView from '@/Components/Lms/Roles/Admin/AdminView.vue';

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
    calendarEvents: {
        type: Object,
        default: () => ({}),
    },
    todayDate: {
        type: String,
        default: '',
    },
    todayDayNum: {
        type: Number,
        default: 0,
    },
});

// Active Role & Tab state
const currentRoleKey = ref(props.initialRole);
const activeTab = ref(props.roles[props.initialRole]?.tabs[0]?.id || 'home');

// Initialize selected day based on today or first day of current week
const todayInDays = props.days.find((d) => d.isToday);
const selectedDay = ref(todayInDays ? todayInDays.n : (props.days[0]?.n || 18));
const selectedDayDate = ref(todayInDays ? todayInDays.date : (props.days[0]?.date || ''));

// Desktop Sidebar state (Desktop only, hidden on mobile)
const isSidebarCollapsed = ref(false);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

// Keyboard shortcut: Cmd+B / Ctrl+B to toggle sidebar
onMounted(() => {
    const handleKeydown = (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'b') {
            e.preventDefault();
            toggleSidebar();
        }
    };
    window.addEventListener('keydown', handleKeydown);
    onUnmounted(() => {
        window.removeEventListener('keydown', handleKeydown);
    });
});

// Interactive features state
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

// Guard against deprecated presensi tab
watch(
    () => activeTab.value,
    (tab) => {
        if (tab === 'presensi') {
            activeTab.value = 'home';
        }
    },
    { immediate: true }
);

const selectTab = (tabId) => {
    if (tabId === 'presensi') {
        activeTab.value = 'home';
        return;
    }
    activeTab.value = tabId === 'materi' ? 'mapel' : tabId;
};

const selectDay = (dayNum, dayDate = null) => {
    selectedDay.value = dayNum;
    if (dayDate) {
        selectedDayDate.value = dayDate;
    } else {
        const found = props.days.find((d) => d.n === dayNum);
        selectedDayDate.value = found ? found.date : '';
    }
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

const handleShowToast = (message) => {
    openActionSheet('toast', { message });
};

const handleLogout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <Head :title="`LMS SMK — ${currentRole.label} (${currentRole.userName})`" />

    <!-- Outer Page Container: Horizontal Shell on Desktop -->
    <div
        class="h-screen h-dvh max-h-screen bg-canvas flex flex-row relative font-sans text-charcoal select-none overflow-hidden"
    >
        <!-- Warm Ambient Top-Right Radial Glow -->
        <div
            class="pointer-events-none fixed -top-24 -right-24 w-96 h-96 rounded-full bg-[#FFE8D6] opacity-60 blur-3xl z-0"
        ></div>

        <!-- Desktop Sidebar (Hidden on mobile, visible on lg screens) -->
        <LmsSidebar
            class="hidden lg:flex"
            :role="currentRole"
            :activeTab="activeTab"
            :isCollapsed="isSidebarCollapsed"
            @selectTab="selectTab"
            @toggleCollapse="toggleSidebar"
            @openProfile="openActionSheet('profile', currentRole)"
            @openMenu="openActionSheet('role_switcher')"
            @openSettings="openActionSheet('settings', currentRole)"
            @logout="handleLogout"
        />

        <!-- Main Responsive Application Container: Expands across desktop screen -->
        <main class="flex-1 flex flex-col h-full overflow-hidden min-w-0 relative z-10 w-full">
            <!-- Dynamic Role Viewport -->
            <div class="flex-1 flex flex-col h-full overflow-hidden w-full">
                <!-- 1. SISWA -->
                <SiswaView
                    v-if="currentRoleKey === 'siswa'"
                    :role="currentRole"
                    :activeTab="activeTab"
                    :selectedDay="selectedDay"
                    :selectedDayDate="selectedDayDate"
                    :days="days"
                    :calendarEvents="calendarEvents"
                    :assignments="assignments"
                    :materialsLock="materialsLock"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    @selectTab="selectTab"
                    @selectDay="selectDay"
                    @toggleSidebar="toggleSidebar"
                    @openLessonSheet="handleLessonSheet"
                    @openUploadModal="handleUploadModal"
                    @openMenu="openActionSheet('role_switcher')"
                    @openProfile="openActionSheet('profile', currentRole)"
                    @openSettings="openActionSheet('settings', currentRole)"
                />

                <!-- 2. GURU -->
                <GuruView
                    v-else-if="currentRoleKey === 'guru'"
                    :role="currentRole"
                    :activeTab="activeTab"
                    :selectedDay="selectedDay"
                    :selectedDayDate="selectedDayDate"
                    :days="days"
                    :calendarEvents="calendarEvents"
                    :materialsLock="materialsLock"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    @selectTab="selectTab"
                    @selectDay="selectDay"
                    @toggleLock="toggleMaterialLock"
                    @toggleSidebar="toggleSidebar"
                    @openGradeModal="handleGradeModal"
                    @openMenu="openActionSheet('role_switcher')"
                    @openProfile="openActionSheet('profile', currentRole)"
                    @openSettings="openActionSheet('settings', currentRole)"
                />

                <!-- 3. WALI KELAS -->
                <WaliKelasView
                    v-else-if="currentRoleKey === 'wali_kelas'"
                    :role="currentRole"
                    :activeTab="activeTab"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    @selectTab="selectTab"
                    @toggleSidebar="toggleSidebar"
                    @openMenu="openActionSheet('role_switcher')"
                    @openProfile="openActionSheet('profile', currentRole)"
                    @openSettings="openActionSheet('settings', currentRole)"
                    @showToast="handleShowToast"
                />

                <!-- 4. KEPALA SEKOLAH -->
                <KepsekView
                    v-else-if="currentRoleKey === 'kepsek'"
                    :role="currentRole"
                    :activeTab="activeTab"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    @toggleSidebar="toggleSidebar"
                    @openMenu="openActionSheet('role_switcher')"
                    @openProfile="openActionSheet('profile', currentRole)"
                    @openSettings="openActionSheet('settings', currentRole)"
                    @showToast="handleShowToast"
                />

                <!-- 5. ADMINISTRATOR -->
                <AdminView
                    v-else-if="currentRoleKey === 'admin'"
                    :role="currentRole"
                    :activeTab="activeTab"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    @selectTab="selectTab"
                    @toggleSidebar="toggleSidebar"
                    @openMenu="openActionSheet('role_switcher')"
                    @openProfile="openActionSheet('profile', currentRole)"
                    @openSettings="openActionSheet('settings', currentRole)"
                    @showToast="handleShowToast"
                />

                <!-- Floating Pill Bottom Navigation Bar (Mobile / Tablet only, hidden on lg screens where Sidebar is active) -->
                <LmsBottomNav
                    class="lg:hidden"
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
            :currentUser="currentUser"
            @close="closeActionSheet"
            @submitAssignment="handleSubmitAssignment"
            @submitGrade="handleSubmitGrade"
            @selectRole="selectRole"
            @openSettings="openActionSheet('settings', currentRole)"
            @logout="handleLogout"
        />
    </div>
</template>
