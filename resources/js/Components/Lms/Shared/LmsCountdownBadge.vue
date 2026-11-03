<script setup>
import { computed } from "vue";
import { useCountdown } from "@/Utils/useCountdown";

const props = defineProps({
    targetDate: {
        type: [String, Date, null],
        required: true,
    },
    mode: {
        type: String,
        default: "deadline", // 'deadline' | 'release'
    },
    showIcon: {
        type: Boolean,
        default: true,
    },
});

const { isPending, isExpired, formatted, urgency } = useCountdown(computed(() => props.targetDate));

const badgeClasses = computed(() => {
    if (props.mode === "release") {
        if (isPending.value) {
            return "bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800";
        }
        return "bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800";
    }

    // mode === 'deadline'
    if (isExpired.value) {
        return "bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800";
    }
    if (urgency.value === "critical") {
        return "bg-rose-50 text-rose-700 border-rose-200 animate-pulse";
    }
    if (urgency.value === "warning") {
        return "bg-amber-50 text-amber-800 border-amber-200";
    }
    return "bg-emerald-50 text-emerald-800 border-emerald-200";
});
</script>

<template>
    <div
        v-if="targetDate"
        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border transition-all"
        :class="badgeClasses"
    >
        <svg
            v-if="showIcon && mode === 'release'"
            class="w-3.5 h-3.5 shrink-0"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>

        <svg
            v-else-if="showIcon && mode === 'deadline'"
            class="w-3.5 h-3.5 shrink-0"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>

        <span v-if="mode === 'release'">
            <template v-if="isPending">Rilis dalam {{ formatted }}</template>
            <template v-else>Telah Dibuka</template>
        </span>

        <span v-else>
            <template v-if="isExpired">Waktu Berakhir</template>
            <template v-else>Sisa {{ formatted }}</template>
        </span>
    </div>
</template>

