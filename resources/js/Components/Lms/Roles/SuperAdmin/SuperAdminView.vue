<script setup>
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';

defineProps({
    role: {
        type: Object,
        required: true,
    },
    activeTab: {
        type: String,
        default: 'home',
    },
});

defineEmits(['openMenu', 'openProfile', 'showToast']);
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <LmsTopBar
            :role="role"
            title="Konfigurasi &amp; Master Data"
            @openMenu="$emit('openMenu')"
            @openProfile="$emit('openProfile')"
        />

        <div class="flex-1 overflow-y-auto no-scrollbar px-6 pb-28">
            <!-- Hero Card: Core System Config -->
            <div class="hero-sage rounded-[28px] p-5 soft-shadow">
                <span
                    class="bg-white px-3 py-1 rounded-full text-[11px] font-extrabold text-charcoal shadow-2xs"
                >
                    TA 2026/2027 • Semester Ganjil
                </span>
                <h3 class="text-[21px] font-extrabold text-charcoal mt-3">
                    Sistem Utama &amp; Master
                </h3>
                <p class="text-xs text-charcoal/75 mt-1">
                    Batas Unggah: 15 MB • Format: PDF, ZIP, JPG
                </p>
            </div>

            <!-- Master Data & Security Management -->
            <h3 class="text-[15px] font-extrabold text-charcoal mt-5 mb-2.5">
                Data Master &amp; Keamanan
            </h3>
            <div class="space-y-2.5">
                <div
                    class="bg-white rounded-[24px] p-3.5 border border-[#ECEAE4] flex items-center justify-between shadow-2xs"
                >
                    <div>
                        <div class="text-sm font-extrabold text-charcoal">
                            Master Jurusan &amp; Tingkat
                        </div>
                        <div class="text-[11px] text-subtext">
                            RPL, TKJ, DKV, AKL • Kelas X, XI, XII
                        </div>
                    </div>
                    <span
                        class="text-xs font-bold px-3 py-1 rounded-full bg-canvas border border-[#E6E4DF] shrink-0 ml-2"
                    >
                        4 Jurusan
                    </span>
                </div>

                <div
                    @click="$emit('showToast', 'Panel Hak Akses & Akun Pejabat dibuka.')"
                    class="cursor-pointer bg-white rounded-[24px] p-3.5 border border-[#ECEAE4] flex items-center justify-between hover:border-charcoal/30 transition-all shadow-2xs"
                >
                    <div>
                        <div class="text-sm font-extrabold text-charcoal">
                            Kelola Akun Pejabat Sekolah
                        </div>
                        <div class="text-[11px] text-subtext">
                            1 Kepsek, 3 Admin TU, 68 Guru Aktif
                        </div>
                    </div>
                    <span
                        class="text-xs font-bold px-3 py-1 rounded-full bg-charcoal text-white shrink-0 ml-2 shadow-xs"
                    >
                        Kelola
                    </span>
                </div>

                <!-- Live Audit Trail Log -->
                <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] shadow-2xs">
                    <div class="text-xs font-extrabold text-charcoal mb-2">
                        Audit Log Aktivitas Terbaru
                    </div>
                    <div class="text-[11px] text-subtext space-y-1.5">
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-charcoal"></span>
                            <span><strong>09:14</strong> — Admin Rizky mengoreksi presensi X DKV 1</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-charcoal"></span>
                            <span><strong>08:02</strong> — Guru Budi membuka sesi presensi XII RPL 1</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-charcoal"></span>
                            <span><strong>07:45</strong> — Kepsek mengunduh Leger Semester</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
