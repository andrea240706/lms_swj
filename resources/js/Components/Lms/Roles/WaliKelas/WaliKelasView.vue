<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import LmsSkeletonLoader from '@/Components/Lms/LmsSkeletonLoader.vue';
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';
import {
    calculateStudentAvg,
    checkStudentTuntas,
    daftarMapelRombel,
    daftarSiswaPerwalian,
    jadwalHariIni,
    rombelInfo,
} from './waliKelasData';

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

const emit = defineEmits(['openMenu', 'openProfile', 'showToast', 'toggleSidebar', 'openSettings']);

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

// Dynamic Page Titles
const pageTitle = computed(() => {
    const map = {
        home: 'Dashboard Perwalian — X DKV 1',
        presensiPagi: 'Presensi Pagi Kelas (1x Sehari)',
        siswaPerwalian: 'Data Siswa Perwalian — 36 Siswa',
        rekapNilai: 'Rekap Nilai Kelas (Leger Semua Mapel)',
    };
    return map[props.activeTab] || 'Perwalian Rombel X DKV 1';
});

// Reactive copy of students to allow interactive attendance input & counseling log additions
const students = ref(JSON.parse(JSON.stringify(daftarSiswaPerwalian)));

// ========================================================
// TAB 2: PRESENSI PAGI STATE & METHODS
// ========================================================
const presensiMode = ref('input'); // 'input' | 'rekap'
const presensiSearchQuery = ref('');
const rekapFilter = ref('semua'); // 'semua' | 'rendah' | 'ada_alpa'

const filteredPresensiStudents = computed(() => {
    const q = presensiSearchQuery.value.trim().toLowerCase();
    return students.value.filter((s) => {
        if (!q) return true;
        return s.name.toLowerCase().includes(q) || s.nisn.includes(q);
    });
});

const presensiCounters = computed(() => {
    const counts = { Hadir: 0, Sakit: 0, Izin: 0, Alpa: 0, Terlambat: 0 };
    students.value.forEach((s) => {
        if (counts[s.presensiHariIni] !== undefined) {
            counts[s.presensiHariIni]++;
        } else {
            counts.Hadir++;
        }
    });
    return counts;
});

const setPresensiStatus = (studentId, status) => {
    const target = students.value.find((s) => s.id === studentId);
    if (target) {
        target.presensiHariIni = status;
        if (status === 'Hadir') {
            target.keteranganPresensi = 'Hadir apel pagi';
        } else if (status === 'Sakit' && !target.keteranganPresensi.includes('dokter')) {
            target.keteranganPresensi = 'Sakit (Surat keterangan diverifikasi)';
        } else if (status === 'Izin') {
            target.keteranganPresensi = 'Izin keperluan keluarga';
        } else if (status === 'Alpa') {
            target.keteranganPresensi = 'Tanpa keterangan';
        } else if (status === 'Terlambat') {
            target.keteranganPresensi = 'Hadir terlambat apel pagi';
        }
    }
};

const markAllPresent = () => {
    students.value.forEach((s) => {
        s.presensiHariIni = 'Hadir';
        s.keteranganPresensi = 'Hadir apel pagi';
    });
    emit('showToast', 'Semua siswa (36 orang) ditandai Hadir pada apel pagi.');
};

const savePresensiPagi = () => {
    emit('showToast', 'Presensi pagi Rombel X DKV 1 berhasil dikunci dan disinkronkan ke ruang Piket & Orang Tua.');
};

const filteredRekapStudents = computed(() => {
    return students.value.filter((s) => {
        if (rekapFilter.value === 'rendah') {
            return s.rekapPresensi.persen < 85;
        }
        if (rekapFilter.value === 'ada_alpa') {
            return s.rekapPresensi.alpa > 0;
        }
        return true;
    });
});

// ========================================================
// TAB 3: DATA SISWA PERWALIAN STATE & METHODS
// ========================================================
const siswaSearchQuery = ref('');
const siswaGenderFilter = ref('semua'); // 'semua' | 'L' | 'P'
const siswaWarningFilter = ref('semua'); // 'semua' | 'warning' | 'normal'

const filteredDataStudents = computed(() => {
    const q = siswaSearchQuery.value.trim().toLowerCase();
    return students.value.filter((s) => {
        if (q && !s.name.toLowerCase().includes(q) && !s.nisn.includes(q)) {
            return false;
        }
        if (siswaGenderFilter.value !== 'semua' && s.gender !== siswaGenderFilter.value) {
            return false;
        }
        if (siswaWarningFilter.value === 'warning' && !s.earlyWarning) {
            return false;
        }
        if (siswaWarningFilter.value === 'normal' && s.earlyWarning) {
            return false;
        }
        return true;
    });
});

// Modal Detail Siswa & Riwayat Pembinaan
const selectedStudentDetail = ref(null);
const isStudentModalOpen = ref(false);

const openStudentModal = (student) => {
    selectedStudentDetail.value = student;
    isStudentModalOpen.value = true;
};

const closeStudentModal = () => {
    isStudentModalOpen.value = false;
    selectedStudentDetail.value = null;
};

// Form Tambah Catatan Pembinaan
const newCounseling = ref({
    type: 'Akademik',
    note: '',
    action: '',
});

const addCounselingLog = () => {
    if (!newCounseling.value.note.trim()) {
        emit('showToast', 'Tuliskan catatan pembinaan terlebih dahulu.');
        return;
    }
    if (selectedStudentDetail.value) {
        selectedStudentDetail.value.counselingLogs.unshift({
            date: '28 Okt 2026',
            type: newCounseling.value.type,
            note: newCounseling.value.note.trim(),
            action: newCounseling.value.action.trim() || 'Pembinaan wali kelas',
        });
        newCounseling.value.note = '';
        newCounseling.value.action = '';
        emit('showToast', `Catatan pembinaan untuk ${selectedStudentDetail.value.name} berhasil disimpan.`);
    }
};

// ========================================================
// TAB 4: REKAP NILAI KELAS (LEGER SEMUA MAPEL)
// ========================================================
const legerSearchQuery = ref('');
const legerStatusFilter = ref('semua'); // 'semua' | 'top10' | 'remedial'

