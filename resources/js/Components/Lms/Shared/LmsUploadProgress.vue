<script setup>
defineProps({
    fileName: {
        type: String,
        default: "",
    },
    progress: {
        type: Number,
        default: 0,
    },
    isUploading: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["cancel"]);
</script>

<template>
    <div v-if="isUploading || progress > 0 || error" class="w-full bg-surface-variant/40 rounded-xl p-3.5 border border-border">
        <div class="flex items-center justify-between mb-2 text-xs">
            <div class="flex items-center gap-2 truncate max-w-[80%]">
                <svg v-if="!error && progress < 100" class="animate-spin w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <svg v-else-if="progress >= 100 && !error" class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg v-else class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="font-medium text-charcoal truncate">{{ fileName || 'Berkas Unggahan' }}</span>
            </div>

            <div class="font-semibold" :class="error ? 'text-rose-600' : 'text-emerald-700'">
                {{ error ? 'Gagal' : `${progress}%` }}
            </div>
        </div>

        <!-- Progress Track -->
        <div class="w-full h-2 bg-border/60 rounded-full overflow-hidden relative">
            <div
                class="h-full rounded-full transition-all duration-200"
                :class="error ? 'bg-rose-500' : progress >= 100 ? 'bg-emerald-600' : 'bg-emerald-500'"
                :style="{ width: `${progress}%` }"
            ></div>
        </div>

        <div class="flex items-center justify-between mt-2 text-[11px] text-subtext">
            <span>
                <template v-if="error">{{ error }}</template>
                <template v-else-if="progress < 100">Mengunggah ke server...</template>
                <template v-else>Pengunggahan selesai, memproses berkas...</template>
            </span>
            <button
                v-if="isUploading && progress < 100"
                type="button"
                @click="emit('cancel')"
                class="text-rose-600 hover:text-rose-700 font-semibold cursor-pointer underline"
            >
                Batal
            </button>
        </div>
    </div>
</template>

