<script setup>
import { computed, ref } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import { kepsekOverviewData, teacherSupervisionList } from './kepsekData';
import { downloadCSV, downloadExcel, downloadPDF } from './exportUtils';

const emit = defineEmits(['showToast']);

// Filter & Search
const search = ref('');
const filterKategori = ref('semua');

const filteredTeachers = computed(() => {
    return teacherSupervisionList.filter((t) => {
        const matchKategori =
            filterKategori.value === 'semua' ||
            (filterKategori.value === 'kejuruan' && t.jur !== 'Umum') ||
            (filterKategori.value === 'umum' && t.jur === 'Umum');
        const q = search.value.trim().toLowerCase();
        const matchSearch =
            !q ||
            t.name.toLowerCase().includes(q) ||
            t.mapel.toLowerCase().includes(q) ||
            t.nip.toLowerCase().includes(q);
        return matchKategori && matchSearch;
    });
});

// Download Handlers (Fungsional Nyata, Bukan Simulasi)
const handleExportKinerja = (format) => {
    const headers = [
        'Nama Guru',
        'NIP',
        'Mata Pelajaran',
        'Program / Rumpun',
        'Beban Jam (JP)',
        'Status Jurnal',
        'Evaluasi Administrasi',
    ];

    const rows = teacherSupervisionList.map((t) => [
        t.name,
        t.nip,
        t.mapel,
        t.jur,
        `${t.jp} JP/minggu`,
        t.journalStatus,
        t.evaluasi,
    ]);

    if (format === 'csv') {
        downloadCSV('Laporan_Kinerja_Mengajar_Guru.csv', headers, rows);
        emit('showToast', 'Berkas CSV Kinerja Mengajar Guru berhasil diunduh.');
    } else if (format === 'excel') {
        downloadExcel(
            'Laporan_Kinerja_Mengajar_Guru.xls',
            'LAPORAN EVALUASI BEBAN KERJA & KINERJA MENGAJAR GURU',
            headers,
            rows
        );
        emit('showToast', 'Berkas Excel Rekap Kinerja Guru berhasil diunduh.');
    } else if (format === 'pdf') {
        downloadPDF(
            'Laporan Eksekutif Supervisi Kinerja Mengajar Guru',
            `Tahun Ajaran ${kepsekOverviewData.tahunAjaran} • Beban Total: ${kepsekOverviewData.totalJpSekolah} • Kepatuhan Jurnal: ${kepsekOverviewData.jurnalTertibPct}`,
            headers,
            rows,
            [
                'Seluruh tenaga pendidik memenuhi beban minimal 24 JP per minggu sesuai ketentuan perundang-undangan.',
                'Kepatuhan pelaporan jurnal materi pembelajaran mencapai 95.6% tertib tepat waktu.',
                'Seluruh rombel terplotting dan tidak ada jam pelajaran tanpa penugasan guru.',
            ]
        );
        emit('showToast', 'Pratinjau PDF Laporan Kinerja Guru siap cetak / simpan telah dibuka.');
    }
};
</script>

