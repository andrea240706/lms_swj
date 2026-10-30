<script setup>
import LmsIcon from '@/Components/Lms/LmsIcon.vue';

const props = defineProps({
    tabs: {
        type: Array,
        required: true,
    },
    activeTab: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['selectTab']);

const getTabLabel = (t) => {
    if (t.id === 'mapel' || t.id === 'materi' || t.label?.toLowerCase() === 'materi') {
        return 'Mapel';
    }
    return t.label;
};

const isTabActive = (t) => {
    if (props.activeTab === t.id) return true;
    if (t.id === 'master' && props.activeTab.startsWith('master_')) return true;
    if (t.id === 'penugasan' && props.activeTab.startsWith('penugasan_')) return true;
    if ((props.activeTab === 'mapel' || props.activeTab === 'materi') && (t.id === 'mapel' || t.id === 'materi')) {
        return true;
    }
    return false;
};

const handleTabClick = (t) => {
    const targetId = t.id === 'materi' ? 'mapel' : t.id;
    emit('selectTab', targetId);
};
</script>

<template>
    <div class="fixed bottom-4 left-1/2 -translate-x-1/2 w-[calc(100%-2rem)] max-w-md z-40 select-none pointer-events-auto">
        <nav
            class="bg-white/95 backdrop-blur-md border border-[#ECEAE4] rounded-full p-1.5 sm:p-2 pill-nav-shadow flex items-center justify-between"
        >
            <button
                v-for="t in tabs"
                :key="t.id"
                type="button"
                @click="handleTabClick(t)"
                class="group relative flex items-center justify-center rounded-full text-xs font-bold transition-all duration-300 ease-[cubic-bezier(0.25,1,0.5,1)] cursor-pointer overflow-hidden active:scale-95 shrink-0"
                :class="
                    isTabActive(t)
                        ? 'h-11 px-4 bg-charcoal text-white shadow-lg shadow-charcoal/20 ring-1 ring-white/10'
                        : 'w-11 h-11 text-charcoal/65 hover:text-charcoal hover:bg-black/5'
                "
                :title="getTabLabel(t)"
            >
                <!-- Icon with smooth scale animation -->
                <LmsIcon
                    :name="t.icon"
                    className="shrink-0 transition-transform duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
                    :class="isTabActive(t) ? 'w-4 h-4 scale-110' : 'w-5 h-5 group-hover:scale-105'"
                />

                <!-- Animated Label with sliding expansion -->
                <span
                    class="overflow-hidden whitespace-nowrap font-extrabold transition-all duration-300 ease-[cubic-bezier(0.25,1,0.5,1)]"
                    :class="
                        isTabActive(t)
                            ? 'max-w-28 ml-2 opacity-100 translate-x-0'
                            : 'max-w-0 ml-0 opacity-0 -translate-x-2'
                    "
                >
                    {{ getTabLabel(t) }}
                </span>
            </button>
        </nav>
    </div>
</template>
