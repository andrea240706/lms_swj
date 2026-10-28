<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import LmsSkeletonLoader from '@/Components/Lms/LmsSkeletonLoader.vue';
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';

const props = defineProps({
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

const emit = defineEmits(['openMenu', 'openProfile', 'showToast', 'toggleSidebar']);

// ===== SKELETON LOADER =====
const isLoading = ref(true);
const triggerLoading = (ms = 400) => {
    isLoading.value = true;
    setTimeout(() => {
        isLoading.value = false;
    }, ms);
};
onMounted(() => {
    triggerLoading(500);
});
watch(() => props.activeTab, () => {
    triggerLoading(400);
});

const pageTitle = computed(() => {
    const map = {
        home: 'Perwalian Rombel X DKV 1',
        absensi: 'Presensi Rombel X DKV 1',
        leger: 'Buku Nilai Lintas Mapel (Leger)',
        laporan: 'Catatan & Rapor Perwalian',
    };
    return map[props.activeTab] || 'Perwalian Rombel X DKV 1';
});

const rombelStudents = [
    { nisn: '009112001', name: 'Aldiansyah Putra', status: 'Alpa', hadirCount: '82%', catatan: 'Peringatan dini alpa berturut-turut' },
    { nisn: '009112002', name: 'Salsabila Azzahra', status: 'Hadir', hadirCount: '94%', catatan: '2 tugas produktif belum kumpul' },
    { nisn: '009112003', name: 'Bagas Pratama', status: 'Hadir', hadirCount: '98%', catatan: 'Tertib & tuntas semua tugas' },
    { nisn: '009112004', name: 'Dinda Rahmawati', status: 'Sakit', hadirCount: '92%', catatan: 'Surat dokter telah diverifikasi' },
    { nisn: '009112005', name: 'Eko Prasetyo', status: 'Hadir', hadirCount: '96%', catatan: 'Ketua kelas X DKV 1' },
];

const legerSubjects = [
    { mapel: 'Dasar Desain Komunikasi Visual', guru: 'Siti Rahma, M.Pd', avg: 86.4, tuntas: '34/36 Siswa' },
    { mapel: 'Fotografi & Tata Cahaya', guru: 'Andi Saputra, S.Sn', avg: 89.1, tuntas: '36/36 Siswa' },
    { mapel: 'Komputer Grafis & Tipografi', guru: 'Rina Kusuma, M.Kom', avg: 83.5, tuntas: '33/36 Siswa' },
    { mapel: 'Bahasa Indonesia', guru: 'Dra. Sri Mulyani', avg: 84.0, tuntas: '35/36 Siswa' },
    { mapel: 'Matematika Terapan Kejuruan', guru: 'Agus Salim, M.Pd', avg: 79.2, tuntas: '31/36 Siswa' },
];
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <LmsTopBar
            :role="role"
            :isSidebarCollapsed="isSidebarCollapsed"
            :title="pageTitle"
            @openMenu="$emit('openMenu')"
            @openProfile="$emit('openProfile')"
            @toggleSidebar="$emit('toggleSidebar')"
        />

        <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
            <div class="w-full max-w-[1800px] mx-auto">

                <!-- SKELETON LOADER -->
                <LmsSkeletonLoader v-if="isLoading" type="card-list" :rows="4" />

                <!-- CONTENT -->
                <template v-else>
                    <!-- ============================================ -->
                    <!-- TAB 1: HOME (RINGKASAN PERWALIAN)           -->
                    <!-- ============================================ -->
                    <div v-if="activeTab === 'home'" class="space-y-5">
                        <!-- Hero Card: Homeroom Attendance Rate -->
                        <div class="hero-sky rounded-[28px] p-5 soft-shadow">
                            <span class="bg-white px-3 py-1 rounded-full text-[11px] font-extrabold text-charcoal shadow-2xs">
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
                        <div>
                            <h3 class="text-[15px] font-extrabold text-charcoal mb-2.5">
                                Peringatan Dini (Early Warning)
                            </h3>
                            <div class="space-y-2.5">
                                <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-blossom-light text-blossom-dark">
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
                                        class="px-3 py-1.5 rounded-full bg-charcoal text-white text-xs font-bold cursor-pointer hover:opacity-90 active:scale-95 transition-all shadow-xs"
                                    >
                                        Pantau
                                    </button>
                                </div>

                                <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-amberpastel-light text-amberpastel-dark">
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
                                        class="px-3 py-1.5 rounded-full bg-charcoal text-white text-xs font-bold cursor-pointer hover:opacity-90 active:scale-95 transition-all shadow-xs"
                                    >
                                        Pantau
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Cross-Subject Leger -->
                        <div>
                            <h3 class="text-[15px] font-extrabold text-charcoal mb-2.5">
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

                    <!-- ============================================ -->
                    <!-- TAB 2: ABSENSI (PRESENSI ROMBEL)            -->
                    <!-- ============================================ -->
                    <div v-else-if="activeTab === 'absensi'" class="space-y-4">
                        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-extrabold text-charcoal">Presensi Harian Rombel X DKV 1</h3>
                                <p class="text-xs text-subtext mt-0.5">36 Siswa Terdaftar • 34 Hadir, 1 Sakit, 1 Alpa</p>
                            </div>
                            <button
                                type="button"
                                @click="$emit('showToast', 'Rekap presensi hari ini disimpan dan disinkronkan ke ruang BK.')"
                                class="px-3.5 py-1.5 rounded-full bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer shadow-xs"
                            >
                                Simpan Harian
                            </button>
                        </div>

                        <div class="space-y-2.5">
                            <div
                                v-for="s in rombelStudents"
                                :key="s.nisn"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between"
                            >
                                <div>
                                    <div class="text-sm font-extrabold text-charcoal">{{ s.name }}</div>
                                    <div class="text-[11px] text-subtext">NISN: {{ s.nisn }} • Kehadiran Semester: {{ s.hadirCount }}</div>
                                    <div class="text-[10px] text-charcoal/70 mt-1 italic">{{ s.catatan }}</div>
                                </div>
                                <span
                                    class="text-xs font-bold px-3 py-1 rounded-full shrink-0"
                                    :class="s.status === 'Hadir' ? 'bg-sage-light text-sage-dark' : s.status === 'Sakit' ? 'bg-[#FFF3E0] text-[#E65100]' : 'bg-blossom-light text-blossom-dark'"
                                >
                                    {{ s.status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- TAB 3: LEGER (NILAI LINTAS MAPEL)            -->
                    <!-- ============================================ -->
                    <div v-else-if="activeTab === 'leger'" class="space-y-4">
                        <div class="hero-sky rounded-[26px] p-4 soft-shadow">
                            <h3 class="text-base font-extrabold text-charcoal">Buku Leger Rombel X DKV 1</h3>
                            <p class="text-xs text-charcoal/75 mt-0.5">Pemantauan capaian ketuntasan belajar siswa di seluruh mata pelajaran.</p>
                        </div>

                        <div class="space-y-2.5">
                            <div
                                v-for="m in legerSubjects"
                                :key="m.mapel"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between"
                            >
                                <div>
                                    <div class="text-sm font-extrabold text-charcoal">{{ m.mapel }}</div>
                                    <div class="text-[11px] text-subtext">Pengampu: {{ m.guru }}</div>
                                    <div class="text-[10px] font-bold text-sage-dark mt-0.5">Ketuntasan: {{ m.tuntas }}</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-base font-black text-charcoal">{{ m.avg }}</span>
                                    <div class="text-[10px] text-subtext">Rata-rata</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- TAB 4: LAPORAN (CATATAN & RAPOR PERWALIAN)   -->
                    <!-- ============================================ -->
                    <div v-else-if="activeTab === 'laporan'" class="space-y-4">
                        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow">
                            <h3 class="text-sm font-extrabold text-charcoal">Unduh Rapor &amp; Buku Induk Perwalian</h3>
                            <p class="text-xs text-subtext mt-0.5">Cetak dan unduh leger rombel resmi untuk tanda tangan kepala sekolah.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            <div
                                @click="$emit('showToast', 'Mengunduh Rapor Bayangan X DKV 1 (.pdf)...')"
                                class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow hover:border-charcoal/30 active:scale-[0.99] transition-all flex items-center justify-between"
                            >
                                <div>
                                    <div class="text-sm font-extrabold text-charcoal">Rapor Bayangan Siswa</div>
                                    <div class="text-[11px] text-subtext mt-0.5">36 Berkas PDF Terbundel</div>
                                </div>
                                <span class="w-9 h-9 rounded-2xl bg-canvas border border-[#ECEAE4] flex items-center justify-center text-charcoal shrink-0">
                                    <LmsIcon name="download" className="w-4 h-4" />
                                </span>
                            </div>

                            <div
                                @click="$emit('showToast', 'Mengunduh Buku Leger X DKV 1 (.xlsx)...')"
                                class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow hover:border-charcoal/30 active:scale-[0.99] transition-all flex items-center justify-between"
                            >
                                <div>
                                    <div class="text-sm font-extrabold text-charcoal">Leger Nilai Semester</div>
                                    <div class="text-[11px] text-subtext mt-0.5">Format Excel Lengkap</div>
                                </div>
                                <span class="w-9 h-9 rounded-2xl bg-canvas border border-[#ECEAE4] flex items-center justify-center text-charcoal shrink-0">
                                    <LmsIcon name="download" className="w-4 h-4" />
                                </span>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>
    </div>
</template>
