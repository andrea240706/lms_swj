<script setup>
import { computed } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    activeTab: {
        type: String,
        required: true,
    },
    isCollapsed: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits([
    'selectTab',
    'toggleCollapse',
    'openProfile',
    'openMenu',
    'openSettings',
    'logout',
]);

const adminSections = [
    {
        title: 'Menu Utama',
        tabs: [
            { id: 'dashboard', label: 'Dashboard', icon: 'home' },
            { id: 'pengguna', label: 'Pengguna', icon: 'user' },
        ],
    },
    {
        title: 'Data Master',
        tabs: [
            { id: 'tahunajaran', label: 'Tahun Ajaran', icon: 'clock' },
            { id: 'jurusan', label: 'Jurusan (Keahlian)', icon: 'folder' },
            { id: 'rombel', label: 'Rombel (Kelas)', icon: 'book' },
            { id: 'matapelajaran', label: 'Mata Pelajaran', icon: 'task' },
        ],
    },
    {
        title: 'Penugasan & Jadwal',
        tabs: [
            { id: 'jadwalpelajaran', label: 'Jadwal Pelajaran', icon: 'clock' },
            { id: 'plottingguru', label: 'Plotting Guru Mapel', icon: 'user' },
            { id: 'walikelas', label: 'Penetapan Wali Kelas', icon: 'shield' },
        ],
    },
    {
        title: 'Informasi & Sistem',
        tabs: [
            { id: 'pengaturansistem', label: 'Pengaturan Sistem', icon: 'settings' },
        ],
    },
];

const isTabActive = (t) => {
    const cur = props.activeTab;
    if (cur === t.id) return true;
    if (t.id === 'dashboard' && (cur === 'home' || cur === 'dashboard')) return true;
    if (t.id === 'tahunajaran' && (cur === 'tahunajaran' || cur === 'master_ta')) return true;
    if (t.id === 'jurusan' && (cur === 'jurusan' || cur === 'master_jurusan' || cur === 'master')) return true;
    if (t.id === 'rombel' && (cur === 'rombel' || cur === 'master_rombel')) return true;
    if (t.id === 'matapelajaran' && (cur === 'matapelajaran' || cur === 'master_mapel')) return true;
    if (t.id === 'jadwalpelajaran' && (cur === 'jadwalpelajaran' || cur === 'penugasan_jadwal' || cur === 'penugasan')) return true;
    if (t.id === 'plottingguru' && (cur === 'plottingguru' || cur === 'penugasan_guru')) return true;
    if (t.id === 'walikelas' && (cur === 'walikelas' || cur === 'penugasan_wali')) return true;
    if (t.id === 'pengaturansistem' && (cur === 'pengaturansistem' || cur === 'sistem')) return true;
    return false;
};

const handleTabClick = (t) => {
    emit('selectTab', t.id);
};
</script>

