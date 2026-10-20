<script setup>
import LmsIcon from '@/Components/Lms/LmsIcon.vue';

defineProps({
    tabs: {
        type: Array,
        required: true,
    },
    activeTab: {
        type: String,
        required: true,
    },
});

defineEmits(['selectTab']);
</script>

<template>
    <div class="absolute bottom-4 inset-x-5 z-30">
        <nav
            class="bg-white/95 backdrop-blur-md border border-[#ECEAE4] rounded-full p-2 pill-nav-shadow flex items-center justify-between"
        >
            <template v-for="t in tabs" :key="t.id">
                <!-- Active Tab: Expanded Pill -->
                <button
                    v-if="activeTab === t.id"
                    type="button"
                    @click="$emit('selectTab', t.id)"
                    class="bg-charcoal text-white px-4 py-2.5 rounded-full flex items-center gap-2 text-xs font-bold transition-all shadow-sm cursor-pointer"
                >
                    <LmsIcon :name="t.icon" className="w-4 h-4" />
                    <span>{{ t.label }}</span>
                </button>

                <!-- Inactive Tab: Circular Button -->
                <button
                    v-else
                    type="button"
                    @click="$emit('selectTab', t.id)"
                    class="w-11 h-11 rounded-full flex items-center justify-center text-charcoal/70 hover:text-charcoal hover:bg-muted/60 transition-all cursor-pointer"
                    :title="t.label"
                >
                    <LmsIcon :name="t.icon" className="w-5 h-5" />
                </button>
            </template>
        </nav>
    </div>
</template>
