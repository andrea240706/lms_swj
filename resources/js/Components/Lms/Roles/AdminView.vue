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
            title="Panel Operator Sekolah"
            @openMenu="$emit('openMenu')"
            @openProfile="$emit('openProfile')"
        />

        <div class="flex-1 overflow-y-auto no-scrollbar px-6 pb-28">
            <!-- Hero Card: Operational Metrics -->
            <div class="hero-lavender rounded-[28px] p-5 soft-shadow">
                <span
                    class="bg-white px-3 py-1 rounded-full text-[11px] font-extrabold text-charcoal shadow-2xs"
                >
                    Operasional Akademik
                </span>
                <h3 class="text-[21px] font-extrabold text-charcoal mt-3">
                    1.240 Siswa &amp; 68 Guru
                </h3>
                <p class="text-xs text-charcoal/75 mt-1">
                    Plotting Rombel, Reset Sandi, &amp; Koreksi Presensi
                </p>
            </div>

            <!-- Operator Quick Actions -->
            <h3 class="text-[15px] font-extrabold text-charcoal mt-5 mb-2.5">
                Tindakan Cepat Operator
            </h3>
            <div class="space-y-2.5">
                <div
                    @click="$emit('showToast', 'Sandi sementara [SMK-8821] berhasil dibuat untuk akun siswa terkait.')"
                    class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between hover:border-charcoal/30 transition-all"
                >
                    <div>
                        <div class="text-sm font-extrabold text-charcoal">
                            Reset Kata Sandi Pengguna
                        </div>
                        <div class="text-[11px] text-subtext">
                            Buat sandi sementara untuk Siswa / Guru
                        </div>
                    </div>
                    <span
                        class="px-3 py-1.5 rounded-full bg-charcoal text-white text-xs font-bold shrink-0 ml-2 shadow-xs"
                    >
                        Reset
                    </span>
                </div>

                <div
                    @click="$emit('showToast', 'Status Alpa siswa berhasil dikoreksi menjadi Sakit (Surat Dokter Terverifikasi).')"
                    class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between hover:border-charcoal/30 transition-all"
                >
                    <div>
                        <div class="text-sm font-extrabold text-charcoal">
                            Koreksi Presensi Siswa
                        </div>
                        <div class="text-[11px] text-subtext">
                            Sesuaikan status izin/sakit susulan
                        </div>
                    </div>
                    <span
                        class="px-3 py-1.5 rounded-full bg-lavender-light text-lavender-dark text-xs font-bold shrink-0 ml-2"
                    >
                        Koreksi
                    </span>
                </div>

                <div
                    class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between"
                >
                    <div>
                        <div class="text-sm font-extrabold text-charcoal">
                            Plotting Guru &amp; Wali Kelas
                        </div>
                        <div class="text-[11px] text-subtext">
                            42 Rombel telah memiliki Wali Kelas aktif
                        </div>
                    </div>
                    <span
                        class="px-3 py-1.5 rounded-full bg-sage-light text-sage-dark text-xs font-bold shrink-0 ml-2"
                    >
                        Lengkap
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