<template>
    <aside
        class="h-full bg-white/95 backdrop-blur-md border-r border-[#ECEAE4] flex flex-col justify-between transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] select-none z-30 shrink-0 relative"
        :class="isCollapsed ? 'w-20' : 'w-64 lg:w-72'"
    >
        <!-- Top Section: Brand + User Info + Navigation -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Brand Monogram & Title -->
            <div
                class="px-4 py-4 flex items-center border-b border-[#ECEAE4]/80"
                :class="isCollapsed ? 'justify-center' : ''"
            >
                <div
                    class="flex items-center gap-3 overflow-hidden"
                    :class="isCollapsed ? 'cursor-pointer group' : ''"
                    @click="isCollapsed ? $emit('toggleCollapse') : null"
                    :title="isCollapsed ? 'Perbesar Sidebar (⌘B)' : ''"
                >
                    <div
                        class="w-10 h-10 rounded-2xl bg-charcoal text-white flex items-center justify-center font-black text-sm tracking-tight shrink-0 shadow-sm shadow-charcoal/15 ring-2 ring-charcoal/10 transition-transform group-hover:scale-105 active:scale-95"
                    >
                        SWJ
                    </div>
                    <div
                        v-if="!isCollapsed"
                        class="flex flex-col whitespace-nowrap overflow-hidden transition-all duration-200"
                    >
                        <span class="text-sm font-black text-charcoal tracking-tight leading-tight">
                            LMS SMK SWJ
                        </span>
                        <span class="text-[10px] font-bold text-subtext uppercase tracking-wider">
                            Panel Administrator
                        </span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links List with Grouped Section Headers -->
            <div class="flex-1 overflow-y-auto no-scrollbar p-3 space-y-3">
                <div
                    v-for="(section, sIdx) in adminSections"
                    :key="section.title"
                    class="space-y-1"
                >
                    <!-- Section Header (Expanded Mode) -->
                    <div
                        v-if="!isCollapsed && section.title"
                        class="px-2.5 pb-1 text-[10px] font-black uppercase tracking-wider text-subtext/70"
                        :class="sIdx > 0 ? 'pt-2' : 'pt-0.5'"
                    >
                        {{ section.title }}
                    </div>

                    <!-- Divider (Collapsed Mode between sections) -->
                    <div
                        v-else-if="isCollapsed && sIdx > 0"
                        class="my-2 border-t border-[#ECEAE4]/80 mx-1"
                    ></div>

                    <!-- Navigation Items -->
                    <button
                        v-for="t in section.tabs"
                        :key="t.id"
                        type="button"
                        @click="handleTabClick(t)"
                        class="w-full relative flex items-center transition-all duration-200 cursor-pointer rounded-2xl group"
                        :class="[
                            isCollapsed
                                ? 'h-10 justify-center'
                                : 'h-10 px-3 justify-between',
                            isTabActive(t)
                                ? 'bg-charcoal text-white font-extrabold shadow-md shadow-charcoal/15'
                                : 'text-charcoal/70 hover:text-charcoal hover:bg-canvas font-bold',
                        ]"
                        :title="isCollapsed ? t.label : ''"
                    >
                        <!-- Left Icon & Label -->
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div
                                class="shrink-0 transition-transform duration-200"
                                :class="isTabActive(t) ? 'scale-110' : 'group-hover:scale-105'"
                            >
                                <LmsIcon :name="t.icon" className="w-4 h-4" />
                            </div>

                            <span
                                v-if="!isCollapsed"
                                class="text-xs whitespace-nowrap overflow-hidden text-ellipsis text-left"
                            >
                                {{ t.label }}
                            </span>
                        </div>

                        <!-- Mini Active Indicator Bar for Collapsed mode -->
                        <span
                            v-if="isCollapsed && isTabActive(t)"
                            class="absolute -left-3 w-1.5 h-5 bg-charcoal rounded-r-full"
                        ></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Roles, Collapse Toggle & Logout -->
        <div class="p-3 border-t border-[#ECEAE4]/80 space-y-1.5 bg-canvas/40">
            <!-- Settings Trigger Button -->
            <button
                type="button"
                @click="$emit('openSettings')"
                class="w-full flex items-center rounded-2xl transition-all cursor-pointer font-bold text-xs text-charcoal/75 hover:text-charcoal hover:bg-white border border-transparent hover:border-[#ECEAE4]"
                :class="isCollapsed ? 'h-10 justify-center' : 'h-10 px-3 gap-2.5'"
                :title="isCollapsed ? 'Pengaturan LMS' : ''"
            >
                <LmsIcon name="settings" className="w-4 h-4 shrink-0 text-subtext" />
                <span v-if="!isCollapsed" class="truncate text-left">
                    Pengaturan
                </span>
            </button>

            <!-- Role Switcher Trigger Button -->
            <button
                type="button"
                @click="$emit('openMenu')"
                class="w-full flex items-center rounded-2xl transition-all cursor-pointer font-bold text-xs text-charcoal/75 hover:text-charcoal hover:bg-white border border-transparent hover:border-[#ECEAE4]"
                :class="isCollapsed ? 'h-10 justify-center' : 'h-10 px-3 gap-2.5'"
                :title="isCollapsed ? 'Ganti Role LMS' : ''"
            >
                <LmsIcon name="user" className="w-4 h-4 shrink-0 text-subtext" />
                <span v-if="!isCollapsed" class="truncate text-left">
                    Ganti Role (Preview)
                </span>
            </button>
        </div>
    </aside>
</template>
