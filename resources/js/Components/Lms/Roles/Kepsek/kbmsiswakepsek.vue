<script setup>
import { ref } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import { kepsekOverviewData, kbmTingkatData, majorsData } from './kepsekData';
import { downloadCSV, downloadExcel, downloadPDF } from './exportUtils';

const emit = defineEmits(['showToast']);

// Filter data
const selectedViewTab = ref('semua');

// Download Handlers (Fungsional Nyata, Bukan Simulasi)
const handleExportKbmPresensi = (format) => {
    const headers = ['Program Keahlian / Tingkat', 'Rombel', 'Total Siswa', 'Hadir', 'Sakit', 'Izin', 'Alpa', '% Kehadiran', 'Keterlaksanaan KBM'];
    const rows = [
        ...kbmTingkatData.map((k) => [
            k.tingkat,
            `${k.rombelCount} Rombel`,
            k.siswaCount,
            k.hadir,
            k.sakit,
            k.izin,
            k.alpa,
            k.presensiPct,
            k.kbmStatus,
        ]),
        ...majorsData.map((m) => [
            m.jur,
            m.rombel,
            m.siswa,
            Math.round(m.siswa * 0.96),
            2,
            3,
            1,
            m.hadir,
            m.sesiInfo,
        ]),
    ];

    if (format === 'csv') {
        downloadCSV('Rekapitulasi_KBM_Presensi_Siswa_SMKN1.csv', headers, rows);
        emit('showToast', 'Berkas CSV Rekapitulasi KBM & Presensi Siswa berhasil diunduh.');
    } else if (format === 'excel') {
        downloadExcel(
            'Rekapitulasi_KBM_Presensi_Siswa_SMKN1.xls',
            'REKAPITULASI KETERLAKSANAAN KBM & PRESENSI SISWA',
            headers,
            rows
        );
        emit('showToast', 'Berkas Excel Template Resmi Dinas berhasil diunduh.');
    } else if (format === 'pdf') {
        downloadPDF(
            'Laporan Eksekutif Keterlaksanaan KBM & Presensi Siswa',
            `Tahun Ajaran ${kepsekOverviewData.tahunAjaran} • Total Siswa: ${kepsekOverviewData.totalSiswa} • Kehadiran Makro: ${kepsekOverviewData.siswaHadirPct}`,
            headers,
            rows,
            [
                'Seluruh jadwal KBM berjalan 96.2% tanpa ada jam pelajaran kosong (tertangani guru pengganti/tugas mandiri).',
                'Tingkat kehadiran siswa sekolah mencapai 95.8%, di atas target standar mutu sekolah (95.0%).',
            ]
        );
        emit('showToast', 'Pratinjau PDF Laporan Resmi siap cetak / simpan telah dibuka.');
    }
};
</script>

