<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import LmsSkeletonLoader from '@/Components/Lms/LmsSkeletonLoader.vue';
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';
import {
    adminStats,
    initialJadwalMingguan,
    initialPengumumanList,
    initialPlottingGuruMapel,
    initialPlottingWali,
    initialUsersList,
    masterJurusan,
    masterMapel,
    masterRombel,
    masterTahunAjaran,
} from './adminData';

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

const emit = defineEmits(['openMenu', 'openProfile', 'showToast', 'toggleSidebar', 'selectTab', 'openSettings']);

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
        home: 'Operasional Akademik Sekolah',
        pengguna: 'Manajemen Pengguna (Siswa, Guru, Wali)',
        master: 'Data Master (TA, Jurusan, Kelas, Mapel)',
        penugasan: 'Penugasan & Jadwal (Plotting Guru & Jadwal)',
        pengumuman: 'Pengumuman & Broadcast Sekolah',
    };
    return map[props.activeTab] || 'Operasional Akademik Sekolah';
});

// ========================================================
// TAB 2: MANAJEMEN PENGGUNA STATE & METHODS
// ========================================================
const userList = ref(JSON.parse(JSON.stringify(initialUsersList)));
const userRoleFilter = ref('semua'); // 'semua' | 'siswa' | 'guru' | 'wali'
const userSearchQuery = ref('');

const filteredUsers = computed(() => {
    const q = userSearchQuery.value.trim().toLowerCase();
    return userList.value.filter((u) => {
        if (userRoleFilter.value !== 'semua' && u.role !== userRoleFilter.value) {
            return false;
        }
        if (q) {
            return (
                u.name.toLowerCase().includes(q) ||
                u.identifier.toLowerCase().includes(q) ||
                u.rombel.toLowerCase().includes(q) ||
                u.email.toLowerCase().includes(q)
            );
        }
        return true;
    });
});

const handleResetPassword = (user) => {
    const tempPassword = `SWJ-${Math.floor(1000 + Math.random() * 9000)}`;
    emit('showToast', `Sandi sementara [${tempPassword}] berhasil dibuat untuk ${user.name}. Notifikasi dikirim ke email.`);
};

const handleToggleLockUser = (user) => {
    user.status = user.status === 'Terkunci' ? 'Aktif' : 'Terkunci';
    emit('showToast', `Status akun ${user.name} diperbarui menjadi ${user.status}.`);
};

// ========================================================
// TAB 3: DATA MASTER STATE & METHODS
// ========================================================
const masterSubTab = ref('jurusan'); // 'ta' | 'jurusan' | 'rombel' | 'mapel'

// ========================================================
// TAB 4: PENUGASAN & JADWAL STATE & METHODS
// ========================================================
const penugasanMode = ref('wali'); // 'wali' | 'guruMapel' | 'jadwal'
const selectedHariJadwal = ref('Senin');

const plottingWali = ref(JSON.parse(JSON.stringify(initialPlottingWali)));
const plottingGuruMapel = ref(JSON.parse(JSON.stringify(initialPlottingGuruMapel)));
const jadwalMingguan = ref(JSON.parse(JSON.stringify(initialJadwalMingguan)));

const filteredJadwal = computed(() => {
    return jadwalMingguan.value.filter((j) => j.hari === selectedHariJadwal.value);
});

// ========================================================
// TAB 5: PENGUMUMAN STATE & METHODS
// ========================================================
const pengumumanList = ref(JSON.parse(JSON.stringify(initialPengumumanList)));
const isPengumumanModalOpen = ref(false);
const newPengumuman = ref({
    title: '',
    target: 'Semua Pengguna',
    urgensi: 'Normal',
    content: '',
});

const openNewPengumumanModal = () => {
    newPengumuman.value = {
        title: '',
        target: 'Semua Pengguna',
        urgensi: 'Normal',
        content: '',
    };
    isPengumumanModalOpen.value = true;
};

