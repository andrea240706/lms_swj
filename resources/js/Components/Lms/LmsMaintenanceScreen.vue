<script setup>
import LmsIcon from '@/Components/Lms/LmsIcon.vue';

const props = defineProps({
    currentRole: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['switchToAdmin']);
</script>

<template>
    <div class="flex-1 flex items-center justify-center p-6 bg-canvas relative overflow-hidden">
        <!-- Ambient Warm Glow -->
        <div class="pointer-events-none absolute -top-20 -right-20 w-80 h-80 rounded-full bg-blossom-light/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-20 w-80 h-80 rounded-full bg-amber-light/30 blur-3xl"></div>

        <div class="max-w-md w-full bg-white rounded-[32px] p-6 sm:p-8 border border-[#ECEAE4] soft-shadow text-center space-y-5 relative z-10">
            <!-- Icon Pulsa Pemeliharaan -->
            <div class="mx-auto w-16 h-16 rounded-2xl bg-blossom-light border border-[#F8B4B4] flex items-center justify-center text-blossom-dark shadow-sm">
                <LmsIcon name="settings" className="w-8 h-8 animate-spin-slow" />
            </div>

            <div class="space-y-2">
                <span class="text-[10px] font-black tracking-wider uppercase px-3 py-1 rounded-full bg-blossom-light text-blossom-dark">
                    Mode Pemeliharaan Aktif
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-charcoal">
                    Sistem Sedang Dibatasi
                </h2>
                <p class="text-xs sm:text-sm text-subtext leading-relaxed">
                    Administrator LMS sedang melakukan pemeliharaan infrastruktur dan rekapitulasi data akademik semester.
                </p>
            </div>

            <!-- Detail Info Kotak -->
            <div class="p-4 rounded-2xl bg-canvas border border-[#ECEAE4] text-left text-xs space-y-2">
                <div class="flex items-center justify-between text-subtext">
                    <span>Peran Saat Ini:</span>
                    <strong class="text-charcoal">{{ currentRole.label }} ({{ currentRole.userName }})</strong>
                </div>
                <div class="flex items-center justify-between text-subtext">
                    <span>Akses Diizinkan:</span>
                    <strong class="text-blossom-dark font-extrabold">Hanya Administrator</strong>
                </div>
                <div class="flex items-center justify-between text-subtext">
                    <span>Estimasi Kembali:</span>
                    <strong class="text-[#2E7D32]">Segera setelah admin membuka kunci</strong>
                </div>
            </div>

            <!-- Tombol Navigasi / Switch ke Admin untuk Pengujian -->
            <div class="pt-2 flex flex-col gap-2">
                <button
                    type="button"
                    @click="$emit('switchToAdmin')"
                    class="w-full py-3 px-4 rounded-xl bg-charcoal text-white text-xs font-extrabold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs flex items-center justify-center gap-2"
                >
                    <LmsIcon name="shield" className="w-4 h-4 text-white" />
                    <span>Masuk ke Akun Admin (Buka Kunci)</span>
                </button>
                <p class="text-[11px] text-subtext">
                    Buka menu Pengaturan Sistem di Administrator untuk menonaktifkan Mode Pemeliharaan.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes spin-slow {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
.animate-spin-slow {
    animation: spin-slow 12s linear infinite;
}
</style>