<template>
    <div class="space-y-5">
        <!-- Hero Banner Standar (min-h: 115px) -->
        <div class="hero-amber rounded-[28px] py-7 px-6 sm:py-8 sm:px-8 soft-shadow relative overflow-hidden flex flex-col justify-center min-h-[115px]">
            <div class="relative z-10">
                <h2 class="text-[20px] sm:text-[23px] font-black text-charcoal leading-tight">
                    KBM &amp; Kehadiran Siswa
                </h2>
                <p class="text-xs sm:text-[13px] text-charcoal/80 mt-1.5 max-w-xl leading-relaxed">
                    Pantauan keterlaksanaan pembelajaran seluruh rombel dan rekapitulasi kehadiran siswa sekolah.
                </p>
            </div>
        </div>

        <!-- 1. Bagian: Kepastian KBM Berjalan (Tingkat Keterlaksanaan Sekolah) -->
        <div class="bg-white rounded-[26px] p-5 sm:p-6 border border-[#ECEAE4] soft-shadow space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#ECEAE4] pb-3">
                <div>
                    <h3 class="text-sm sm:text-base font-extrabold text-charcoal">
                        Kepastian KBM Berjalan Hari Ini
                    </h3>
                    <p class="text-xs text-subtext mt-0.5">
                        Memastikan seluruh jadwal KBM terlaksana normal tanpa jam pelajaran kosong.
                    </p>
                </div>
                <div class="px-3 py-1.5 rounded-full bg-[#E8F5E9] text-[#2E7D32] text-xs font-black self-start sm:self-auto">
                    KBM Berjalan: {{ kepsekOverviewData.kbmPercentage }}
                </div>
            </div>

            <!-- 4 Indikator Status KBM Makro -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="p-3.5 rounded-2xl bg-canvas border border-[#ECEAE4]">
                    <div class="text-[11px] font-bold text-subtext">Total Jadwal Hari Ini</div>
                    <div class="text-xl font-black text-charcoal mt-1">40 Sesi KBM</div>
                    <div class="text-[10px] text-charcoal/60 mt-0.5">42 Rombel paralel</div>
                </div>
                <div class="p-3.5 rounded-2xl bg-canvas border border-[#ECEAE4]">
                    <div class="text-[11px] font-bold text-subtext">Sesi Berjalan Normal</div>
                    <div class="text-xl font-black text-[#2E7D32] mt-1">38 Sesi</div>
                    <div class="text-[10px] text-[#2E7D32] mt-0.5">Tepat waktu di kelas/lab</div>
                </div>
                <div class="p-3.5 rounded-2xl bg-canvas border border-[#ECEAE4]">
                    <div class="text-[11px] font-bold text-subtext">Guru Tugas Luar / Izin</div>
                    <div class="text-xl font-black text-amber-dark mt-1">2 Sesi</div>
                    <div class="text-[10px] text-subtext mt-0.5">Ada tugas mandiri / piket</div>
                </div>
                <div class="p-3.5 rounded-2xl bg-canvas border border-[#ECEAE4]">
                    <div class="text-[11px] font-bold text-subtext">Jam Pelajaran Kosong</div>
                    <div class="text-xl font-black text-charcoal mt-1">0 Sesi</div>
                    <div class="text-[10px] text-[#2E7D32] font-bold mt-0.5">Semua kelas tertangani</div>
                </div>
            </div>

            <!-- Keterlaksanaan per Jenjang Kelas -->
            <div class="pt-2">
                <h4 class="text-xs font-extrabold text-charcoal mb-2.5">Keterlaksanaan per Jenjang Rombel</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div
                        v-for="tingkat in kbmTingkatData"
                        :key="tingkat.tingkat"
                        class="p-4 rounded-2xl border border-[#ECEAE4] bg-white soft-shadow flex flex-col justify-between"
                    >
                        <div>
                            <div class="text-xs font-black text-charcoal">{{ tingkat.tingkat }}</div>
                            <div class="text-[11px] text-subtext mt-0.5">{{ tingkat.rombelCount }} Rombel • {{ tingkat.siswaCount }} Siswa</div>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-[#ECEAE4] flex items-center justify-between text-xs">
                            <span class="font-bold text-charcoal">{{ tingkat.kbmStatus }}</span>
                            <span class="font-black text-[#2E7D32]">{{ tingkat.kbmPct }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Bagian: Rekapitulasi Presensi Makro Siswa -->
        <div class="bg-white rounded-[26px] p-5 sm:p-6 border border-[#ECEAE4] soft-shadow space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#ECEAE4] pb-3">
                <div>
                    <h3 class="text-sm sm:text-base font-extrabold text-charcoal">
                        Rekapitulasi Kehadiran Siswa Seluruh Rombel
                    </h3>
                    <p class="text-xs text-subtext mt-0.5">
                        Tingkat kehadiran siswa se-sekolah semester berjalan.
                    </p>
                </div>
                <div class="px-3 py-1.5 rounded-full bg-sky-light text-sky-dark text-xs font-black self-start sm:self-auto">
                    Tingkat Kehadiran: {{ kepsekOverviewData.siswaHadirPct }}
                </div>
            </div>

            <!-- 4 Kartu Breakdown Presensi Siswa -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="p-4 rounded-2xl bg-[#E8F5E9] text-[#2E7D32]">
                    <div class="text-xs font-bold opacity-80">Siswa Hadir</div>
                    <div class="text-2xl font-black mt-1">{{ kepsekOverviewData.siswaHadirCount }}</div>
                    <div class="text-[10px] font-semibold mt-0.5">95.8% dari seluruh siswa</div>
                </div>
                <div class="p-4 rounded-2xl bg-sky-light text-sky-dark">
                    <div class="text-xs font-bold opacity-80">Sakit (Keterangan)</div>
                    <div class="text-2xl font-black mt-1">{{ kepsekOverviewData.siswaSakitCount }}</div>
                    <div class="text-[10px] font-semibold mt-0.5">1.1% dengan surat dokter</div>
                </div>
                <div class="p-4 rounded-2xl bg-amber-light text-amber-dark">
                    <div class="text-xs font-bold opacity-80">Izin Resmi</div>
                    <div class="text-2xl font-black mt-1">{{ kepsekOverviewData.siswaIzinCount }}</div>
                    <div class="text-[10px] font-semibold mt-0.5">2.3% surat dari orang tua</div>
                </div>
                <div class="p-4 rounded-2xl bg-blossom-light text-blossom-dark">
                    <div class="text-xs font-bold opacity-80">Tanpa Keterangan (Alpa)</div>
                    <div class="text-2xl font-black mt-1">{{ kepsekOverviewData.siswaAlpaCount }}</div>
                    <div class="text-[10px] font-semibold mt-0.5">0.8% siswa se-sekolah</div>
                </div>
            </div>

            <!-- Tabel Rekap Presensi per Jurusan -->
            <div class="overflow-x-auto no-scrollbar pt-2">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-[#ECEAE4] text-subtext font-bold text-[11px]">
                            <th class="py-2.5 px-3">Program Keahlian</th>
                            <th class="py-2.5 px-3">Jumlah Rombel</th>
                            <th class="py-2.5 px-3">Total Siswa</th>
                            <th class="py-2.5 px-3">Kehadiran Rata-rata</th>
                            <th class="py-2.5 px-3">Status KBM</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#ECEAE4]">
                        <tr v-for="m in majorsData" :key="m.jur" class="hover:bg-canvas transition-colors">
                            <td class="py-3 px-3 font-extrabold text-charcoal">{{ m.jur }}</td>
                            <td class="py-3 px-3 text-subtext">{{ m.rombel }}</td>
                            <td class="py-3 px-3 font-semibold text-charcoal">{{ m.siswa }} Siswa</td>
                            <td class="py-3 px-3 font-black text-[#2E7D32]">{{ m.hadir }}</td>
                            <td class="py-3 px-3 text-subtext">{{ m.sesiInfo }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Bagian: Pusat Unduhan Laporan KBM & Presensi (3 Format: CSV, Excel, PDF) -->
        <div class="bg-white rounded-[26px] p-5 sm:p-6 border border-[#ECEAE4] soft-shadow flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full bg-amber-light text-amber-dark">
                    DOKUMEN RESMI SEKOLAH
                </span>
                <h4 class="text-base font-extrabold text-charcoal mt-1.5">
                    Unduh Rekapitulasi Presensi &amp; Keterlaksanaan KBM
                </h4>
                <p class="text-xs text-subtext mt-0.5">
                    Tersedia dalam 3 format resmi siap pakai untuk laporan dinas, pengawas, atau arsip mutu.
                </p>
            </div>

            <!-- 3 Tombol Download yang Berfungsi Nyata -->
            <div class="flex items-center gap-2 flex-wrap shrink-0">
                <button
                    type="button"
                    @click="handleExportKbmPresensi('pdf')"
                    class="px-3.5 py-2 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs flex items-center gap-1.5"
                    title="Cetak atau Unduh Dokumen PDF Rapi Resmi"
                >
                    <LmsIcon name="download" className="w-3.5 h-3.5 text-white" />
                    <span>PDF (Rapi)</span>
                </button>

                <button
                    type="button"
                    @click="handleExportKbmPresensi('excel')"
                    class="px-3.5 py-2 rounded-xl bg-[#E8F5E9] text-[#2E7D32] border border-[#C8E6C9] text-xs font-bold hover:bg-[#C8E6C9] active:scale-95 transition-all cursor-pointer shadow-2xs flex items-center gap-1.5"
                    title="Unduh Berkas Excel Ber-template Resmi"
                >
                    <LmsIcon name="download" className="w-3.5 h-3.5 text-[#2E7D32]" />
                    <span>Excel (Template)</span>
                </button>

                <button
                    type="button"
                    @click="handleExportKbmPresensi('csv')"
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
