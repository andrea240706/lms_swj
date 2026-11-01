<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import LmsSkeletonLoader from '@/Components/Lms/LmsSkeletonLoader.vue';
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';
import dashboardkepsek from './dashboardkepsek.vue';
import kbmsiswakepsek from './kbmsiswakepsek.vue';
import kinerjagurukepsek from './kinerjagurukepsek.vue';

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    activeTab: {
        type: String,
        default: 'dashboard',
    },
    isSidebarCollapsed: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['openMenu', 'openProfile', 'showToast', 'toggleSidebar', 'openSettings', 'selectTab']);

// ===== SKELETON LOADER =====
const isLoading = ref(true);
const triggerLoading = (ms = 350) => {
    isLoading.value = true;
    setTimeout(() => {
        isLoading.value = false;
    }, ms);
};
onMounted(() => {
    triggerLoading(400);
});
watch(() => props.activeTab, () => {
    triggerLoading(300);
});

// Helper untuk identifikasi tab aktif (termasuk alias kompatibilitas)
const isCurrentTab = (t) => {
    const cur = props.activeTab;
    if (cur === t) return true;
    if (t === 'dashboard' && (cur === 'home' || cur === 'dashboard')) return true;
    if (t === 'kbm' && (cur === 'kbm' || cur === 'kbmsiswa')) return true;
    if (t === 'kinerja' && (cur === 'kinerja' || cur === 'jurusan')) return true;
    return false;
};

const pageTitle = computed(() => {
    const cur = props.activeTab;
    if (cur === 'dashboard' || cur === 'home') return 'Pantauan Eksekutif Sekolah';
    if (cur === 'kbm' || cur === 'kbmsiswa') return 'KBM & Kehadiran Siswa';
    if (cur === 'kinerja' || cur === 'jurusan') return 'Supervisi Kinerja & Laporan Guru';
    return 'Pantauan Eksekutif Sekolah';
});
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <LmsTopBar
            :role="role"
            :isSidebarCollapsed="isSidebarCollapsed"
            :title="pageTitle"
            @openMenu="$emit('openMenu')"
            @openProfile="$emit('openProfile')"
            @openSettings="$emit('openSettings')"
            @toggleSidebar="$emit('toggleSidebar')"
        />

        <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
            <div class="w-full max-w-[1800px] mx-auto">
                <!-- SKELETON LOADER -->
                <LmsSkeletonLoader v-if="isLoading" type="card-list" :rows="4" />

                <!-- MODULAR TABS CONTENT -->
                <template v-else>
                    <!-- 1. DASHBOARD -->
                    <dashboardkepsek
                        v-if="isCurrentTab('dashboard')"
                        @selectTab="$emit('selectTab', $event)"
                        @showToast="(msg, t, type) => $emit('showToast', msg, t, type)"
                    />

                    <!-- 2. KBM & SISWA -->
                    <kbmsiswakepsek
                        v-else-if="isCurrentTab('kbm')"
                        @showToast="(msg, t, type) => $emit('showToast', msg, t, type)"
                    />

                    <!-- 3. KINERJA GURU -->
                    <kinerjagurukepsek
                        v-else-if="isCurrentTab('kinerja')"
                        @showToast="(msg, t, type) => $emit('showToast', msg, t, type)"
                    />
                </template>
            </div>
        </div>
    </div>
</template>
