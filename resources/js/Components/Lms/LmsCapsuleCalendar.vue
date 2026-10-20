<script setup>
defineProps({
    title: {
        type: String,
        default: 'Class Schedule',
    },
    days: {
        type: Array,
        default: () => [
            { d: 'Sun', n: 17 },
            { d: 'Mon', n: 18 },
            { d: 'Tue', n: 19 },
            { d: 'Wed', n: 20 },
            { d: 'Thu', n: 21 },
            { d: 'Fri', n: 22 },
            { d: 'Sat', n: 23 },
        ],
    },
    selectedDay: {
        type: Number,
        default: 17,
    },
});

defineEmits(['selectDay']);
</script>

<template>
    <div class="mt-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-[16px] font-extrabold text-charcoal">
                {{ title }}
            </h3>
            <span
                class="text-xs font-bold text-charcoal bg-white border border-[#ECEAE4] px-3 py-1 rounded-full flex items-center gap-1 shadow-2xs"
            >
                Okt 2026
                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd"
                    />
                </svg>
            </span>
        </div>

        <div class="grid grid-cols-7 gap-1.5">
            <button
                v-for="item in days"
                :key="item.n"
                type="button"
                @click="$emit('selectDay', item.n)"
                class="py-3 rounded-full flex flex-col items-center justify-center transition-all cursor-pointer"
                :class="
                    selectedDay === item.n
                        ? 'bg-charcoal text-white shadow-md scale-[1.03]'
                        : 'bg-white text-charcoal border border-[#ECEAE4] hover:border-charcoal/30'
                "
            >
                <span
                    class="text-[10px] font-semibold"
                    :class="selectedDay === item.n ? 'text-white/75' : 'text-subtext'"
                >
                    {{ item.d }}
                </span>
                <span class="text-[15px] font-extrabold mt-0.5">{{ item.n }}</span>
                <div class="flex gap-0.5 mt-1">
                    <span
                        class="w-1 h-1 rounded-full"
                        :class="selectedDay === item.n ? 'bg-white' : 'bg-charcoal/25'"
                    ></span>
                    <span
                        class="w-1 h-1 rounded-full"
                        :class="selectedDay === item.n ? 'bg-white/70' : 'bg-transparent'"
                    ></span>
                </div>
            </button>
        </div>
    </div>
</template>
