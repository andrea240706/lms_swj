<script setup>
/**
 * LmsSkeletonLoader
 * Reusable shimmer skeleton loader for all LMS role views.
 *
 * @prop {string} type - Layout preset:
 *   'dashboard'    — hero card + calendar row + 2-col cards (Siswa/Guru home)
 *   'mapel-list'   — hero + 2/3-col card grid
 *   'meeting-list' — header + vertical list
 *   'meeting-detail' — tabs + content rows
 *   'rekap-list'   — header + stats + table rows
 *   'card-list'    — simple vertical card list (Admin/Kepsek/WaliKelas)
 * @prop {number} rows - Number of list rows to render (default 4)
 * @prop {number} cards - Number of grid cards to render (default 4)
 */
defineProps({
    type: {
        type: String,
        default: 'dashboard',
    },
    rows: {
        type: Number,
        default: 4,
    },
    cards: {
        type: Number,
        default: 4,
    },
});
</script>

<template>
    <!-- ================================================
         Shimmer Base: every element uses .skel class
         ================================================ -->
    <div class="w-full animate-pulse select-none pointer-events-none">

        <!-- ==================== DASHBOARD ==================== -->
        <!-- Siswa Home / Guru Home: hero + calendar row + 2-col bottom -->
        <template v-if="type === 'dashboard'">
            <!-- Hero Card -->
            <div class="rounded-[28px] bg-canvas border border-[#ECEAE4] p-5 mb-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="skel h-5 w-32 rounded-full"></div>
                    <div class="skel h-5 w-20 rounded-full"></div>
                </div>
                <div class="skel h-7 w-48 rounded-xl mb-2"></div>
                <div class="skel h-4 w-40 rounded-xl mb-4"></div>
                <div class="flex gap-2">
                    <div class="flex-1 skel h-10 rounded-2xl"></div>
                    <div class="flex-1 skel h-10 rounded-2xl"></div>
                    <div class="flex-1 skel h-10 rounded-2xl"></div>
                </div>
                <div class="flex gap-2 mt-4">
                    <div class="skel h-9 w-36 rounded-full"></div>
                    <div class="skel h-9 w-28 rounded-full"></div>
                </div>
            </div>

            <!-- Calendar Strip -->
            <div class="rounded-[24px] bg-canvas border border-[#ECEAE4] p-4 mb-5">
                <div class="skel h-4 w-32 rounded-full mb-3"></div>
                <div class="flex gap-2">
                    <div v-for="i in 6" :key="i" class="flex-1 skel h-14 rounded-2xl"></div>
                </div>
            </div>

            <!-- 2-col bottom cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div
                    v-for="col in 2"
                    :key="col"
                    class="bg-canvas border border-[#ECEAE4] rounded-[28px] p-5"
                >
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="skel w-7 h-7 rounded-full"></div>
                            <div class="skel h-4 w-28 rounded-xl"></div>
                        </div>
                        <div class="skel h-4 w-16 rounded-full"></div>
                    </div>
                    <div class="space-y-3">
                        <div v-for="r in 3" :key="r" class="skel h-16 rounded-[20px]"></div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ==================== MAPEL-LIST ==================== -->
        <!-- Guru/Siswa mapel grid with hero banner -->
        <template v-else-if="type === 'mapel-list'">
            <!-- Banner -->
            <div class="rounded-[28px] bg-canvas border border-[#ECEAE4] p-5 mb-5">
                <div class="skel h-5 w-40 rounded-full mb-2"></div>
                <div class="skel h-7 w-52 rounded-xl mb-1.5"></div>
                <div class="skel h-4 w-64 rounded-xl"></div>
            </div>
            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <div
                    v-for="i in cards"
                    :key="i"
                    class="bg-canvas border border-[#ECEAE4] rounded-[26px] p-5"
                >
                    <div class="flex justify-between mb-3">
                        <div class="skel h-4 w-24 rounded-full"></div>
                        <div class="skel h-4 w-20 rounded-full"></div>
                    </div>
                    <div class="skel h-5 w-48 rounded-xl mb-2"></div>
                    <div class="flex gap-1.5 mb-4">
                        <div class="skel h-5 w-16 rounded-full"></div>
                        <div class="skel h-5 w-16 rounded-full"></div>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-[#ECEAE4]">
                        <div class="skel h-4 w-24 rounded-xl"></div>
                        <div class="skel h-4 w-12 rounded-full"></div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ==================== MEETING-LIST ==================== -->
        <!-- Vertical meeting list inside a mapel detail -->
        <template v-else-if="type === 'meeting-list'">
            <!-- Alert placeholder -->
            <div class="skel h-14 rounded-2xl mb-4"></div>
            <div class="skel h-4 w-28 rounded-full mb-3"></div>
            <div class="space-y-3">
                <div
                    v-for="i in rows"
                    :key="i"
                    class="bg-canvas border border-[#ECEAE4] rounded-[24px] p-4 flex items-center gap-4"
                >
                    <div class="skel w-11 h-11 rounded-full shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="skel h-4 w-3/4 rounded-xl"></div>
                        <div class="skel h-3 w-1/2 rounded-xl"></div>
                    </div>
                    <div class="skel w-4 h-4 rounded-full shrink-0"></div>
                </div>
            </div>
        </template>

        <!-- ==================== MEETING-DETAIL ==================== -->
        <!-- Sub-tabs + content (penilaian/materi/kuis/video) -->
        <template v-else-if="type === 'meeting-detail'">
            <!-- Sub-tab bar -->
            <div class="skel h-11 rounded-2xl mb-4"></div>
            <!-- Info card -->
            <div class="skel h-28 rounded-[22px] mb-4"></div>
            <!-- 2-col grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="space-y-3">
                    <div class="skel h-4 w-32 rounded-full"></div>
                    <div v-for="i in 3" :key="i" class="skel h-20 rounded-[22px]"></div>
                </div>
                <div class="space-y-3">
                    <div class="skel h-4 w-28 rounded-full"></div>
                    <div v-for="i in 3" :key="i" class="skel h-20 rounded-[22px]"></div>
                </div>
            </div>
        </template>

        <!-- ==================== REKAP-LIST ==================== -->
        <!-- RekapNilai: stat boxes + table rows -->
        <template v-else-if="type === 'rekap-list'">
            <!-- Banner -->
            <div class="skel h-28 rounded-[28px] mb-5"></div>
            <!-- Stats row -->
            <div class="grid grid-cols-4 gap-2.5 mb-4">
                <div v-for="i in 4" :key="i" class="skel h-16 rounded-[20px]"></div>
            </div>
            <!-- Search & filter -->
            <div class="flex gap-2.5 mb-4">
                <div class="flex-1 skel h-10 rounded-2xl"></div>
                <div class="skel h-10 w-20 rounded-full"></div>
                <div class="skel h-10 w-24 rounded-full"></div>
            </div>
            <!-- Table rows -->
            <div class="space-y-2.5">
                <div
                    v-for="i in rows"
                    :key="i"
                    class="bg-canvas border border-[#ECEAE4] rounded-[22px] p-3.5 flex items-center gap-3"
                >
                    <div class="skel w-10 h-10 rounded-full shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="skel h-3.5 w-36 rounded-xl"></div>
                        <div class="skel h-3 w-24 rounded-xl"></div>
                    </div>
                    <div class="skel w-12 h-12 rounded-full shrink-0"></div>
                    <div class="skel w-14 h-6 rounded-full shrink-0"></div>
                </div>
            </div>
        </template>

        <!-- ==================== CARD-LIST (default) ==================== -->
        <!-- Admin / Kepsek / WaliKelas / SuperAdmin: hero + vertical action cards -->
        <template v-else>
            <!-- Hero Card -->
            <div class="rounded-[28px] bg-canvas border border-[#ECEAE4] p-5 mb-5">
                <div class="skel h-6 w-36 rounded-full mb-3"></div>
                <div class="skel h-7 w-52 rounded-xl mb-2"></div>
                <div class="skel h-4 w-48 rounded-xl"></div>
            </div>

            <!-- Section title -->
            <div class="skel h-4 w-40 rounded-full mb-3"></div>

            <!-- Action cards -->
            <div class="space-y-2.5">
                <div
                    v-for="i in rows"
                    :key="i"
                    class="bg-canvas border border-[#ECEAE4] rounded-[24px] p-4 flex items-center justify-between"
                >
                    <div class="space-y-2">
                        <div class="skel h-4 w-44 rounded-xl"></div>
                        <div class="skel h-3 w-32 rounded-xl"></div>
                    </div>
                    <div class="skel h-7 w-20 rounded-full shrink-0"></div>
                </div>
            </div>

            <!-- Optional 2-col grid (for kepsek download buttons) -->
            <div class="grid grid-cols-2 gap-2.5 mt-5">
                <div class="skel h-24 rounded-[22px]"></div>
                <div class="skel h-24 rounded-[22px]"></div>
            </div>
        </template>

    </div>
</template>

<style scoped>
.skel {
    background: linear-gradient(
        90deg,
        #F0EEE9 0%,
        #E8E5DF 40%,
        #F0EEE9 80%
    );
    background-size: 200% 100%;
    animation: shimmer 1.6s ease-in-out infinite;
    border-radius: 12px;
}

@keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>