<template>
    <div class="space-y-5">
        <!-- Hero Banner Standar (min-h: 115px) -->
        <div class="hero-amber rounded-[28px] py-7 px-6 sm:py-8 sm:px-8 soft-shadow relative overflow-hidden flex flex-col justify-center min-h-[115px]">
            <div class="relative z-10">
                <h2 class="text-[20px] sm:text-[23px] font-black text-charcoal leading-tight">
                    Kinerja Guru
                </h2>
                <p class="text-xs sm:text-[13px] text-charcoal/80 mt-1.5 max-w-xl leading-relaxed">
                    Supervisi pemenuhan beban jam mengajar mingguan (JP), kepatuhan jurnal pengajaran, dan tertib administrasi guru.
                </p>
            </div>
        </div>

        <!-- 3 Kartu Ringkasan Kinerja Mengajar -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow">
                <div class="text-xs font-bold text-subtext">Total Beban Mengajar Sekolah</div>
                <div class="text-[26px] font-black text-charcoal mt-1">{{ kepsekOverviewData.totalJpSekolah }}</div>
                <div class="text-[11px] text-subtext font-semibold mt-0.5">Terplotting penuh di jadwal mingguan</div>
            </div>
            <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow">
                <div class="text-xs font-bold text-subtext">Tenaga Pendidik Aktif</div>
                <div class="text-[26px] font-black text-[#2E7D32] mt-1">{{ kepsekOverviewData.totalGuru }} Guru</div>
                <div class="text-[11px] text-[#2E7D32] font-semibold mt-0.5">100% Memenuhi Beban &ge; 24 JP</div>
            </div>
            <div class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow">
                <div class="text-xs font-bold text-subtext">Kepatuhan Jurnal Mengajar</div>
                <div class="text-[26px] font-black text-charcoal mt-1">{{ kepsekOverviewData.jurnalTertibPct }}</div>
                <div class="text-[11px] text-subtext font-semibold mt-0.5">Tertib mengisi laporan pengajaran</div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center gap-1.5 p-1 rounded-2xl bg-[#F0EFEA] border border-[#ECEAE4] overflow-x-auto">
                <button
                    type="button"
                    @click="filterKategori = 'semua'"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                    :class="filterKategori === 'semua' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                >
                    Semua Guru ({{ teacherSupervisionList.length }})
                </button>
                <button
                    type="button"
                    @click="filterKategori = 'kejuruan'"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                    :class="filterKategori === 'kejuruan' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                >
                    Produktif Kejuruan
                </button>
                <button
                    type="button"
                    @click="filterKategori = 'umum'"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap"
                    :class="filterKategori === 'umum' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                >
                    Muatan Umum
                </button>
            </div>

            <div class="w-full md:w-72">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari guru, NIP, atau mapel..."
                    class="w-full px-3.5 py-2 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-semibold text-charcoal placeholder:text-subtext focus:outline-none focus:border-charcoal/40 transition-all"
                />
            </div>
        </div>

        <!-- Tabel/List Kinerja Guru yang Bersih, Rapi, & Proporsional -->
        <div class="space-y-3">
            <div
                v-for="t in filteredTeachers"
                :key="t.id"
                class="bg-white rounded-[22px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-charcoal/30 transition-all"
            >
                <!-- Identitas Guru & Mapel -->
                <div class="space-y-1.5 flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h4 class="text-sm font-extrabold text-charcoal truncate">{{ t.name }}</h4>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-canvas border border-[#ECEAE4] text-subtext">
                            NIP. {{ t.nip }}
                        </span>
                        <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full bg-amber-light text-amber-dark">
                            {{ t.jp }} JP / Minggu
                        </span>
                    </div>

                    <div class="text-xs text-subtext flex items-center gap-2 flex-wrap">
                        <span>Mapel: <strong class="text-charcoal">{{ t.mapel }}</strong></span>
                        <span>•</span>
                        <span>Rumpun: <strong class="text-charcoal">{{ t.jur }}</strong></span>
                    </div>
                </div>

                <!-- Status Kepatuhan Jurnal Mengajar -->
                <div class="flex items-center gap-2 shrink-0 self-start sm:self-center">
                    <span class="text-xs font-bold px-3 py-1.5 rounded-xl bg-[#E8F5E9] text-[#2E7D32] border border-[#C8E6C9] flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#2E7D32]"></span>
                        {{ t.journalStatus }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Pusat Unduhan Laporan Kinerja Guru (3 Format: CSV, Excel, PDF) -->
        <div class="bg-white rounded-[26px] p-5 sm:p-6 border border-[#ECEAE4] soft-shadow flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full bg-amber-light text-amber-dark">
                    DOKUMEN RESMI SUPERVISI
                </span>
                <h4 class="text-base font-extrabold text-charcoal mt-1.5">
                    Unduh Rekapitulasi Kinerja Mengajar Guru
                </h4>
                <p class="text-xs text-subtext mt-0.5">
                    Laporan beban kerja guru dan kepatuhan jurnal resmi untuk akreditasi, dinas, dan arsip kepegawaian.
                </p>
            </div>

            <!-- 3 Tombol Download yang Berfungsi Nyata -->
            <div class="flex items-center gap-2 flex-wrap shrink-0">
                <button
                    type="button"
                    @click="handleExportKinerja('pdf')"
                    class="px-3.5 py-2 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs flex items-center gap-1.5"
                    title="Cetak atau Unduh Dokumen PDF Rapi Resmi"
                >
                    <LmsIcon name="download" className="w-3.5 h-3.5 text-white" />
                    <span>PDF (Rapi)</span>
                </button>

                <button
                    type="button"
                    @click="handleExportKinerja('excel')"
                    class="px-3.5 py-2 rounded-xl bg-[#E8F5E9] text-[#2E7D32] border border-[#C8E6C9] text-xs font-bold hover:bg-[#C8E6C9] active:scale-95 transition-all cursor-pointer shadow-2xs flex items-center gap-1.5"
                    title="Unduh Berkas Excel Ber-template Resmi"
                >
                    <LmsIcon name="download" className="w-3.5 h-3.5 text-[#2E7D32]" />
                    <span>Excel (Template)</span>
                </button>

                <button
                    type="button"
                    @click="handleExportKinerja('csv')"
                    class="px-3.5 py-2 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal text-xs font-bold hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs flex items-center gap-1.5"
                    title="Unduh Data Mentah CSV"
                >
                    <LmsIcon name="download" className="w-3.5 h-3.5 text-charcoal" />
                    <span>CSV (Mentah)</span>
                </button>
            </div>
        </div>
    </div>
</template>
