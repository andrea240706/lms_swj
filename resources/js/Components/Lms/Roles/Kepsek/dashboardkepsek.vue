<script setup>
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import { kepsekOverviewData, majorsData } from './kepsekData';

const emit = defineEmits(['selectTab', 'showToast']);
</script>

<template>
    <div class="space-y-5">
        <!-- Hero Banner Standar (min-h: 115px) -->
        <div class="hero-amber rounded-[28px] py-7 px-6 sm:py-8 sm:px-8 soft-shadow relative overflow-hidden flex flex-col justify-center min-h-[115px]">
            <div class="relative z-10">
                <h2 class="text-[22px] sm:text-[25px] font-black text-charcoal leading-tight">
                    Pusat Kendali Eksekutif
                </h2>
                <p class="text-xs sm:text-[13px] text-charcoal/80 mt-1.5 max-w-xl leading-relaxed">
                    Tahun Ajaran {{ kepsekOverviewData.tahunAjaran }} • Supervisi 42 Rombel, 68 Tenaga Pendidik, &amp; 1.240 Siswa
                </p>
            </div>
        </div>

        <!-- 4 Kartu Indikator Makro Utama -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- KPI 1: Keterlaksanaan KBM -->
            <div
                @click="emit('selectTab', 'kbm')"
                class="bg-white rounded-[24px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-subtext">Keterlaksanaan KBM</span>
                    <span class="w-8 h-8 rounded-xl bg-sage-light flex items-center justify-center text-sage-dark">
                        <LmsIcon name="book" className="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-[26px] sm:text-[30px] font-black text-charcoal leading-none">
                        {{ kepsekOverviewData.kbmPercentage }}
                    </div>
                    <div class="text-[11px] font-bold text-[#2E7D32] mt-1.5">
                        {{ kepsekOverviewData.sesiBerjalanCount }}
                    </div>
                </div>
            </div>

            <!-- KPI 2: Kehadiran Guru -->
            <div
                @click="emit('selectTab', 'kinerja')"
                class="bg-white rounded-[24px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-subtext">Kehadiran Guru</span>
                    <span class="w-8 h-8 rounded-xl bg-amber-light flex items-center justify-center text-amber-dark">
                        <LmsIcon name="user" className="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-[26px] sm:text-[30px] font-black text-charcoal leading-none">
                        {{ kepsekOverviewData.guruHadirPct }}
                    </div>
                    <div class="text-[11px] font-bold text-charcoal/70 mt-1.5">
                        {{ kepsekOverviewData.guruHadirCount }}
                    </div>
                </div>
            </div>

            <!-- KPI 3: Kehadiran Siswa Makro -->
            <div
                @click="emit('selectTab', 'kbm')"
                class="bg-white rounded-[24px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-subtext">Kehadiran Siswa</span>
                    <span class="w-8 h-8 rounded-xl bg-sky-light flex items-center justify-center text-sky-dark">
                        <LmsIcon name="chart" className="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-[26px] sm:text-[30px] font-black text-charcoal leading-none">
                        {{ kepsekOverviewData.siswaHadirPct }}
                    </div>
                    <div class="text-[11px] font-bold text-subtext mt-1.5">
                        {{ kepsekOverviewData.siswaHadirCount }} dari {{ kepsekOverviewData.totalSiswa }} Siswa
                    </div>
                </div>
            </div>

            <!-- KPI 4: Kepatuhan Jurnal Mengajar -->
            <div
                @click="emit('selectTab', 'kinerja')"
                class="bg-white rounded-[24px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-subtext">Kepatuhan Jurnal</span>
                    <span class="w-8 h-8 rounded-xl bg-lavender-light flex items-center justify-center text-lavender-dark">
                        <LmsIcon name="task" className="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-3">
                    <div class="text-[26px] sm:text-[30px] font-black text-charcoal leading-none">
                        {{ kepsekOverviewData.jurnalTertibPct }}
                    </div>
                    <div class="text-[11px] font-bold text-[#2E7D32] mt-1.5">
                        Tertib Laporan Ajar
                    </div>
                </div>
            </div>
        </div>

        <!-- Komparasi Keterlaksanaan KBM & Presensi per Program Keahlian -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="text-sm sm:text-base font-extrabold text-charcoal">
                        Keterlaksanaan KBM &amp; Kehadiran Siswa per Program Keahlian
                    </h3>
                    <p class="text-xs text-subtext mt-0.5">
                        Evaluasi menyeluruh 5 kompetensi keahlian SMKN 1 Nusantara.
                    </p>
                </div>
                <button
                    type="button"
                    @click="emit('selectTab', 'kbm')"
                    class="text-xs font-black text-charcoal hover:underline flex items-center gap-1 cursor-pointer shrink-0"
                >
                    Lihat Rekap Lengkap →
                </button>
            </div>

            <div class="space-y-2.5">
                <div
                    v-for="j in majorsData"
                    :key="j.jur"
                    class="bg-white rounded-[24px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-charcoal/20 transition-all"
                >
                    <div>
                        <div class="text-xs sm:text-sm font-extrabold text-charcoal">{{ j.jur }}</div>
                        <div class="text-[11px] text-subtext mt-1 flex items-center gap-2.5 flex-wrap">
                            <span>{{ j.rombel }}</span>
                            <span>•</span>
                            <span>{{ j.guru }}</span>
                            <span>•</span>
                            <span>{{ j.siswa }} Siswa</span>
                            <span>•</span>
                            <span class="text-charcoal font-semibold">{{ j.lab }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 shrink-0 self-start sm:self-center">
                        <div class="text-right">
                            <div class="text-xs font-extrabold text-[#2E7D32]">{{ j.sesiInfo }} ({{ j.kbmPct }})</div>
                            <div class="text-[10px] text-subtext font-semibold">Kehadiran Siswa: {{ j.hadir }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