const savePengumuman = () => {
    if (!newPengumuman.value.title.trim() || !newPengumuman.value.content.trim()) {
        emit('showToast', 'Lengkapi judul dan isi pengumuman terlebih dahulu.');
        return;
    }
    pengumumanList.value.unshift({
        id: Date.now(),
        title: newPengumuman.value.title.trim(),
        target: newPengumuman.value.target,
        targetBadge: 'bg-charcoal text-white',
        urgensi: newPengumuman.value.urgensi,
        urgensiBadge: newPengumuman.value.urgensi === 'Penting' ? 'bg-blossom-light text-blossom-dark' : 'bg-sage-light text-sage-dark',
        status: 'Tayang',
        date: '28 Okt 2026',
        author: 'Rizky Pratama (Operator TU)',
        content: newPengumuman.value.content.trim(),
        views: 1,
    });
    isPengumumanModalOpen.value = false;
    emit('showToast', 'Pengumuman sekolah berhasil dipublikasikan ke seluruh target pengguna.');
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
                    :type="activeTab === 'home' ? 'dashboard' : activeTab === 'master' ? 'mapel-list' : 'card-list'"
                    :rows="6"
                    :cards="6"
                />

                <!-- CONTENT -->
                <template v-else>
                    <!-- ======================================================== -->
                    <!-- TAB 1: OPERASIONAL AKADEMIK (DASHBOARD OPERATOR)         -->
                    <!-- ======================================================== -->
                    <div v-if="activeTab === 'home'" class="space-y-5">
                        <!-- Hero Banner: Operational Overview -->
                        <div class="hero-lavender rounded-[28px] p-5 soft-shadow relative overflow-hidden">
                            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="bg-white/95 text-charcoal px-3 py-1 rounded-full text-[11px] font-black shadow-2xs">
                                            Operator Sekolah • Tata Usaha
                                        </span>
                                        <span class="text-xs font-bold text-lavender-dark flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-[#2E7D32] animate-pulse"></span>
                                            Dapodik Terkoneksi: {{ adminStats.terakhirSinkronDapodik }}
                                        </span>
                                    </div>
                                    <h2 class="text-[22px] sm:text-[24px] font-black text-charcoal leading-tight">
                                        Pusat Operasional Akademik
                                    </h2>
                                    <p class="text-xs text-charcoal/80 mt-1 max-w-xl">
                                        Tahun Ajaran {{ adminStats.tahunAjaranAktif }} • Pengelolaan 1.240 Siswa, 68 Tenaga Pendidik, &amp; 42 Rombel
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 self-start md:self-auto shrink-0">
                                    <button
                                        type="button"
                                        @click="$emit('showToast', 'Sinkronisasi Dapodik berhasil diproses. Seluruh data siswa dan rombel terverifikasi.')"
                                        class="px-4 py-2.5 rounded-2xl bg-charcoal text-white text-xs font-extrabold hover:bg-black active:scale-95 transition-all shadow-sm cursor-pointer flex items-center gap-2"
                                    >
                                        <LmsIcon name="clock" className="w-4 h-4 text-white" />
                                        <span>Sinkron Dapodik</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 4 Operational KPI Cards -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                            <!-- KPI 1: Siswa -->
                            <div
                                @click="$emit('selectTab', 'pengguna')"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between cursor-pointer hover:border-charcoal/30 transition-colors"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-subtext">Total Siswa Aktif</span>
                                    <span class="w-8 h-8 rounded-full bg-sky-light text-sky-dark flex items-center justify-center font-bold text-xs">
                                        <LmsIcon name="user" className="w-4 h-4" />
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <div class="text-2xl font-black text-charcoal">{{ adminStats.totalSiswa.toLocaleString() }}</div>
                                    <div class="text-[11px] font-medium text-subtext mt-0.5">42 Rombel (X, XI, XII)</div>
                                </div>
                            </div>

                            <!-- KPI 2: Guru & Pendidik -->
                            <div
                                @click="$emit('selectTab', 'pengguna')"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between cursor-pointer hover:border-charcoal/30 transition-colors"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-subtext">Guru &amp; Pendidik</span>
                                    <span class="w-8 h-8 rounded-full bg-sage-light text-sage-dark flex items-center justify-center font-bold text-xs">
                                        <LmsIcon name="book" className="w-4 h-4" />
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <div class="text-2xl font-black text-charcoal">{{ adminStats.totalGuru }} Guru</div>
                                    <div class="text-[11px] font-medium text-subtext mt-0.5">42 Wali Kelas Terplot</div>
                                </div>
                            </div>

                            <!-- KPI 3: Plotting Jadwal -->
                            <div
                                @click="$emit('selectTab', 'penugasan')"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between cursor-pointer hover:border-charcoal/30 transition-colors"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-subtext">Jadwal Mingguan</span>
                                    <span class="w-8 h-8 rounded-full bg-lavender-light text-lavender-dark flex items-center justify-center font-bold text-xs">
                                        <LmsIcon name="clock" className="w-4 h-4" />
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <div class="text-2xl font-black text-charcoal">{{ adminStats.jadwalTerverifikasi }} Sesi</div>
                                    <div class="text-[11px] font-medium text-subtext mt-0.5">100% Terdistribusi Valid</div>
                                </div>
                            </div>

                            <!-- KPI 4: Konflik Jadwal -->
                            <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-subtext">Status Konflik</span>
                                    <span class="w-8 h-8 rounded-full bg-[#E8F5E9] text-[#2E7D32] flex items-center justify-center font-black text-xs">
                                        ✓
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <div class="text-2xl font-black text-[#2E7D32]">0 Konflik</div>
                                    <div class="text-[11px] font-medium text-subtext mt-0.5">Bebas bentrok ruang &amp; guru</div>
                                </div>
                            </div>
                        </div>

                        <!-- 2 Columns: Tindakan Cepat Operator & Live Audit Trail -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                            <!-- Left: Tindakan Cepat Operator -->
                            <div class="lg:col-span-7 space-y-3.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-charcoal"></span>
                                        <h3 class="text-sm font-black text-charcoal uppercase tracking-wider">
                                            Aksi Cepat Operasional
                                        </h3>
                                    </div>
                                    <span class="text-[11px] font-bold text-subtext">Pintasan Operator</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div
                                        @click="$emit('selectTab', 'penugasan')"
                                        class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
                                    >
                                        <div>
                                            <span class="w-9 h-9 rounded-2xl bg-lavender-light text-lavender-dark flex items-center justify-center mb-2.5 font-bold">
                                                <LmsIcon name="clock" className="w-4 h-4" />
                                            </span>
                                            <h4 class="text-sm font-extrabold text-charcoal">Plotting Guru &amp; Jadwal</h4>
                                            <p class="text-[11px] text-subtext mt-1">Petakan guru pengampu, wali kelas rombel, dan jadwal pelajaran mingguan.</p>
                                        </div>
                                        <div class="text-xs font-bold text-charcoal mt-3 flex items-center gap-1">
                                            Buka Plotting →
                                        </div>
                                    </div>

                                    <div
                                        @click="$emit('selectTab', 'pengguna')"
                                        class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
                                    >
                                        <div>
                                            <span class="w-9 h-9 rounded-2xl bg-sky-light text-sky-dark flex items-center justify-center mb-2.5 font-bold">
                                                <LmsIcon name="user" className="w-4 h-4" />
                                            </span>
                                            <h4 class="text-sm font-extrabold text-charcoal">Manajemen Pengguna</h4>
                                            <p class="text-[11px] text-subtext mt-1">Kelola akun Siswa, Guru, dan Wali Kelas. Reset sandi sementara instan.</p>
                                        </div>
                                        <div class="text-xs font-bold text-charcoal mt-3 flex items-center gap-1">
                                            Kelola Akun →
                                        </div>
                                    </div>

                                    <div
                                        @click="$emit('selectTab', 'master')"
                                        class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
                                    >
                                        <div>
                                            <span class="w-9 h-9 rounded-2xl bg-sage-light text-sage-dark flex items-center justify-center mb-2.5 font-bold">
                                                <LmsIcon name="book" className="w-4 h-4" />
                                            </span>
                                            <h4 class="text-sm font-extrabold text-charcoal">Data Master Akademik</h4>
                                            <p class="text-[11px] text-subtext mt-1">Konfigurasi Tahun Ajaran, Program Keahlian (Jurusan), Rombel, &amp; Mapel.</p>
                                        </div>
                                        <div class="text-xs font-bold text-charcoal mt-3 flex items-center gap-1">
                                            Kelola Master →
                                        </div>
                                    </div>

                                    <div
                                        @click="openNewPengumumanModal"
                                        class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
                                    >
                                        <div>
                                            <span class="w-9 h-9 rounded-2xl bg-amberpastel-light text-amberpastel-dark flex items-center justify-center mb-2.5 font-bold">
                                                <LmsIcon name="task" className="w-4 h-4" />
                                            </span>
                                            <h4 class="text-sm font-extrabold text-charcoal">Buat Pengumuman</h4>
                                            <p class="text-[11px] text-subtext mt-1">Broadcast informasi KBM, surat edaran, atau pengingat ke siswa dan guru.</p>
                                        </div>
                                        <div class="text-xs font-bold text-charcoal mt-3 flex items-center gap-1">
                                            + Broadcast Baru →
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Live Log Aktivitas Operator -->
                            <div class="lg:col-span-5 space-y-3.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-sage-dark"></span>
                                        <h3 class="text-sm font-black text-charcoal uppercase tracking-wider">
                                            Log Mutasi &amp; Aktivitas
                                        </h3>
                                    </div>
                                    <span class="text-[11px] font-bold text-subtext">Realtime</span>
                                </div>

                                <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow space-y-3">
                                    <div class="space-y-3 text-xs">
                                        <div class="flex items-start gap-2.5 pb-2.5 border-b border-[#F2F0EC]">
                                            <span class="w-2 h-2 rounded-full bg-[#2E7D32] mt-1.5 shrink-0"></span>
                                            <div>
                                                <div class="font-extrabold text-charcoal">Sinkronisasi Dapodik Berhasil</div>
                                                <div class="text-[10px] text-subtext">Hari ini, 06:30 WIB • 1.240 data siswa diverifikasi</div>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-2.5 pb-2.5 border-b border-[#F2F0EC]">
                                            <span class="w-2 h-2 rounded-full bg-sky-dark mt-1.5 shrink-0"></span>
                                            <div>
                                                <div class="font-extrabold text-charcoal">Reset Sandi: Dimas Anggara (XII RPL 2)</div>
                                                <div class="text-[10px] text-subtext">Hari ini, 07:15 WIB • Dibuatkan sandi sementara</div>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-2.5 pb-2.5 border-b border-[#F2F0EC]">
                                            <span class="w-2 h-2 rounded-full bg-lavender-dark mt-1.5 shrink-0"></span>
                                            <div>
                                                <div class="font-extrabold text-charcoal">Plotting Wali Kelas X DKV 1</div>
                                                <div class="text-[10px] text-subtext">Kemarin, 14:20 WIB • Siti Rahma, M.Pd disahkan</div>
                                            </div>
                                        </div>
                                        <div class="flex items-start gap-2.5">
                                            <span class="w-2 h-2 rounded-full bg-amberpastel-dark mt-1.5 shrink-0"></span>
                                            <div>
                                                <div class="font-extrabold text-charcoal">Publikasi Pengumuman ATS Ganjil</div>
                                                <div class="text-[10px] text-subtext">Kemarin, 10:00 WIB • Ditayangkan ke seluruh pengguna</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 2: MANAJEMEN PENGGUNA (SISWA, GURU, WALI)            -->
                    <!-- ======================================================== -->
                    <div v-else-if="activeTab === 'pengguna'" class="space-y-4">
                        <!-- Top Filter & Action Bar -->
                        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col md:flex-row gap-3 items-center justify-between">
                            <div class="flex items-center gap-1.5 p-1 bg-muted/70 rounded-2xl w-full md:w-auto flex-wrap">
                                <button
                                    type="button"
                                    @click="userRoleFilter = 'semua'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="userRoleFilter === 'semua' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Semua Akun ({{ userList.length }})
                                </button>
                                <button
                                    type="button"
                                    @click="userRoleFilter = 'siswa'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="userRoleFilter === 'siswa' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Siswa ({{ userList.filter(u => u.role === 'siswa').length }})
                                </button>
                                <button
                                    type="button"
                                    @click="userRoleFilter = 'guru'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="userRoleFilter === 'guru' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Guru ({{ userList.filter(u => u.role === 'guru').length }})
                                </button>
                                <button
                                    type="button"
                                    @click="userRoleFilter = 'wali'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="userRoleFilter === 'wali' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Wali Kelas ({{ userList.filter(u => u.role === 'wali').length }})
                                </button>
                            </div>

                            <div class="flex items-center gap-2 w-full md:w-auto">
                                <div class="relative flex-1 md:w-64">
                                    <input
                                        v-model="userSearchQuery"
                                        type="text"
                                        placeholder="Cari nama, NIP, NISN, rombel..."
                                        class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2 text-xs text-charcoal placeholder:text-subtext/70 focus:outline-none focus:border-charcoal/40 transition-colors"
                                    />
                                </div>
                                <button
                                    type="button"
                                    @click="$emit('showToast', 'Modal Tambah Pengguna Baru dibuka.')"
                                    class="px-3.5 py-2 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs shrink-0"
                                >
                                    + Pengguna
                                </button>
                            </div>
                        </div>

                        <!-- User List Cards -->
                        <div class="space-y-2.5">
                            <div
                                v-for="u in filteredUsers"
                                :key="u.id"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-charcoal/30 transition-all"
                            >
                                <div class="flex items-center gap-3 min-w-0">
                                    <div
                                        class="w-11 h-11 rounded-2xl flex items-center justify-center font-black text-sm shrink-0"
                                        :class="u.role === 'siswa' ? 'bg-sky-light text-sky-dark' : u.role === 'guru' ? 'bg-sage-light text-sage-dark' : 'bg-lavender-light text-lavender-dark'"
                                    >
                                        {{ u.name.charAt(0) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-sm font-extrabold text-charcoal truncate">{{ u.name }}</h4>
                                            <span
                                                class="text-[9px] font-black px-2 py-0.5 rounded-full uppercase"
                                                :class="u.role === 'siswa' ? 'bg-sky-light text-sky-dark' : u.role === 'guru' ? 'bg-sage-light text-sage-dark' : 'bg-lavender-light text-lavender-dark'"
                                            >
                                                {{ u.role === 'wali' ? 'Wali Kelas' : u.role }}
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-subtext truncate mt-0.5">
                                            {{ u.role === 'siswa' ? 'NISN: ' : 'NIP: ' }}{{ u.identifier }} • {{ u.rombel }} • {{ u.email }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                                    <span
                                        class="text-[10px] font-bold px-2.5 py-1 rounded-full"
                                        :class="u.status === 'Aktif' ? 'bg-[#E8F5E9] text-[#2E7D32]' : u.status === 'Pantauan' ? 'bg-blossom-light text-blossom-dark' : 'bg-muted text-subtext'"
                                    >
                                        {{ u.status }}
                                    </span>
                                    <button
                                        type="button"
                                        @click="handleResetPassword(u)"
                                        class="px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal text-xs font-bold hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                                        title="Reset Kata Sandi"
                                    >
                                        Reset Sandi
                                    </button>
                                    <button
                                        type="button"
                                        @click="handleToggleLockUser(u)"
                                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                        :class="u.status === 'Terkunci' ? 'bg-charcoal text-white' : 'bg-canvas border border-[#ECEAE4] text-subtext hover:text-charcoal'"
                                    >
                                        {{ u.status === 'Terkunci' ? 'Buka Kunci' : 'Kunci' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 3: DATA MASTER (TA, JURUSAN, KELAS, MAPEL)           -->
                    <!-- ======================================================== -->
                    <div v-else-if="activeTab === 'master'" class="space-y-4">
                        <!-- Sub-tab switcher -->
                        <div class="bg-white rounded-[26px] p-3 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-1.5 p-1 bg-muted/70 rounded-2xl w-full sm:w-auto flex-wrap">
                                <button
                                    type="button"
                                    @click="masterSubTab = 'jurusan'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="masterSubTab === 'jurusan' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Jurusan (4)
                                </button>
                                <button
                                    type="button"
                                    @click="masterSubTab = 'rombel'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="masterSubTab === 'rombel' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Rombel Kelas (42)
                                </button>
                                <button
                                    type="button"
                                    @click="masterSubTab = 'mapel'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="masterSubTab === 'mapel' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Mata Pelajaran (58)
                                </button>
                                <button
                                    type="button"
                                    @click="masterSubTab = 'ta'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="masterSubTab === 'ta' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Tahun Ajaran
                                </button>
                            </div>

                            <button
                                type="button"
                                @click="$emit('showToast', 'Modal Tambah Master Data Baru dibuka.')"
                                class="px-4 py-2 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs self-start sm:self-auto"
                            >
                                + Tambah Data Master
                            </button>
                        </div>

                        <!-- SUB 1: JURUSAN -->
                        <div v-if="masterSubTab === 'jurusan'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                v-for="j in masterJurusan"
                                :key="j.code"
                                class="bg-white rounded-[26px] p-5 border border-[#ECEAE4] soft-shadow flex flex-col justify-between"
                            >
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-black px-2.5 py-1 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal">
                                            Kode: {{ j.code }}
                                        </span>
                                        <span class="text-[11px] font-bold text-subtext">{{ j.tingkat }}</span>
                                    </div>
                                    <h4 class="text-base font-extrabold text-charcoal mt-1">{{ j.name }}</h4>
                                    <p class="text-xs text-subtext mt-0.5">Kepala Bengkel / Lab: <strong>{{ j.kepalaLab }}</strong></p>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-[#F2F0EC] text-xs">
                                    <div class="p-2.5 rounded-xl bg-canvas text-center">
                                        <div class="font-black text-charcoal">{{ j.rombelCount }} Rombel</div>
                                        <div class="text-[10px] text-subtext font-bold">Rombongan Belajar</div>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-canvas text-center">
                                        <div class="font-black text-charcoal">{{ j.siswaCount }} Siswa</div>
                                        <div class="text-[10px] text-subtext font-bold">Peserta Didik</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SUB 2: ROMBEL KELAS -->
                        <div v-else-if="masterSubTab === 'rombel'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3.5">
                            <div
                                v-for="r in masterRombel"
                                :key="r.code"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between"
                            >
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-black px-2.5 py-0.5 rounded-lg bg-canvas text-charcoal border border-[#ECEAE4]">
                                            {{ r.name }}
                                        </span>
                                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-sage-light text-sage-dark">
                                            {{ r.jurusan }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-subtext mt-1">
                                        Wali Kelas: <strong class="text-charcoal">{{ r.wali }}</strong>
                                    </div>
                                    <div class="text-xs text-subtext mt-0.5">
                                        Homebase: {{ r.ruang }}
                                    </div>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-[#F2F0EC] flex items-center justify-between text-xs">
                                    <span class="text-subtext">Kapasitas:</span>
                                    <span class="font-bold text-charcoal">{{ r.terisi }} / {{ r.kapasitas }} Siswa</span>
                                </div>
                            </div>
                        </div>

                        <!-- SUB 3: MATA PELAJARAN -->
                        <div v-else-if="masterSubTab === 'mapel'" class="space-y-2.5">
                            <div
                                v-for="m in masterMapel"
                                :key="m.code"
                                class="bg-white rounded-[22px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                            >
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-black px-2 py-0.5 rounded-md bg-canvas text-charcoal border border-[#ECEAE4]">{{ m.code }}</span>
                                        <h4 class="text-sm font-extrabold text-charcoal">{{ m.name }}</h4>
                                    </div>
                                    <div class="text-xs text-subtext mt-1">
                                        Kategori: {{ m.kategori }} • Sasaran: {{ m.rombelTingkat }} • Beban: {{ m.jamPerMinggu }} Jam/Minggu
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-xs font-extrabold px-3 py-1 rounded-full bg-canvas border border-[#ECEAE4] text-charcoal">
                                        KKM: {{ m.kkm }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- SUB 4: TAHUN AJARAN -->
                        <div v-else class="space-y-3">
                            <div
                                v-for="ta in masterTahunAjaran"
                                :key="ta.id"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between"
                            >
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-extrabold text-charcoal">Tahun Ajaran {{ ta.tahun }} • Semester {{ ta.semester }}</h4>
                                        <span
                                            class="text-[10px] font-black px-2 py-0.5 rounded-full"
                                            :class="ta.status === 'Aktif' ? 'bg-[#E8F5E9] text-[#2E7D32]' : 'bg-muted text-subtext'"
                                        >
                                            {{ ta.status }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-subtext mt-1">
                                        Periode: {{ ta.mulai }} s/d {{ ta.selesai }} • {{ ta.totalHariEfektif }} Hari Efektif KBM
                                    </div>
                                </div>
                                <button
                                    v-if="ta.status !== 'Aktif'"
                                    type="button"
                                    @click="$emit('showToast', `Tahun ajaran ${ta.tahun} ${ta.semester} diaktifkan.`)"
                                    class="px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-white active:scale-95 transition-all cursor-pointer"
                                >
                                    Aktifkan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 4: PENUGASAN & JADWAL (PLOTTING GURU & JADWAL)       -->
                    <!-- ======================================================== -->
                    <div v-else-if="activeTab === 'penugasan'" class="space-y-4">
                        <!-- Mode Switcher -->
                        <div class="bg-white rounded-[26px] p-3 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-1.5 p-1 bg-muted/70 rounded-2xl w-full sm:w-auto flex-wrap">
                                <button
                                    type="button"
                                    @click="penugasanMode = 'wali'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="penugasanMode === 'wali' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Plotting Wali Kelas (42 Rombel)
                                </button>
                                <button
                                    type="button"
                                    @click="penugasanMode = 'guruMapel'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="penugasanMode === 'guruMapel' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Plotting Guru Mapel
                                </button>
                                <button
                                    type="button"
                                    @click="penugasanMode = 'jadwal'"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="penugasanMode === 'jadwal' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Jadwal Pelajaran Mingguan
                                </button>
                            </div>

                            <button
                                type="button"
                                @click="$emit('showToast', 'Jadwal pelajaran resmi berhasil diekspor format Excel (.xlsx).')"
                                class="px-4 py-2 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs flex items-center gap-1.5 self-start sm:self-auto"
                            >
                                <LmsIcon name="download" className="w-3.5 h-3.5" />
                                <span>Ekspor Jadwal</span>
                            </button>
                        </div>

                        <!-- MODE 1: PLOTTING WALI KELAS -->
                        <div v-if="penugasanMode === 'wali'" class="space-y-4">
                            <div class="hero-lavender rounded-[26px] p-4 soft-shadow flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-extrabold text-charcoal">Status Plotting Pembina Rombel</h3>
                                    <p class="text-xs text-charcoal/75 mt-0.5">Seluruh 42 rombel telah terpetakan ke wali kelas ber-SK resmi.</p>
                                </div>
                                <span class="px-3 py-1 rounded-full bg-white text-charcoal text-xs font-black shadow-2xs">
                                    100% Terplot
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                <div
                                    v-for="w in plottingWali"
                                    :key="w.id"
                                    class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between"
                                >
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-black px-2.5 py-0.5 rounded-lg bg-canvas text-charcoal border border-[#ECEAE4]">
                                                {{ w.rombel }}
                                            </span>
                                            <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-sage-light text-sage-dark">
                                                {{ w.status }}
                                            </span>
                                        </div>
                                        <h4 class="text-sm font-extrabold text-charcoal mt-1">{{ w.waliName }}</h4>
                                        <div class="text-[11px] text-subtext mt-0.5">NIP: {{ w.nip }} • {{ w.ruang }}</div>
                                    </div>
                                    <div class="mt-3 pt-2.5 border-t border-[#F2F0EC] flex items-center justify-between text-xs">
                                        <span class="text-subtext">{{ w.siswaCount }} Siswa Binaan</span>
                                        <button
                                            type="button"
                                            @click="$emit('showToast', `Dialog penugasan wali kelas untuk ${w.rombel} dibuka.`)"
                                            class="text-xs font-bold text-charcoal hover:underline cursor-pointer"
                                        >
                                            Ubah Wali →
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MODE 2: PLOTTING GURU MAPEL -->
                        <div v-else-if="penugasanMode === 'guruMapel'" class="space-y-3">
                            <div
                                v-for="g in plottingGuruMapel"
                                :key="g.id"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                            >
                                <div>
                                    <h4 class="text-sm font-extrabold text-charcoal">{{ g.guruName }}</h4>
                                    <div class="text-xs font-bold text-sky-dark mt-0.5">{{ g.mapel }}</div>
                                    <div class="text-[11px] text-subtext mt-1">
                                        Mengajar: <strong>{{ g.rombelList.join(', ') }}</strong> • Ruang: {{ g.ruang }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="px-3 py-1 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-extrabold text-charcoal">
                                        {{ g.totalJam }} Jam/Minggu
                                    </span>
                                    <button
                                        type="button"
                                        @click="$emit('showToast', `Edit plotting mapel untuk ${g.guruName} dibuka.`)"
                                        class="px-3 py-1 rounded-xl bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer"
                                    >
                                        Edit
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- MODE 3: JADWAL PELAJARAN MINGGUAN -->
                        <div v-else class="space-y-4">
                            <!-- Hari Tabs -->
                            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                                <button
                                    v-for="h in ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']"
                                    :key="h"
                                    type="button"
                                    @click="selectedHariJadwal = h"
                                    class="px-4 py-2 rounded-full text-xs font-bold transition-all cursor-pointer shrink-0"
                                    :class="selectedHariJadwal === h ? 'bg-charcoal text-white shadow-xs' : 'bg-white border border-[#ECEAE4] text-charcoal'"
                                >
                                    {{ h }}
                                </button>
                            </div>

                            <!-- List Jadwal Hari Ini -->
                            <div class="space-y-2.5">
                                <div
                                    v-for="(j, idx) in filteredJadwal"
                                    :key="idx"
                                    class="bg-white rounded-[22px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                                >
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-black px-2 py-0.5 rounded-md bg-canvas text-charcoal border border-[#ECEAE4]">{{ j.rombel }}</span>
                                            <span class="text-xs font-extrabold text-charcoal">{{ j.mapel }}</span>
                                        </div>
                                        <div class="text-xs text-subtext">
                                            Jam: <strong>{{ j.jamKe }}</strong> • Ruang: {{ j.ruang }} • Guru: {{ j.guru }}
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 rounded-full bg-[#E8F5E9] text-[#2E7D32] text-xs font-bold shrink-0 self-start sm:self-center">
                                        {{ j.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 5: PENGUMUMAN & BROADCAST SEKOLAH                    -->
                    <!-- ======================================================== -->
                    <div v-else-if="activeTab === 'pengumuman'" class="space-y-4">
                        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-extrabold text-charcoal">Papan Pengumuman &amp; Broadcast Sekolah</h3>
                                <p class="text-xs text-subtext mt-0.5">Publikasikan informasi resmi ke akun Siswa, Guru, atau Wali Kelas.</p>
                            </div>
                            <button
                                type="button"
                                @click="openNewPengumumanModal"
                                class="px-4 py-2 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                            >
                                + Buat Pengumuman
                            </button>
                        </div>

                        <div class="space-y-3">
                            <div
                                v-for="p in pengumumanList"
                                :key="p.id"
                                class="bg-white rounded-[26px] p-5 border border-[#ECEAE4] soft-shadow space-y-2.5"
                            >
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full" :class="p.urgensiBadge">
                                            {{ p.urgensi }}
                                        </span>
                                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full" :class="p.targetBadge">
                                            {{ p.target }}
                                        </span>
                                        <span class="text-[11px] text-subtext">• {{ p.date }} • Dibuat oleh: {{ p.author }}</span>
                                    </div>
                                    <span class="text-xs font-extrabold text-sage-dark bg-sage-light px-2.5 py-0.5 rounded-full shrink-0 self-start sm:self-auto">
                                        {{ p.views }} Dilihat
                                    </span>
                                </div>

                                <h4 class="text-base font-extrabold text-charcoal">{{ p.title }}</h4>
                                <p class="text-xs text-charcoal/80 leading-relaxed bg-canvas p-3 rounded-2xl border border-[#ECEAE4]">
                                    {{ p.content }}
                                </p>

                                <div class="flex items-center gap-2 pt-1 text-xs">
                                    <button
                                        type="button"
                                        @click="$emit('showToast', `Pengumuman '${p.title}' disiarkan ulang.`)"
                                        class="px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal font-bold hover:bg-white active:scale-95 transition-all cursor-pointer"
                                    >
                                        Broadcast Ulang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODAL: BUAT PENGUMUMAN BARU                                  -->
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
                v-if="isPengumumanModalOpen"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
                @click.self="isPengumumanModalOpen = false"
            >
                <div class="bg-white w-full max-w-lg rounded-t-[32px] sm:rounded-[32px] p-5 sm:p-6 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[#ECEAE4]">
                        <h3 class="text-base font-extrabold text-charcoal">Buat Pengumuman Sekolah</h3>
                        <button
                            type="button"
                            @click="isPengumumanModalOpen = false"
                            class="w-8 h-8 rounded-full bg-canvas text-charcoal flex items-center justify-center hover:bg-muted font-bold text-xs cursor-pointer"
                        >
                            ✕
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-extrabold text-charcoal mb-1">Judul Pengumuman</label>
                            <input
                                v-model="newPengumuman.title"
                                type="text"
                                placeholder="Tuliskan judul pengumuman resmi..."
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40"
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-extrabold text-charcoal mb-1">Target Sasaran</label>
                                <select
                                    v-model="newPengumuman.target"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-charcoal/40"
                                >
                                    <option value="Semua Pengguna">Semua Pengguna</option>
                                    <option value="Khusus Guru">Khusus Guru</option>
                                    <option value="Khusus Siswa">Khusus Siswa</option>
                                    <option value="Khusus Wali Kelas & Guru">Khusus Wali Kelas &amp; Guru</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-charcoal mb-1">Tingkat Urgensi</label>
                                <select
                                    v-model="newPengumuman.urgensi"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-charcoal/40"
                                >
                                    <option value="Normal">Normal</option>
                                    <option value="Penting">Penting</option>
                                    <option value="Khusus">Khusus</option>
                                    <option value="Info">Info</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-extrabold text-charcoal mb-1">Isi Pesan Pengumuman</label>
                            <textarea
                                v-model="newPengumuman.content"
                                rows="4"
                                placeholder="Tuliskan detail pengumuman yang akan disiarkan..."
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl p-3 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 resize-none"
                            ></textarea>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button
                            type="button"
                            @click="isPengumumanModalOpen = false"
                            class="flex-1 py-2.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-muted active:scale-95 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="savePengumuman"
                            class="flex-1 py-2.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                        >
                            Publikasikan
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