const rankedStudents = computed(() => {
    // Clone and enrich with avg and tuntas
    const list = students.value.map((s) => {
        const avg = calculateStudentAvg(s.grades);
        const tuntas = checkStudentTuntas(s.grades);
        return {
            ...s,
            avgScore: avg,
            isTuntas: tuntas,
        };
    });

    // Sort descending by avgScore
    list.sort((a, b) => b.avgScore - a.avgScore);

    // Assign ranking
    list.forEach((s, idx) => {
        s.rank = idx + 1;
    });

    return list;
});

const filteredLegerStudents = computed(() => {
    const q = legerSearchQuery.value.trim().toLowerCase();
    return rankedStudents.value.filter((s) => {
        if (q && !s.name.toLowerCase().includes(q) && !s.nisn.includes(q)) {
            return false;
        }
        if (legerStatusFilter.value === 'top10') {
            return s.rank <= 10;
        }
        if (legerStatusFilter.value === 'remedial') {
            return !s.isTuntas;
        }
        return true;
    });
});

const legerStatistics = computed(() => {
    const avgs = rankedStudents.value.map((s) => s.avgScore);
    const sum = avgs.reduce((a, b) => a + b, 0);
    const classAvg = avgs.length ? Math.round((sum / avgs.length) * 10) / 10 : 0;
    const highest = avgs.length ? Math.max(...avgs) : 0;
    const lowest = avgs.length ? Math.min(...avgs) : 0;
    const tuntasCount = rankedStudents.value.filter((s) => s.isTuntas).length;
    const tuntasPct = avgs.length ? Math.round((tuntasCount / avgs.length) * 1000) / 10 : 0;

    return {
        classAvg,
        highest,
        lowest,
        tuntasCount,
        tuntasPct,
    };
});

