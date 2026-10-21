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
    isSidebarCollapsed: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['openMenu', 'openProfile', 'showToast', 'toggleSidebar']);
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <LmsTopBar
            :role="role"
            :isSidebarCollapsed="isSidebarCollapsed"
            title="Perwalian Rombel X DKV 1"
            @openMenu="$emit('openMenu')"
            @openProfile="$emit('openProfile')"
            @toggleSidebar="$emit('toggleSidebar')"
        />

        <div class="flex-1 overflow-y-auto no-scrollbar px-6 pb-28">
            <!-- Hero Card: Homeroom Attendance Rate -->
            <div class="hero-sky rounded-[28px] p-5 soft-shadow">
                <span
                    class="bg-white px-3 py-1 rounded-full text-[11px] font-extrabold text-charcoal shadow-2xs"
                >
                    Wali Kelas • X DKV 1
                </span>
                <h3 class="text-[21px] font-extrabold text-charcoal mt-3">
                    Kehadiran Rombel: 94.4%
                </h3>
                <p class="text-xs text-charcoal/75 mt-1">
                    34 Hadir • 1 Sakit • 1 Alpa Hari Ini
                </p>
            </div>

            <!-- Early Warning Section -->
            <h3 class="text-[15px] font-extrabold text-charcoal mt-5 mb-2.5">
                Peringatan Dini (Early Warning)
            </h3>
            <div class="space-y-2.5">
                <div
                    class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between"
                >
                    <div>
                        <span
                            class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-blossom-light text-blossom-dark"
                        >
                            Alpa 3 Hari Berturut-turut
                        </span>
                        <div class="text-sm font-extrabold text-charcoal mt-1.5">
                            Aldiansyah Putra
                        </div>
                        <div class="text-[11px] text-subtext">
                            Rata-rata Nilai Tugas: 68.5 (Di bawah KKM)
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="$emit('showToast', 'Notifikasi pemanggilan wali santri dikirim untuk Aldiansyah Putra.')"
                        class="px-3 py-1.5 rounded-full bg-charcoal text-white text-xs font-bold cursor-pointer hover:opacity-90 active:scale-95 transition-all"
                    >
                        Pantau
                    </button>
                </div>

                <div
                    class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between"
                >
                    <div>
                        <span
                            class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amberpastel-light text-amberpastel-dark"
                        >
                            2 Tugas Produktif Belum Kumpul
                        </span>
                        <div class="text-sm font-extrabold text-charcoal mt-1.5">
                            Salsabila Azzahra
                        </div>
                        <div class="text-[11px] text-subtext">
                            Rata-rata Nilai Tugas: 74.0
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="$emit('showToast', 'Pengingat tugas dikirim ke akun Salsabila Azzahra.')"
                        class="px-3 py-1.5 rounded-full bg-charcoal text-white text-xs font-bold cursor-pointer hover:opacity-90 active:scale-95 transition-all"
                    >
                        Pantau
                    </button>
                </div>
            </div>

            <!-- Cross-Subject Leger -->
            <h3 class="text-[15px] font-extrabold text-charcoal mt-5 mb-2.5">
                Buku Nilai Lintas Mapel (Leger Rombel)
            </h3>
            <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow space-y-2.5">
                <div class="flex justify-between items-center text-xs font-bold text-charcoal pb-2 border-b border-[#F0EFEA]">
                    <span>Dasar Desain Komunikasi Visual</span>
                    <span class="px-2 py-0.5 rounded-md bg-sage-light text-sage-dark font-extrabold">86.4</span>
                </div>
                <div class="flex justify-between items-center text-xs font-bold text-charcoal pb-2 border-b border-[#F0EFEA]">
                    <span>Fotografi &amp; Tata Cahaya</span>
                    <span class="px-2 py-0.5 rounded-md bg-sage-light text-sage-dark font-extrabold">89.1</span>
                </div>
                <div class="flex justify-between items-center text-xs font-bold text-charcoal">
                    <span>Bahasa Indonesia</span>
                    <span class="px-2 py-0.5 rounded-md bg-sky-light text-sky-dark font-extrabold">84.0</span>
                </div>
            </div>
        </div>
    </div>
</template>