const getGradeBadgeClass = (score, kkm) => {
    if (score < kkm) {
        return 'bg-blossom-light text-blossom-dark font-black';
    }
    if (score >= 88) {
        return 'bg-sage-light text-sage-dark font-extrabold';
    }
    return 'bg-sky-light text-sky-dark font-bold';
};
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <LmsTopBar
            :role="role"
            :isSidebarCollapsed="isSidebarCollapsed"
            :title="pageTitle"
            @openMenu="$emit('openMenu')"
            @openProfile="$emit('openProfile')"
            @openSettings="$emit('openSettings')"
            @toggleSidebar="$emit('toggleSidebar')"
        />

        <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
            <div class="w-full max-w-[1800px] mx-auto">

                <!-- SKELETON LOADER -->
                <LmsSkeletonLoader
                    v-if="isLoading"
                    :type="activeTab === 'home' ? 'dashboard' : activeTab === 'presensiPagi' ? 'card-list' : activeTab === 'siswaPerwalian' ? 'mapel-list' : 'rekap-list'"
                    :rows="6"
                    :cards="6"
                />

                <!-- CONTENT -->
                <template v-else>
                    <!-- ======================================================== -->
                    <!-- TAB 1: DASHBOARD PERWALIAN                               -->
                    <!-- ======================================================== -->
                    <div v-if="activeTab === 'home'" class="space-y-5">
                        <!-- Hero Banner: Rombel X DKV 1 -->
                        <div class="hero-sky rounded-[28px] p-5 soft-shadow relative overflow-hidden">
                            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="bg-white/95 text-charcoal px-3 py-1 rounded-full text-[11px] font-black shadow-2xs">
                                            Wali Kelas • {{ rombelInfo.name }}
                                        </span>
                                        <span class="text-xs font-bold text-sky-dark flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-[#2E7D32] animate-pulse"></span>
                                            Apel Pagi: 34 Hadir, 1 Sakit, 1 Alpa
                                        </span>
                                    </div>
                                    <h2 class="text-[22px] sm:text-[24px] font-black text-charcoal leading-tight">
                                        Pembinaan Rombel {{ rombelInfo.name }}
                                    </h2>
                                    <p class="text-xs text-charcoal/80 mt-1 max-w-xl">
                                        {{ rombelInfo.jurusan }} • TA {{ rombelInfo.tahunAjaran }} Semester {{ rombelInfo.semester }} • {{ rombelInfo.ruangHomebase }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 self-start md:self-auto shrink-0">
                                    <button
                                        type="button"
                                        @click="$emit('selectTab', 'presensiPagi')"
                                        class="px-4 py-2.5 rounded-2xl bg-charcoal text-white text-xs font-extrabold hover:bg-black active:scale-95 transition-all shadow-sm cursor-pointer flex items-center gap-2"
                                    >
                                        <LmsIcon name="clock" className="w-4 h-4 text-white" />
                                        <span>Presensi Pagi Hari Ini</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 4 KPI Macro Cards -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                            <!-- KPI 1: Kehadiran Hari Ini -->
                            <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-subtext">Kehadiran Hari Ini</span>
                                    <span class="w-8 h-8 rounded-full bg-sage-light text-sage-dark flex items-center justify-center">
                                        <LmsIcon name="clock" className="w-4 h-4" />
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <div class="text-2xl font-black text-charcoal">94.4%</div>
                                    <div class="text-[11px] font-medium text-subtext mt-0.5">34 dari 36 siswa hadir apel</div>
                                </div>
                            </div>

                            <!-- KPI 2: Early Warning -->
                            <div
                                @click="$emit('selectTab', 'siswaPerwalian')"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between cursor-pointer hover:border-blossom-dark/50 transition-colors"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-subtext">Perlu Pembinaan</span>
                                    <span class="w-8 h-8 rounded-full bg-blossom-light text-blossom-dark flex items-center justify-center font-black text-xs">
                                        !
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <div class="text-2xl font-black text-blossom-dark">2 Siswa</div>
                                    <div class="text-[11px] font-medium text-subtext mt-0.5">Alpa berturut-turut & tugas</div>
                                </div>
                            </div>

                            <!-- KPI 3: Rata-rata Leger Kelas -->
                            <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-subtext">Rata-Rata Leger</span>
                                    <span class="w-8 h-8 rounded-full bg-sky-light text-sky-dark flex items-center justify-center">
                                        <LmsIcon name="chart" className="w-4 h-4" />
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <div class="text-2xl font-black text-charcoal">{{ legerStatistics.classAvg }}</div>
                                    <div class="text-[11px] font-medium text-subtext mt-0.5">Tertinggi: {{ legerStatistics.highest }} (Bagas)</div>
                                </div>
                            </div>

                            <!-- KPI 4: Ketuntasan Belajar -->
                            <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-subtext">Ketuntasan KKM</span>
                                    <span class="w-8 h-8 rounded-full bg-sage-light text-sage-dark flex items-center justify-center">
                                        <LmsIcon name="task" className="w-4 h-4" />
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <div class="text-2xl font-black text-[#2E7D32]">{{ legerStatistics.tuntasPct }}%</div>
                                    <div class="text-[11px] font-medium text-subtext mt-0.5">{{ legerStatistics.tuntasCount }} dari 36 siswa tuntas</div>
                                </div>
                            </div>
                        </div>

                        <!-- 2 Columns: Early Warning & Jadwal KBM Hari Ini -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                            <!-- Left: Early Warning Siswa (Pembinaan Prioritas) -->
                            <div class="lg:col-span-7 space-y-3.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-blossom-dark"></span>
                                        <h3 class="text-sm font-black text-charcoal uppercase tracking-wider">
                                            Prioritas Pembinaan Siswa (Early Warning)
                                        </h3>
                                    </div>
                                    <span class="text-[11px] font-bold text-subtext">2 Kasus Aktif</span>
                                </div>

                                <div class="space-y-3">
                                    <!-- Case 1: Aldiansyah Putra -->
                                    <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow space-y-3">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <img
                                                    src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=150&auto=format&fit=crop&q=80"
                                                    alt="Aldiansyah"
                                                    class="w-11 h-11 rounded-full object-cover border border-[#E6E4DF] shrink-0"
                                                />
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-extrabold text-charcoal">Aldiansyah Putra</span>
                                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-blossom-light text-blossom-dark">
                                                            Alpa 3x
                                                        </span>
                                                    </div>
                                                    <div class="text-[11px] text-subtext">
                                                        NISN: 009112001 • Wali: Bambang Supriyanto (0812-3456-7890)
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="text-xs font-black text-blossom-dark px-2.5 py-1 rounded-xl bg-blossom-light/60 shrink-0">
                                                Avg: 68.5 (Remedial)
                                            </span>
                                        </div>

                                        <p class="text-xs text-charcoal/80 bg-canvas rounded-2xl p-3 border border-[#ECEAE4] leading-relaxed">
                                            ⚠️ <strong>Kasus:</strong> Tidak hadir tanpa keterangan selama 3 hari berturut-turut. Rata-rata nilai tugas di bawah KKM. Rekomendasi: Pemanggilan orang tua / koordinasi dengan Guru BK.
                                        </p>

                                        <div class="flex items-center gap-2 pt-1">
                                            <button
                                                type="button"
                                                @click="$emit('showToast', 'Surat pemanggilan wali santri resmi diterbitkan untuk orang tua Aldiansyah Putra.')"
                                                class="flex-1 py-2 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                                            >
                                                Panggil Orang Tua
                                            </button>
                                            <button
                                                type="button"
                                                @click="openStudentModal(students[0])"
                                                class="px-3 py-2 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal text-xs font-bold hover:bg-white active:scale-95 transition-all cursor-pointer"
                                            >
                                                Buka Catatan Kasus
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Case 2: Salsabila Azzahra -->
                                    <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow space-y-3">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3">
                                                <img
                                                    src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80"
                                                    alt="Salsabila"
                                                    class="w-11 h-11 rounded-full object-cover border border-[#E6E4DF] shrink-0"
                                                />
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-extrabold text-charcoal">Salsabila Azzahra</span>
                                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-amberpastel-light text-amberpastel-dark">
                                                            Tugas Menunggak
                                                        </span>
                                                    </div>
                                                    <div class="text-[11px] text-subtext">
                                                        NISN: 009112002 • Wali: Hj. Ratna Juwita (0813-8821-4321)
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="text-xs font-black text-charcoal px-2.5 py-1 rounded-xl bg-canvas border border-[#ECEAE4] shrink-0">
                                                Avg: 74.0
                                            </span>
                                        </div>

                                        <p class="text-xs text-charcoal/80 bg-canvas rounded-2xl p-3 border border-[#ECEAE4] leading-relaxed">
                                            📌 <strong>Kasus:</strong> 2 tugas praktikum kejuruan (Dasar DKV & Tipografi) belum dikumpulkan. Alasan: kendala laptop. Rekomendasi: Pinjam PC di Lab DKV sekolah.
                                        </p>

                                        <div class="flex items-center gap-2 pt-1">
                                            <button
                                                type="button"
                                                @click="$emit('showToast', 'Pesan pengingat tugas berhasil dikirimkan ke WhatsApp Salsabila Azzahra.')"
                                                class="flex-1 py-2 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                                            >
                                                Kirim Pengingat Tugas
                                            </button>
                                            <button
                                                type="button"
                                                @click="openStudentModal(students[1])"
                                                class="px-3 py-2 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal text-xs font-bold hover:bg-white active:scale-95 transition-all cursor-pointer"
                                            >
                                                Catat Bimbingan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Jadwal KBM Rombel Hari Ini -->
                            <div class="lg:col-span-5 space-y-3.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-sky-dark"></span>
                                        <h3 class="text-sm font-black text-charcoal uppercase tracking-wider">
                                            Jadwal KBM Rombel Hari Ini
                                        </h3>
                                    </div>
                                    <span class="text-[11px] font-bold text-subtext">Rabu, 28 Okt</span>
                                </div>

                                <div class="space-y-2.5">
                                    <div
                                        v-for="(j, idx) in jadwalHariIni"
                                        :key="idx"
                                        class="bg-white rounded-[22px] p-3.5 border border-[#ECEAE4] soft-shadow flex items-start gap-3"
                                    >
                                        <div
                                            class="w-2 h-2 rounded-full mt-2 shrink-0"
                                            :class="j.status === 'berlangsung' ? 'bg-[#2E7D32] animate-pulse' : j.status === 'selesai' ? 'bg-sage-dark' : 'bg-muted'"
                                        ></div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-xs font-black text-charcoal truncate">{{ j.kegiatan }}</span>
                                                <span
                                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0"
                                                    :class="j.status === 'berlangsung' ? 'bg-[#E8F5E9] text-[#2E7D32]' : j.status === 'selesai' ? 'bg-muted text-subtext' : 'bg-canvas text-subtext border border-[#ECEAE4]'"
                                                >
                                                    {{ j.status === 'berlangsung' ? 'Sedang KBM' : j.status === 'selesai' ? 'Selesai' : 'Mendatang' }}
                                                </span>
                                            </div>
                                            <div class="text-[11px] text-subtext mt-0.5">
                                                {{ j.jam }} • {{ j.lokasi }}
                                            </div>
                                            <div class="text-[10px] font-semibold text-charcoal/70 mt-1">
                                                Pengampu: {{ j.pengampu }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pengurus Rombel Box -->
                                <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow">
                                    <div class="text-xs font-black text-charcoal mb-2">Pengurus Kelas X DKV 1</div>
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div class="p-2.5 rounded-xl bg-canvas border border-[#ECEAE4]">
                                            <div class="text-[10px] text-subtext font-bold uppercase">Ketua Kelas</div>
                                            <div class="font-extrabold text-charcoal mt-0.5">{{ rombelInfo.ketuaKelas }}</div>
                                        </div>
                                        <div class="p-2.5 rounded-xl bg-canvas border border-[#ECEAE4]">
                                            <div class="text-[10px] text-subtext font-bold uppercase">Wakil Ketua</div>
                                            <div class="font-extrabold text-charcoal mt-0.5">{{ rombelInfo.wakilKetua }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 2: PRESENSI PAGI KELAS (1X SEHARI & REKAP)           -->
                    <!-- ======================================================== -->
                    <div v-else-if="activeTab === 'presensiPagi'" class="space-y-4">
                        <!-- Mode Selector Bar -->
                        <div class="bg-white rounded-[26px] p-3 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-1.5 p-1 bg-muted/70 rounded-2xl w-full sm:w-auto">
                                <button
                                    type="button"
                                    @click="presensiMode = 'input'"
                                    class="flex-1 sm:flex-none px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="presensiMode === 'input' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Input Presensi Pagi (Hari Ini)
                                </button>
                                <button
                                    type="button"
                                    @click="presensiMode = 'rekap'"
                                    class="flex-1 sm:flex-none px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="presensiMode === 'rekap' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Rekapitulasi Presensi Rombel
                                </button>
                            </div>

                            <div v-if="presensiMode === 'input'" class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="markAllPresent"
                                    class="px-3.5 py-2 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-white active:scale-95 transition-all cursor-pointer"
                                >
                                    Tandai Semua Hadir
                                </button>
                                <button
                                    type="button"
                                    @click="savePresensiPagi"
                                    class="px-4 py-2 rounded-xl bg-charcoal text-white text-xs font-extrabold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                                >
                                    Kunci &amp; Kirim ke Piket
                                </button>
                            </div>

                            <div v-else class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="$emit('showToast', 'Mengunduh Rekap Presensi Rombel X DKV 1 (.xlsx)...')"
                                    class="px-4 py-2 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs flex items-center gap-1.5"
                                >
                                    <LmsIcon name="download" className="w-3.5 h-3.5" />
                                    <span>Ekspor Excel</span>
                                </button>
                            </div>
                        </div>

                        <!-- SUB-VIEW 1: INPUT PRESENSI PAGI -->
                        <div v-if="presensiMode === 'input'" class="space-y-4">
                            <!-- Realtime Counters Row -->
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                                <div class="bg-white rounded-[20px] p-3 border border-[#ECEAE4] soft-shadow text-center">
                                    <div class="text-xl font-black text-[#2E7D32]">{{ presensiCounters.Hadir }}</div>
                                    <div class="text-[10px] font-bold text-subtext mt-0.5">Hadir (H)</div>
                                </div>
                                <div class="bg-white rounded-[20px] p-3 border border-[#ECEAE4] soft-shadow text-center">
                                    <div class="text-xl font-black text-[#E65100]">{{ presensiCounters.Sakit }}</div>
                                    <div class="text-[10px] font-bold text-subtext mt-0.5">Sakit (S)</div>
                                </div>
                                <div class="bg-white rounded-[20px] p-3 border border-[#ECEAE4] soft-shadow text-center">
                                    <div class="text-xl font-black text-sky-dark">{{ presensiCounters.Izin }}</div>
                                    <div class="text-[10px] font-bold text-subtext mt-0.5">Izin (I)</div>
                                </div>
                                <div class="bg-white rounded-[20px] p-3 border border-[#ECEAE4] soft-shadow text-center">
                                    <div class="text-xl font-black text-blossom-dark">{{ presensiCounters.Alpa }}</div>
                                    <div class="text-[10px] font-bold text-subtext mt-0.5">Alpa (A)</div>
                                </div>
                                <div class="bg-white rounded-[20px] p-3 border border-[#ECEAE4] soft-shadow text-center col-span-2 sm:col-span-1">
                                    <div class="text-xl font-black text-charcoal">{{ presensiCounters.Terlambat }}</div>
                                    <div class="text-[10px] font-bold text-subtext mt-0.5">Terlambat (T)</div>
                                </div>
                            </div>

                            <!-- Search Input -->
                            <div class="relative">
                                <input
                                    v-model="presensiSearchQuery"
                                    type="text"
                                    placeholder="Cari siswa berdasarkan nama atau NISN..."
                                    class="w-full bg-white border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal placeholder:text-subtext/70 focus:outline-none focus:border-charcoal/40 transition-colors"
                                />
                            </div>

                            <!-- 36 Students Attendance Interactive Rows -->
                            <div class="space-y-2.5">
                                <div
                                    v-for="s in filteredPresensiStudents"
                                    :key="s.id"
                                    class="bg-white rounded-[22px] p-3.5 border border-[#ECEAE4] soft-shadow flex flex-col md:flex-row md:items-center justify-between gap-3 hover:border-charcoal/20 transition-all"
                                >
                                    <!-- Student Info -->
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-7 h-7 rounded-full bg-canvas text-charcoal font-black text-xs flex items-center justify-center shrink-0 border border-[#ECEAE4]">
                                            {{ s.noAbsen }}
                                        </div>
                                        <img
                                            :src="s.avatar"
                                            :alt="s.name"
                                            class="w-9 h-9 rounded-full object-cover border border-[#E6E4DF] shrink-0"
                                        />
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-xs font-extrabold text-charcoal truncate">{{ s.name }}</h4>
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md bg-canvas border border-[#ECEAE4] text-subtext">
                                                    {{ s.gender }}
                                                </span>
                                            </div>
                                            <p class="text-[10px] text-subtext truncate mt-0.5">
                                                NISN: {{ s.nisn }} • {{ s.keteranganPresensi }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Interactive 5 Status Buttons (H / S / I / A / T) -->
                                    <div class="flex items-center gap-1.5 shrink-0 self-end md:self-auto">
                                        <button
                                            type="button"
                                            @click="setPresensiStatus(s.id, 'Hadir')"
                                            class="px-3 py-1.5 rounded-xl text-xs font-black transition-all cursor-pointer"
                                            :class="s.presensiHariIni === 'Hadir' ? 'bg-[#2E7D32] text-white shadow-xs' : 'bg-canvas text-charcoal/70 hover:bg-[#E8F5E9]'"
                                        >
                                            Hadir
                                        </button>
                                        <button
                                            type="button"
                                            @click="setPresensiStatus(s.id, 'Sakit')"
                                            class="px-3 py-1.5 rounded-xl text-xs font-black transition-all cursor-pointer"
                                            :class="s.presensiHariIni === 'Sakit' ? 'bg-[#E65100] text-white shadow-xs' : 'bg-canvas text-charcoal/70 hover:bg-[#FFF3E0]'"
                                        >
                                            Sakit
                                        </button>
                                        <button
                                            type="button"
                                            @click="setPresensiStatus(s.id, 'Izin')"
                                            class="px-3 py-1.5 rounded-xl text-xs font-black transition-all cursor-pointer"
                                            :class="s.presensiHariIni === 'Izin' ? 'bg-sky-dark text-white shadow-xs' : 'bg-canvas text-charcoal/70 hover:bg-sky-light'"
                                        >
                                            Izin
                                        </button>
                                        <button
                                            type="button"
                                            @click="setPresensiStatus(s.id, 'Alpa')"
                                            class="px-3 py-1.5 rounded-xl text-xs font-black transition-all cursor-pointer"
                                            :class="s.presensiHariIni === 'Alpa' ? 'bg-blossom-dark text-white shadow-xs' : 'bg-canvas text-charcoal/70 hover:bg-blossom-light'"
                                        >
                                            Alpa
                                        </button>
                                        <button
                                            type="button"
                                            @click="setPresensiStatus(s.id, 'Terlambat')"
                                            class="px-2.5 py-1.5 rounded-xl text-xs font-black transition-all cursor-pointer"
                                            :class="s.presensiHariIni === 'Terlambat' ? 'bg-charcoal text-white shadow-xs' : 'bg-canvas text-charcoal/70 hover:bg-muted'"
                                        >
                                            T
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SUB-VIEW 2: REKAPITULASI PRESENSI BULANAN -->
                        <div v-else class="space-y-4">
                            <!-- Filter Chips -->
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="rekapFilter = 'semua'"
                                    class="px-3 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer"
                                    :class="rekapFilter === 'semua' ? 'bg-charcoal text-white' : 'bg-white border border-[#ECEAE4] text-charcoal'"
                                >
                                    Semua Siswa (36)
                                </button>
                                <button
                                    type="button"
                                    @click="rekapFilter = 'rendah'"
                                    class="px-3 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer"
                                    :class="rekapFilter === 'rendah' ? 'bg-blossom-dark text-white' : 'bg-white border border-[#ECEAE4] text-charcoal'"
                                >
                                    Kehadiran Rendah (&lt; 85%)
                                </button>
                                <button
                                    type="button"
                                    @click="rekapFilter = 'ada_alpa'"
                                    class="px-3 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer"
                                    :class="rekapFilter === 'ada_alpa' ? 'bg-charcoal text-white' : 'bg-white border border-[#ECEAE4] text-charcoal'"
                                >
                                    Ada Catatan Alpa
                                </button>
                            </div>

                            <!-- Rekap Table / List -->
                            <div class="space-y-2.5">
                                <div
                                    v-for="s in filteredRekapStudents"
                                    :key="s.id"
                                    class="bg-white rounded-[22px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-canvas text-charcoal font-black text-xs flex items-center justify-center shrink-0 border border-[#ECEAE4]">
                                            {{ s.noAbsen }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-extrabold text-charcoal">{{ s.name }}</div>
                                            <div class="text-[11px] text-subtext">NISN: {{ s.nisn }} • 56 Hari KBM Terjadwal</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 sm:gap-4 shrink-0">
                                        <div class="grid grid-cols-4 gap-2 text-center text-xs">
                                            <div class="bg-canvas px-2.5 py-1 rounded-xl">
                                                <div class="font-black text-[#2E7D32]">{{ s.rekapPresensi.hadir }}</div>
                                                <div class="text-[9px] text-subtext font-bold">H</div>
                                            </div>
                                            <div class="bg-canvas px-2.5 py-1 rounded-xl">
                                                <div class="font-black text-[#E65100]">{{ s.rekapPresensi.sakit }}</div>
                                                <div class="text-[9px] text-subtext font-bold">S</div>
                                            </div>
                                            <div class="bg-canvas px-2.5 py-1 rounded-xl">
                                                <div class="font-black text-sky-dark">{{ s.rekapPresensi.izin }}</div>
                                                <div class="text-[9px] text-subtext font-bold">I</div>
                                            </div>
                                            <div class="bg-canvas px-2.5 py-1 rounded-xl">
                                                <div class="font-black text-blossom-dark">{{ s.rekapPresensi.alpa }}</div>
                                                <div class="text-[9px] text-subtext font-bold">A</div>
                                            </div>
                                        </div>

                                        <div class="text-right w-16 shrink-0">
                                            <div
                                                class="text-sm font-black"
                                                :class="s.rekapPresensi.persen < 85 ? 'text-blossom-dark' : 'text-charcoal'"
                                            >
                                                {{ s.rekapPresensi.persen }}%
                                            </div>
                                            <div class="text-[9px] font-bold text-subtext">Hadir</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 3: DATA SISWA PERWALIAN                              -->
                    <!-- ======================================================== -->
                    <div v-else-if="activeTab === 'siswaPerwalian'" class="space-y-4">
                        <!-- Search & Filter Controls -->
                        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col md:flex-row gap-3 items-center justify-between">
                            <div class="relative w-full md:w-80">
                                <input
                                    v-model="siswaSearchQuery"
                                    type="text"
                                    placeholder="Cari siswa berdasarkan nama / NISN..."
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal placeholder:text-subtext/70 focus:outline-none focus:border-charcoal/40 transition-colors"
                                />
                            </div>

                            <div class="flex items-center gap-2 flex-wrap w-full md:w-auto">
                                <!-- Gender Filter -->
                                <button
                                    type="button"
                                    @click="siswaGenderFilter = 'semua'"
                                    class="px-3 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer"
                                    :class="siswaGenderFilter === 'semua' ? 'bg-charcoal text-white' : 'bg-canvas text-charcoal border border-[#ECEAE4]'"
                                >
                                    Semua (36)
                                </button>
                                <button
                                    type="button"
                                    @click="siswaGenderFilter = 'L'"
                                    class="px-3 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer"
                                    :class="siswaGenderFilter === 'L' ? 'bg-charcoal text-white' : 'bg-canvas text-charcoal border border-[#ECEAE4]'"
                                >
                                    Laki-laki (16)
                                </button>
                                <button
                                    type="button"
                                    @click="siswaGenderFilter = 'P'"
                                    class="px-3 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer"
                                    :class="siswaGenderFilter === 'P' ? 'bg-charcoal text-white' : 'bg-canvas text-charcoal border border-[#ECEAE4]'"
                                >
                                    Perempuan (20)
                                </button>

                                <!-- Warning Filter -->
                                <button
                                    type="button"
                                    @click="siswaWarningFilter = siswaWarningFilter === 'warning' ? 'semua' : 'warning'"
                                    class="px-3 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer"
                                    :class="siswaWarningFilter === 'warning' ? 'bg-blossom-dark text-white' : 'bg-canvas text-blossom-dark border border-[#ECEAE4]'"
                                >
                                    Perlu Pembinaan (2)
                                </button>
                            </div>
                        </div>

                        <!-- 36 Students Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                            <div
                                v-for="s in filteredDataStudents"
                                :key="s.id"
                                class="bg-white rounded-[26px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow flex flex-col justify-between hover:border-charcoal/30 transition-all"
                            >
                                <div>
                                    <!-- Top: No Absen, Gender & Warning Pill -->
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-[10px] font-black px-2 py-0.5 rounded-md bg-canvas text-charcoal border border-[#ECEAE4]">
                                            Absen {{ s.noAbsen }}
                                        </span>
                                        <div class="flex items-center gap-1.5">
                                            <span
                                                v-if="s.earlyWarning"
                                                class="text-[9px] font-black px-2 py-0.5 rounded-full bg-blossom-light text-blossom-dark animate-pulse"
                                            >
                                                Perhatian
                                            </span>
                                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-canvas text-subtext border border-[#ECEAE4]">
                                                {{ s.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Student Identity -->
                                    <div class="flex items-center gap-3 mb-3">
                                        <img
                                            :src="s.avatar"
                                            :alt="s.name"
                                            class="w-12 h-12 rounded-full object-cover border border-[#E6E4DF] shrink-0"
                                        />
                                        <div class="min-w-0">
                                            <h4 class="text-sm font-extrabold text-charcoal leading-snug truncate">{{ s.name }}</h4>
                                            <div class="text-[11px] text-subtext mt-0.5">NISN: {{ s.nisn }}</div>
                                        </div>
                                    </div>

                                    <!-- Parent & Contact Info -->
                                    <div class="p-3 rounded-2xl bg-canvas border border-[#ECEAE4] space-y-1 mb-3 text-xs">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] text-subtext font-bold">Wali Santri</span>
                                            <span class="font-extrabold text-charcoal text-[11px]">{{ s.parentName }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] text-subtext font-bold">No. Kontak</span>
                                            <span class="font-bold text-sky-dark text-[11px]">{{ s.parentPhone }}</span>
                                        </div>
                                    </div>

                                    <!-- Attendance & Avg Mini Row -->
                                    <div class="grid grid-cols-2 gap-2 text-center text-xs mb-4">
                                        <div class="p-2 rounded-xl bg-canvas border border-[#ECEAE4]">
                                            <div class="font-black text-charcoal">{{ s.rekapPresensi.persen }}%</div>
                                            <div class="text-[9px] text-subtext font-bold">Kehadiran</div>
                                        </div>
                                        <div class="p-2 rounded-xl bg-canvas border border-[#ECEAE4]">
                                            <div class="font-black text-charcoal">{{ calculateStudentAvg(s.grades) }}</div>
                                            <div class="text-[9px] text-subtext font-bold">Rata-rata Leger</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2 pt-2 border-t border-[#F2F0EC]">
                                    <button
                                        type="button"
                                        @click="openStudentModal(s)"
                                        class="flex-1 py-2 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                                    >
                                        Riwayat Pembinaan
                                    </button>
                                    <button
                                        type="button"
                                        @click="$emit('showToast', `Membuka WhatsApp ke wali santri ${s.parentName} (${s.parentPhone})...`)"
                                        class="px-3 py-2 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-white active:scale-95 transition-all cursor-pointer"
                                        title="Hubungi Orang Tua via WhatsApp"
                                    >
                                        WA
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 4: REKAP NILAI KELAS (LEGER SEMUA MAPEL)             -->
                    <!-- ======================================================== -->
                    <div v-else-if="activeTab === 'rekapNilai'" class="space-y-4">
                        <!-- Top Summary Card -->
                        <div class="hero-sky rounded-[28px] p-5 soft-shadow flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <span class="bg-white/95 text-charcoal px-3 py-1 rounded-full text-[10px] font-black shadow-2xs">
                                    Leger Rombel • 7 Mata Pelajaran
                                </span>
                                <h3 class="text-xl font-black text-charcoal mt-2">
                                    Buku Leger Nilai Rombel X DKV 1
                                </h3>
                                <p class="text-xs text-charcoal/80 mt-0.5">
                                    Rata-rata Kelas: <strong>{{ legerStatistics.classAvg }}</strong> • Ketuntasan: <strong>{{ legerStatistics.tuntasPct }}%</strong> ({{ legerStatistics.tuntasCount }}/36 Siswa)
                                </p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button
                                    type="button"
                                    @click="$emit('showToast', 'Mengunduh Buku Leger Lengkap X DKV 1 format Excel (.xlsx)...')"
                                    class="px-4 py-2.5 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs flex items-center gap-1.5"
                                >
                                    <LmsIcon name="download" className="w-4 h-4 text-white" />
                                    <span>Unduh Leger (.xlsx)</span>
                                </button>
                            </div>
                        </div>

                        <!-- Filter & Search Controls -->
                        <div class="bg-white rounded-[24px] p-3.5 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="relative w-full sm:w-72">
                                <input
                                    v-model="legerSearchQuery"
                                    type="text"
                                    placeholder="Cari siswa atau NISN..."
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2 text-xs text-charcoal placeholder:text-subtext/70 focus:outline-none focus:border-charcoal/40 transition-colors"
                                />
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="legerStatusFilter = 'semua'"
                                    class="px-3 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer"
                                    :class="legerStatusFilter === 'semua' ? 'bg-charcoal text-white' : 'bg-canvas text-charcoal border border-[#ECEAE4]'"
                                >
                                    Semua (36)
                                </button>
                                <button
                                    type="button"
                                    @click="legerStatusFilter = 'top10'"
                                    class="px-3 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer"
                                    :class="legerStatusFilter === 'top10' ? 'bg-charcoal text-white' : 'bg-canvas text-charcoal border border-[#ECEAE4]'"
                                >
                                    Top 10 Rombel
                                </button>
                                <button
                                    type="button"
                                    @click="legerStatusFilter = 'remedial'"
                                    class="px-3 py-1.5 rounded-full text-xs font-bold transition-all cursor-pointer"
                                    :class="legerStatusFilter === 'remedial' ? 'bg-blossom-dark text-white' : 'bg-canvas text-blossom-dark border border-[#ECEAE4]'"
                                >
                                    Butuh Remedial ({{ rankedStudents.filter(s => !s.isTuntas).length }})
                                </button>
                            </div>
                        </div>

                        <!-- Responsive Matrix Leger Table -->
                        <div class="bg-white rounded-[26px] border border-[#ECEAE4] soft-shadow overflow-hidden">
                            <div class="overflow-x-auto no-scrollbar">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-canvas border-b border-[#ECEAE4] text-[10px] font-black uppercase tracking-wider text-subtext">
                                            <th class="py-3.5 px-3 text-center w-12">Rank</th>
                                            <th class="py-3.5 px-4 min-w-[180px]">Siswa</th>
                                            <th
                                                v-for="m in daftarMapelRombel"
                                                :key="m.code"
                                                class="py-3.5 px-2.5 text-center min-w-[70px]"
                                                :title="`${m.name} (KKM ${m.kkm}) - Guru: ${m.guru}`"
                                            >
                                                {{ m.code.toUpperCase() }}<br/>
                                                <span class="text-[9px] font-medium text-subtext/70">KKM {{ m.kkm }}</span>
                                            </th>
                                            <th class="py-3.5 px-3 text-center min-w-[80px]">Rata-rata</th>
                                            <th class="py-3.5 px-3 text-center min-w-[90px]">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#F2F0EC]">
                                        <tr
                                            v-for="s in filteredLegerStudents"
                                            :key="s.id"
                                            class="hover:bg-canvas/60 transition-colors"
                                        >
                                            <!-- Rank Column -->
                                            <td class="py-3 px-3 text-center">
                                                <span
                                                    class="inline-block w-6 h-6 rounded-full text-xs font-black leading-6 text-center"
                                                    :class="s.rank === 1 ? 'bg-[#FFD700] text-black shadow-xs' : s.rank === 2 ? 'bg-[#C0C0C0] text-black' : s.rank === 3 ? 'bg-[#CD7F32] text-white' : 'text-subtext'"
                                                >
                                                    {{ s.rank }}
                                                </span>
                                            </td>

                                            <!-- Student Name & NISN -->
                                            <td class="py-3 px-4">
                                                <div class="font-extrabold text-charcoal">{{ s.name }}</div>
                                                <div class="text-[10px] text-subtext">NISN: {{ s.nisn }} • Absen {{ s.noAbsen }}</div>
                                            </td>

                                            <!-- Subject Scores Columns -->
                                            <td
                                                v-for="m in daftarMapelRombel"
                                                :key="m.code"
                                                class="py-3 px-2 text-center"
                                            >
                                                <span
                                                    class="inline-block px-2 py-0.5 rounded-lg text-xs"
                                                    :class="getGradeBadgeClass(s.grades[m.code], m.kkm)"
                                                >
                                                    {{ s.grades[m.code] }}
                                                </span>
                                            </td>

                                            <!-- Average Score -->
                                            <td class="py-3 px-3 text-center font-black text-sm text-charcoal">
                                                {{ s.avgScore }}
                                            </td>

                                            <!-- Status Tuntas / Remedial -->
                                            <td class="py-3 px-3 text-center">
                                                <span
                                                    class="inline-block text-[10px] font-black px-2.5 py-1 rounded-full"
                                                    :class="s.isTuntas ? 'bg-sage-light text-sage-dark' : 'bg-blossom-light text-blossom-dark'"
                                                >
                                                    {{ s.isTuntas ? 'TUNTAS' : 'REMEDIAL' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODAL: DETAIL SISWA & RIWAYAT PEMBINAAN WALI KELAS          -->
        <!-- ============================================================ -->
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isStudentModalOpen && selectedStudentDetail"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
                @click.self="closeStudentModal"
            >
                <div
                    class="bg-white w-full max-w-xl rounded-t-[32px] sm:rounded-[32px] p-5 sm:p-6 shadow-2xl max-h-[90vh] overflow-y-auto no-scrollbar space-y-4"
                >
                    <!-- Modal Header -->
                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-[#ECEAE4]">
                        <div class="flex items-center gap-3">
                            <img
                                :src="selectedStudentDetail.avatar"
                                :alt="selectedStudentDetail.name"
                                class="w-12 h-12 rounded-full object-cover border border-[#ECEAE4] shrink-0"
                            />
                            <div>
                                <h3 class="text-base font-extrabold text-charcoal">{{ selectedStudentDetail.name }}</h3>
                                <p class="text-xs text-subtext">
                                    NISN: {{ selectedStudentDetail.nisn }} • Absen {{ selectedStudentDetail.noAbsen }} • {{ selectedStudentDetail.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="closeStudentModal"
                            class="w-8 h-8 rounded-full bg-canvas text-charcoal flex items-center justify-center hover:bg-muted font-bold text-xs cursor-pointer"
                        >
                            ✕
                        </button>
                    </div>

                    <!-- Student Biodata & Contact -->
                    <div class="p-3.5 rounded-2xl bg-canvas border border-[#ECEAE4] space-y-1.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-subtext font-bold">Wali Santri:</span>
                            <span class="font-extrabold text-charcoal">{{ selectedStudentDetail.parentName }} ({{ selectedStudentDetail.parentRelation }})</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-subtext font-bold">No. WhatsApp Wali:</span>
                            <span class="font-bold text-sky-dark">{{ selectedStudentDetail.parentPhone }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-subtext font-bold">Alamat Rumah:</span>
                            <span class="font-semibold text-charcoal truncate max-w-[60%]">{{ selectedStudentDetail.address }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-subtext font-bold">Status Berkas:</span>
                            <span class="font-bold text-sage-dark">{{ selectedStudentDetail.statusBerkas }}</span>
                        </div>
                    </div>

                    <!-- Riwayat Catatan Pembinaan & Kasus -->
                    <div>
                        <h4 class="text-xs font-black text-charcoal uppercase tracking-wider mb-2">
                            Buku Catatan Pembinaan / Kasus Siswa
                        </h4>

                        <div v-if="selectedStudentDetail.counselingLogs.length" class="space-y-2 mb-4">
                            <div
                                v-for="(log, idx) in selectedStudentDetail.counselingLogs"
                                :key="idx"
                                class="p-3 rounded-2xl bg-white border border-[#ECEAE4] soft-shadow space-y-1 text-xs"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-canvas border border-[#ECEAE4] text-charcoal">
                                        {{ log.type }}
                                    </span>
                                    <span class="text-[10px] text-subtext">{{ log.date }}</span>
                                </div>
                                <p class="text-charcoal/90 font-medium">{{ log.note }}</p>
                                <p class="text-[11px] font-bold text-sky-dark">Tindak Lanjut: {{ log.action }}</p>
                            </div>
                        </div>

                        <div v-else class="text-center py-5 bg-canvas rounded-2xl border border-dashed border-[#ECEAE4] text-subtext text-xs font-medium mb-4">
                            Belum ada catatan pelanggaran atau kasus. Siswa berperilaku tertib.
                        </div>
                    </div>

                    <!-- Form Tambah Catatan Pembinaan Baru -->
                    <div class="pt-3 border-t border-[#ECEAE4] space-y-2.5">
                        <h4 class="text-xs font-black text-charcoal uppercase tracking-wider">
                            + Tambah Catatan Pembinaan Wali Kelas
                        </h4>

                        <div class="flex items-center gap-2">
                            <button
                                v-for="t in ['Akademik', 'Kehadiran', 'Perilaku', 'Prestasi']"
                                :key="t"
                                type="button"
                                @click="newCounseling.type = t"
                                class="px-3 py-1 rounded-full text-xs font-bold transition-all cursor-pointer"
                                :class="newCounseling.type === t ? 'bg-charcoal text-white' : 'bg-canvas text-charcoal border border-[#ECEAE4]'"
                            >
                                {{ t }}
                            </button>
                        </div>

                        <textarea
                            v-model="newCounseling.note"
                            rows="2"
                            placeholder="Tuliskan catatan kejadian / permasalahan / prestasi siswa..."
                            class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl p-3 text-xs text-charcoal placeholder:text-subtext/70 focus:outline-none focus:border-charcoal/40 transition-colors resize-none"
                        ></textarea>

                        <input
                            v-model="newCounseling.action"
                            type="text"
                            placeholder="Rencana tindak lanjut (misal: panggil wali / bimbingan BK)..."
                            class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2 text-xs text-charcoal placeholder:text-subtext/70 focus:outline-none focus:border-charcoal/40 transition-colors"
                        />

                        <div class="flex gap-2 pt-1">
                            <button
                                type="button"
                                @click="closeStudentModal"
                                class="flex-1 py-2.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-muted active:scale-95 transition-all cursor-pointer"
                            >
                                Tutup
                            </button>
                            <button
                                type="button"
                                @click="addCounselingLog"
                                class="flex-1 py-2.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                            >
                                Simpan Catatan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
