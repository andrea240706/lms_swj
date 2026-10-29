<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import LmsSkeletonLoader from '@/Components/Lms/LmsSkeletonLoader.vue';
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';
import {
    initialAdminMenuConfigs,
    initialAdminUsers,
    initialSystemSettings,
} from './superAdminData';

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    activeTab: {
        type: String,
        default: 'admin_mgmt',
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

// Normalized Tab Key (maps 'home' or default to 'admin_mgmt')
const currentTab = computed(() => {
    if (props.activeTab === 'system_config' || props.activeTab === 'sistem') {
        return 'system_config';
    }
    return 'admin_mgmt';
});

// Dynamic Page Title
const pageTitle = computed(() => {
    if (currentTab.value === 'system_config') {
        return 'Pengaturan Sistem & Konfigurasi Menu Admin';
    }
    return 'Manajemen Admin & Operator Sekolah';
});

// ========================================================
// TAB 1: MANAJEMEN ADMIN (KELOLA AKUN OPERATOR)
// ========================================================
const adminUsers = ref(JSON.parse(JSON.stringify(initialAdminUsers)));
const userSearchQuery = ref('');
const userStatusFilter = ref('semua'); // 'semua' | 'Aktif' | 'Terkunci'

// Filtered Operator List
const filteredAdmins = computed(() => {
    const q = userSearchQuery.value.trim().toLowerCase();
    return adminUsers.value.filter((u) => {
        if (userStatusFilter.value !== 'semua' && u.status !== userStatusFilter.value) {
            return false;
        }
        if (q) {
            return (
                u.name.toLowerCase().includes(q) ||
                u.nip.toLowerCase().includes(q) ||
                u.email.toLowerCase().includes(q) ||
                u.role.toLowerCase().includes(q)
            );
        }
        return true;
    });
});

// Reset Password
const handleResetPassword = (user) => {
    const tempPassword = `SWJ-ADM-${Math.floor(1000 + Math.random() * 9000)}`;
    emit(
        'showToast',
        `Sandi baru dibuat untuk ${user.name}: [${tempPassword}]. Email instruksi telah dikirim.`
    );
};

// Toggle Lock Status
const handleToggleLock = (user) => {
    if (user.role.includes('Root')) {
        emit('showToast', 'Akun Super Admin (Root) tidak dapat dikunci untuk mencegah lockout sistem!');
        return;
    }
    user.status = user.status === 'Aktif' ? 'Terkunci' : 'Aktif';
    emit(
        'showToast',
        `Status akun ${user.name} diperbarui menjadi [${user.status}].`
    );
};

// Modal Tambah Operator
const isAddModalOpen = ref(false);
const newAdminForm = ref({
    name: '',
    nip: '',
    email: '',
    roleBadge: 'Operator Akademik',
    twoFactor: true,
    initialPassword: '',
    selectedPermissions: ['Operasional Akademik', 'Manajemen Pengguna'],
});

const openAddModal = () => {
    newAdminForm.value = {
        name: '',
        nip: `19970${Math.floor(1000 + Math.random() * 9000)} 202401 1 00${adminUsers.value.length + 1}`,
        email: '',
        roleBadge: 'Operator Akademik',
        twoFactor: true,
        initialPassword: `Adm@${Math.floor(1000 + Math.random() * 9000)}`,
        selectedPermissions: ['Operasional Akademik', 'Manajemen Pengguna'],
    };
    isAddModalOpen.value = true;
};

const handleSaveNewAdmin = () => {
    if (!newAdminForm.value.name.trim() || !newAdminForm.value.email.trim()) {
        emit('showToast', 'Nama lengkap dan email resmi wajib diisi.');
        return;
    }

    const created = {
        id: Date.now(),
        name: newAdminForm.value.name.trim(),
        nip: newAdminForm.value.nip.trim(),
        email: newAdminForm.value.email.trim(),
        role: `Admin ${newAdminForm.value.roleBadge}`,
        roleBadge: newAdminForm.value.roleBadge,
        status: 'Aktif',
        twoFactor: newAdminForm.value.twoFactor,
        permissions: [...newAdminForm.value.selectedPermissions],
        lastLogin: 'Belum pernah login',
        ipAddress: '192.168.1.150',
        avatarBg: 'bg-indigo-100 text-indigo-800',
    };

    adminUsers.value.push(created);
    isAddModalOpen.value = false;
    emit('showToast', `Akun operator ${created.name} berhasil ditambahkan!`);
};

// Modal Edit Hak Akses
const isEditPermissionModalOpen = ref(false);
const editingAdmin = ref(null);
const availableModules = [
    'Operasional Akademik',
    'Manajemen Pengguna',
    'Data Master',
    'Penugasan & Jadwal',
    'Pengumuman',
    'Ekspor Nilai & Leger',
];

const openEditPermissions = (user) => {
    editingAdmin.value = user;
    isEditPermissionModalOpen.value = true;
};

const togglePermission = (mod) => {
    if (!editingAdmin.value) return;
    const idx = editingAdmin.value.permissions.indexOf(mod);
    if (idx >= 0) {
        editingAdmin.value.permissions.splice(idx, 1);
    } else {
        editingAdmin.value.permissions.push(mod);
    }
};

const handleSavePermissions = () => {
    isEditPermissionModalOpen.value = false;
    emit('showToast', `Hak akses modul untuk ${editingAdmin.value.name} berhasil diperbarui.`);
};

// ========================================================
// TAB 2: PENGATURAN SISTEM (KONFIGURASI GLOBAL MENU ADMIN)
// ========================================================
const menuConfigs = ref(JSON.parse(JSON.stringify(initialAdminMenuConfigs)));
const systemSettings = ref(JSON.parse(JSON.stringify(initialSystemSettings)));

// Toggle Menu Admin Feature
const handleToggleMenu = (item) => {
    item.enabled = !item.enabled;
    const stateText = item.enabled ? 'diaktifkan' : 'dinonaktifkan';
    emit('showToast', `Menu [${item.label}] berhasil ${stateText} secara global.`);
};

// Handle Save All Settings
const isSaving = ref(false);
const handleSaveAllSettings = () => {
    isSaving.value = true;
    setTimeout(() => {
        isSaving.value = false;
        emit('showToast', 'Konfigurasi global sistem & menu admin berhasil disimpan dan diterapkan.');
    }, 450);
};

// Handle Trigger Backup
const handleRunBackup = () => {
    emit('showToast', 'Memulai pencadangan database PostgreSQL & asset storage...');
    setTimeout(() => {
        systemSettings.value.lastBackupTime = 'Baru saja';
        emit('showToast', 'Pencadangan database snapshot berhasil dibuat (Ukuran: 48.2 MB).');
    }, 800);
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
                <LmsSkeletonLoader v-if="isLoading" type="card-list" :rows="4" />

                <!-- MAIN CONTENT -->
                <template v-else>

                    <!-- ============================================ -->
                    <!-- TAB 1: MANAJEMEN ADMIN (KELOLA OPERATOR)      -->
                    <!-- ============================================ -->
                    <div v-if="currentTab === 'admin_mgmt'" class="space-y-6">

                        <!-- Hero Banner -->
                        <div class="hero-sage rounded-[28px] p-6 lg:p-7 soft-shadow relative overflow-hidden">
                            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="bg-white/95 backdrop-blur-sm px-3 py-1 rounded-full text-[11px] font-black text-charcoal shadow-2xs border border-[#ECEAE4]">
                                            Root Pusdatin IT
                                        </span>
                                        <span class="bg-emerald-600 text-white px-3 py-1 rounded-full text-[11px] font-bold shadow-2xs flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                            Otentikasi 2FA Aktif
                                        </span>
                                    </div>
                                    <h2 class="text-2xl sm:text-3xl font-black text-charcoal tracking-tight">
                                        Manajemen Akun Operator &amp; Admin TU
                                    </h2>
                                    <p class="text-xs sm:text-sm text-charcoal/80 mt-1 max-w-2xl leading-relaxed">
                                        Khusus wewenang super admin untuk mengatur kredensial operator, pembatasan hak akses modul, dan penguncian akun operator sekolah.
                                    </p>
                                </div>

                                <div class="flex items-center gap-2.5 shrink-0">
                                    <button
                                        type="button"
                                        @click="openAddModal"
                                        class="cursor-pointer px-4 py-2.5 rounded-full bg-charcoal text-white text-xs font-black hover:opacity-90 active:scale-95 transition-all shadow-md flex items-center gap-2"
                                    >
                                        <LmsIcon name="user" className="w-4 h-4" />
                                        + Tambah Operator
                                    </button>
                                </div>
                            </div>

                            <!-- Mini Summary Strip -->
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-5 border-t border-charcoal/15">
                                <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-3 border border-white/60">
                                    <div class="text-[10px] font-bold text-charcoal/60 uppercase tracking-wider">Total Operator</div>
                                    <div class="text-xl font-black text-charcoal mt-0.5">{{ adminUsers.length }} Akun</div>
                                </div>
                                <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-3 border border-white/60">
                                    <div class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider">Akun Aktif</div>
                                    <div class="text-xl font-black text-emerald-800 mt-0.5">
                                        {{ adminUsers.filter(u => u.status === 'Aktif').length }}
                                    </div>
                                </div>
                                <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-3 border border-white/60">
                                    <div class="text-[10px] font-bold text-rose-700 uppercase tracking-wider">Terkunci</div>
                                    <div class="text-xl font-black text-rose-800 mt-0.5">
                                        {{ adminUsers.filter(u => u.status === 'Terkunci').length }}
                                    </div>
                                </div>
                                <div class="bg-white/70 backdrop-blur-sm rounded-2xl p-3 border border-white/60">
                                    <div class="text-[10px] font-bold text-charcoal/60 uppercase tracking-wider">Perlindungan 2FA</div>
                                    <div class="text-xl font-black text-charcoal mt-0.5">100% Wajib</div>
                                </div>
                            </div>
                        </div>

                        <!-- Filter & Search Controls -->
                        <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div class="relative w-full sm:w-80">
                                <input
                                    v-model="userSearchQuery"
                                    type="text"
                                    placeholder="Cari nama, NIP, email operator..."
                                    class="w-full pl-9 pr-3 py-2 text-xs rounded-xl bg-canvas border border-[#E6E4DF] focus:outline-none focus:ring-2 focus:ring-charcoal/20"
                                />
                                <span class="absolute left-3 top-2.5 text-subtext text-xs">🔍</span>
                            </div>

                            <div class="flex items-center gap-1.5 self-start sm:self-auto overflow-x-auto w-full sm:w-auto">
                                <span class="text-xs font-bold text-subtext mr-1">Status:</span>
                                <button
                                    v-for="st in ['semua', 'Aktif', 'Terkunci']"
                                    :key="st"
                                    type="button"
                                    @click="userStatusFilter = st"
                                    class="cursor-pointer px-3 py-1 rounded-full text-xs font-bold transition-all capitalize"
                                    :class="userStatusFilter === st ? 'bg-charcoal text-white' : 'bg-canvas text-subtext hover:text-charcoal border border-[#ECEAE4]'"
                                >
                                    {{ st }}
                                </button>
                            </div>
                        </div>

                        <!-- Operator Account Cards Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                v-for="user in filteredAdmins"
                                :key="user.id"
                                class="bg-white rounded-[26px] p-5 border border-[#ECEAE4] soft-shadow flex flex-col justify-between transition-all hover:border-charcoal/30"
                            >
                                <div>
                                    <!-- Top Row: Avatar, Identity & Status Badge -->
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-12 h-12 rounded-2xl flex items-center justify-center font-black text-base shrink-0 shadow-2xs"
                                                :class="user.avatarBg"
                                            >
                                                {{ user.name.charAt(0) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <h3 class="text-sm sm:text-base font-black text-charcoal">{{ user.name }}</h3>
                                                    <span
                                                        class="text-[10px] font-extrabold px-2 py-0.5 rounded-full"
                                                        :class="user.role.includes('Root') ? 'bg-emerald-100 text-emerald-800' : 'bg-canvas border border-[#ECEAE4] text-charcoal'"
                                                    >
                                                        {{ user.roleBadge }}
                                                    </span>
                                                </div>
                                                <div class="text-[11px] text-subtext mt-0.5">
                                                    NIP: <span class="font-mono font-medium">{{ user.nip }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <span
                                            class="text-[10px] font-black px-2.5 py-1 rounded-full shrink-0 flex items-center gap-1.5"
                                            :class="user.status === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'"
                                        >
                                            <span class="w-1.5 h-1.5 rounded-full" :class="user.status === 'Aktif' ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                            {{ user.status }}
                                        </span>
                                    </div>

                                    <!-- Account Meta Details -->
                                    <div class="mt-4 pt-3 border-t border-[#F2F0EB] space-y-1.5 text-xs">
                                        <div class="flex items-center justify-between text-subtext">
                                            <span>Email Resmi:</span>
                                            <span class="font-medium text-charcoal">{{ user.email }}</span>
                                        </div>
                                        <div class="flex items-center justify-between text-subtext">
                                            <span>Terakhir Aktif:</span>
                                            <span class="text-charcoal">{{ user.lastLogin }} (IP: {{ user.ipAddress }})</span>
                                        </div>
                                        <div class="flex items-center justify-between text-subtext">
                                            <span>Autentikasi 2FA:</span>
                                            <span class="font-bold text-emerald-700 flex items-center gap-1">
                                                <LmsIcon name="check" className="w-3.5 h-3.5" /> Terverifikasi
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Module Permissions Chips -->
                                    <div class="mt-3.5">
                                        <div class="text-[10px] font-bold text-subtext uppercase tracking-wider mb-1.5">
                                            Wewenang Akses Modul:
                                        </div>
                                        <div class="flex flex-wrap gap-1.5">
                                            <span
                                                v-for="perm in user.permissions"
                                                :key="perm"
                                                class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-canvas border border-[#ECEAE4] text-charcoal"
                                            >
                                                {{ perm }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-5 pt-3 border-t border-[#F2F0EB] flex items-center justify-between gap-2">
                                    <button
                                        type="button"
                                        @click="handleResetPassword(user)"
                                        class="cursor-pointer px-3 py-1.5 rounded-xl bg-canvas hover:bg-[#EBE8E1] border border-[#ECEAE4] text-charcoal text-xs font-bold transition-all active:scale-95"
                                        title="Buat sandi sementara baru"
                                    >
                                        🔑 Reset Sandi
                                    </button>

                                    <div class="flex items-center gap-1.5">
                                        <button
                                            type="button"
                                            @click="openEditPermissions(user)"
                                            class="cursor-pointer px-3 py-1.5 rounded-xl bg-canvas hover:bg-[#EBE8E1] border border-[#ECEAE4] text-charcoal text-xs font-bold transition-all active:scale-95"
                                        >
                                            Wewenang
                                        </button>
                                        <button
                                            v-if="!user.role.includes('Root')"
                                            type="button"
                                            @click="handleToggleLock(user)"
                                            class="cursor-pointer px-3 py-1.5 rounded-xl text-xs font-bold transition-all active:scale-95"
                                            :class="user.status === 'Aktif' ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200'"
                                        >
                                            {{ user.status === 'Aktif' ? 'Kunci Akun' : 'Aktifkan' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ============================================ -->
                    <!-- TAB 2: PENGATURAN SISTEM (GLOBAL MENU ADMIN)  -->
                    <!-- ============================================ -->
                    <div v-else-if="currentTab === 'system_config'" class="space-y-6">

                        <!-- Hero Banner -->
                        <div class="hero-sky rounded-[28px] p-6 lg:p-7 soft-shadow relative overflow-hidden">
                            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="bg-white/95 backdrop-blur-sm px-3 py-1 rounded-full text-[11px] font-black text-charcoal shadow-2xs border border-[#ECEAE4]">
                                            Konfigurasi Global LMS
                                        </span>
                                        <span class="bg-charcoal text-white px-3 py-1 rounded-full text-[11px] font-bold shadow-2xs">
                                            TA {{ systemSettings.tahunAjaranAktif }}
                                        </span>
                                    </div>
                                    <h2 class="text-2xl sm:text-3xl font-black text-charcoal tracking-tight">
                                        Pengaturan Sistem &amp; Kontrol Menu Admin
                                    </h2>
                                    <p class="text-xs sm:text-sm text-charcoal/80 mt-1 max-w-2xl leading-relaxed">
                                        Pusat kendali saklar menu operator TU, pembatasan upload materi pembelajaran, masa kadaluarsa sesi, serta jadwal backup basis data.
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button
                                        type="button"
                                        @click="handleSaveAllSettings"
                                        class="cursor-pointer px-5 py-2.5 rounded-full bg-charcoal text-white text-xs font-black hover:opacity-90 active:scale-95 transition-all shadow-md flex items-center gap-2"
                                    >
                                        <LmsIcon v-if="!isSaving" name="check" className="w-4 h-4" />
                                        <span v-if="isSaving">Menyimpan...</span>
                                        <span v-else>Simpan Konfigurasi</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION A: GLOBAL MENU ADMIN TOGGLE CONTROLS -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-black text-charcoal">
                                        Kontrol Visibilitas &amp; Izin Menu Admin TU
                                    </h3>
                                    <p class="text-xs text-subtext mt-0.5">
                                        Saklar aktif/nonaktif fitur yang muncul pada akun seluruh staf Administrasi Akademik.
                                    </p>
                                </div>
                                <span class="text-xs font-bold px-3 py-1 rounded-full bg-canvas border border-[#ECEAE4] text-charcoal">
                                    {{ menuConfigs.filter(m => m.enabled).length }} / {{ menuConfigs.length }} Menu Aktif
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                <div
                                    v-for="item in menuConfigs"
                                    :key="item.id"
                                    class="bg-white rounded-[24px] p-4 sm:p-5 border border-[#ECEAE4] soft-shadow flex items-start justify-between gap-4 transition-all"
                                    :class="item.enabled ? 'border-charcoal/20' : 'opacity-70 bg-canvas/40'"
                                >
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-subtext bg-canvas px-2 py-0.5 rounded">
                                                {{ item.category }}
                                            </span>
                                            <span
                                                class="text-[10px] font-extrabold px-2 py-0.5 rounded-full"
                                                :class="item.enabled ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600'"
                                            >
                                                {{ item.enabled ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </div>
                                        <div class="text-sm font-black text-charcoal leading-snug">
                                            {{ item.label }}
                                        </div>
                                        <p class="text-xs text-subtext leading-relaxed">
                                            {{ item.desc }}
                                        </p>
                                        <div class="text-[11px] font-medium text-charcoal/70 pt-1">
                                            Akses: <span class="font-bold text-charcoal">{{ item.accessLevel }}</span>
                                        </div>
                                    </div>

                                    <!-- Interactive iOS-style Toggle Switch -->
                                    <button
                                        type="button"
                                        @click="handleToggleMenu(item)"
                                        class="cursor-pointer shrink-0 mt-1 w-12 h-6 rounded-full p-0.5 transition-colors duration-200 ease-in-out focus:outline-none"
                                        :class="item.enabled ? 'bg-emerald-600' : 'bg-gray-300'"
                                        :title="item.enabled ? 'Nonaktifkan menu ini' : 'Aktifkan menu ini'"
                                    >
                                        <div
                                            class="w-5 h-5 rounded-full bg-white shadow-md transform transition-transform duration-200 ease-in-out"
                                            :class="item.enabled ? 'translate-x-6' : 'translate-x-0'"
                                        ></div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION B: GLOBAL LMS SYSTEM PARAMETERS -->
                        <div class="bg-white rounded-[28px] p-6 border border-[#ECEAE4] soft-shadow space-y-6">
                            <div>
                                <h3 class="text-base font-black text-charcoal">
                                    Parameter Operasional &amp; Batas Sistem
                                </h3>
                                <p class="text-xs text-subtext mt-0.5">
                                    Pengaturan teknis kapasitas penyimpanan, sesi keamanan, dan tahun ajaran resmi.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-2">

                                <!-- Parameter 1: Tahun Ajaran Aktif -->
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-charcoal">Tahun Ajaran &amp; Semester Aktif:</label>
                                    <select
                                        v-model="systemSettings.tahunAjaranAktif"
                                        class="w-full text-xs font-bold px-3 py-2.5 rounded-xl bg-canvas border border-[#ECEAE4] focus:outline-none focus:ring-2 focus:ring-charcoal/20"
                                    >
                                        <option value="2024/2025 Genap">2024/2025 Genap (Aktif Sekarang)</option>
                                        <option value="2024/2025 Ganjil">2024/2025 Ganjil (Arsip KBM)</option>
                                        <option value="2025/2026 Ganjil">2025/2026 Ganjil (Persiapan KBM)</option>
                                    </select>
                                    <p class="text-[11px] text-subtext">Perubahan TA mengubah periode KBM guru dan rekap presensi secara menyeluruh.</p>
                                </div>

                                <!-- Parameter 2: Batas Upload Materi & Tugas -->
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-charcoal">Batas Maksimal Upload Berkas (Per File):</label>
                                    <div class="grid grid-cols-4 gap-2">
                                        <button
                                            v-for="size in [10, 15, 25, 50]"
                                            :key="size"
                                            type="button"
                                            @click="systemSettings.maxUploadMb = size"
                                            class="cursor-pointer py-2 text-xs font-bold rounded-xl border text-center transition-all"
                                            :class="systemSettings.maxUploadMb === size ? 'bg-charcoal text-white border-charcoal shadow-xs' : 'bg-canvas text-subtext border-[#ECEAE4] hover:text-charcoal'"
                                        >
                                            {{ size }} MB
                                        </button>
                                    </div>
                                    <p class="text-[11px] text-subtext">Berlaku untuk modul guru, tugas siswa, dan lampiran pengumuman.</p>
                                </div>

                                <!-- Parameter 3: Session Timeout -->
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-charcoal">Batas Waktu Sesi Tidak Aktif (Session Timeout):</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        <button
                                            v-for="min in [30, 60, 120]"
                                            :key="min"
                                            type="button"
                                            @click="systemSettings.sessionTimeoutMinutes = min"
                                            class="cursor-pointer py-2 text-xs font-bold rounded-xl border text-center transition-all"
                                            :class="systemSettings.sessionTimeoutMinutes === min ? 'bg-charcoal text-white border-charcoal shadow-xs' : 'bg-canvas text-subtext border-[#ECEAE4] hover:text-charcoal'"
                                        >
                                            {{ min }} Menit
                                        </button>
                                    </div>
                                    <p class="text-[11px] text-subtext">Pengguna akan logout otomatis bila tidak ada aktivitas dalam durasi ini.</p>
                                </div>

                                <!-- Parameter 4: Mode Pemeliharaan (Maintenance Mode) -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label class="text-xs font-bold text-charcoal">Mode Pemeliharaan (Maintenance Mode):</label>
                                        <button
                                            type="button"
                                            @click="systemSettings.maintenanceMode = !systemSettings.maintenanceMode"
                                            class="cursor-pointer shrink-0 w-12 h-6 rounded-full p-0.5 transition-colors duration-200"
                                            :class="systemSettings.maintenanceMode ? 'bg-rose-600' : 'bg-gray-300'"
                                        >
                                            <div
                                                class="w-5 h-5 rounded-full bg-white shadow-md transform transition-transform duration-200"
                                                :class="systemSettings.maintenanceMode ? 'translate-x-6' : 'translate-x-0'"
                                            ></div>
                                        </button>
                                    </div>
                                    <div
                                        class="p-2.5 rounded-xl text-xs font-medium border"
                                        :class="systemSettings.maintenanceMode ? 'bg-rose-50 text-rose-800 border-rose-200' : 'bg-canvas text-subtext border-[#ECEAE4]'"
                                    >
                                        {{ systemSettings.maintenanceMode ? '⚠️ PERINGATAN: Sistem dikunci, hanya Super Admin yang dapat mengakses aplikasi.' : 'LMS beroperasi normal untuk seluruh siswa, guru, wali, dan staf operator.' }}
                                    </div>
                                </div>

                            </div>

                            <!-- Format Berkas Diizinkan -->
                            <div class="pt-4 border-t border-[#F2F0EB]">
                                <div class="text-xs font-bold text-charcoal mb-2">Format Ekstensi Berkas Pembelajaran Resmi:</div>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="ext in systemSettings.allowedExtensions"
                                        :key="ext"
                                        class="text-xs font-mono font-bold px-3 py-1 rounded-lg bg-canvas border border-[#ECEAE4] text-charcoal"
                                    >
                                        .{{ ext.toLowerCase() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Cadangan Basis Data (Snapshot) -->
                            <div class="pt-4 border-t border-[#F2F0EB] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="text-xs font-bold text-charcoal">Pencadangan Basis Data Otomatis:</div>
                                    <div class="text-[11px] text-subtext">
                                        {{ systemSettings.backupSchedule }} • Terakhir berhasil: <span class="font-bold text-charcoal">{{ systemSettings.lastBackupTime }}</span>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    @click="handleRunBackup"
                                    class="cursor-pointer px-4 py-2 rounded-xl bg-canvas hover:bg-[#EBE8E1] border border-[#ECEAE4] text-xs font-bold text-charcoal transition-all active:scale-95"
                                >
                                    💾 Cadangkan Sekarang
                                </button>
                            </div>

                        </div>

                    </div>

                </template>

            </div>
        </div>

        <!-- ============================================ -->
        <!-- MODAL: TAMBAH AKUN OPERATOR BARU             -->
        <!-- ============================================ -->
        <div
            v-if="isAddModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-charcoal/40 backdrop-blur-xs animate-in fade-in duration-200"
        >
            <div class="bg-white rounded-[28px] max-w-lg w-full p-6 soft-shadow border border-[#ECEAE4] space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#ECEAE4]">
                    <div>
                        <h3 class="text-base font-black text-charcoal">Tambah Akun Operator Baru</h3>
                        <p class="text-xs text-subtext">Berikan akses wewenang admin untuk staf tata usaha.</p>
                    </div>
                    <button
                        type="button"
                        @click="isAddModalOpen = false"
                        class="cursor-pointer w-8 h-8 rounded-full bg-canvas text-subtext hover:text-charcoal flex items-center justify-center font-bold"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="font-bold text-charcoal block mb-1">Nama Lengkap &amp; Gelar:</label>
                        <input
                            v-model="newAdminForm.name"
                            type="text"
                            placeholder="Contoh: Hendra Saputra, S.Kom"
                            class="w-full px-3 py-2 rounded-xl bg-canvas border border-[#ECEAE4] focus:outline-none focus:ring-2 focus:ring-charcoal/20"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-bold text-charcoal block mb-1">NIP / ID Pegawai:</label>
                            <input
                                v-model="newAdminForm.nip"
                                type="text"
                                class="w-full px-3 py-2 rounded-xl bg-canvas border border-[#ECEAE4] font-mono text-xs"
                            />
                        </div>
                        <div>
                            <label class="font-bold text-charcoal block mb-1">Peran Operator:</label>
                            <select
                                v-model="newAdminForm.roleBadge"
                                class="w-full px-3 py-2 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold"
                            >
                                <option value="Operator Utama">Operator Utama</option>
                                <option value="Operator Akademik">Operator Akademik</option>
                                <option value="Asisten Operator">Asisten Operator</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-charcoal block mb-1">Email Resmi Sekolah:</label>
                        <input
                            v-model="newAdminForm.email"
                            type="email"
                            placeholder="nama.operator@smkswj.sch.id"
                            class="w-full px-3 py-2 rounded-xl bg-canvas border border-[#ECEAE4] focus:outline-none focus:ring-2 focus:ring-charcoal/20"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-charcoal block mb-1">Sandi Sementara Awal:</label>
                        <input
                            v-model="newAdminForm.initialPassword"
                            type="text"
                            class="w-full px-3 py-2 rounded-xl bg-canvas border border-[#ECEAE4] font-mono text-xs font-bold text-charcoal"
                        />
                    </div>

                    <div>
                        <label class="font-bold text-charcoal block mb-1.5">Wewenang Modul yang Diberikan:</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label
                                v-for="mod in availableModules"
                                :key="mod"
                                class="flex items-center gap-2 p-2 rounded-xl bg-canvas border border-[#ECEAE4] cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    :value="mod"
                                    v-model="newAdminForm.selectedPermissions"
                                    class="rounded accent-charcoal"
                                />
                                <span class="text-[11px] font-bold text-charcoal">{{ mod }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-[#ECEAE4] flex items-center justify-end gap-2">
                    <button
                        type="button"
                        @click="isAddModalOpen = false"
                        class="cursor-pointer px-4 py-2 rounded-xl bg-canvas text-subtext hover:text-charcoal font-bold text-xs"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="handleSaveNewAdmin"
                        class="cursor-pointer px-5 py-2 rounded-xl bg-charcoal text-white font-black text-xs hover:opacity-90 active:scale-95 shadow-xs"
                    >
                        Simpan Operator
                    </button>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- MODAL: EDIT WEWENANG HAK AKSES OPERATOR      -->
        <!-- ============================================ -->
        <div
            v-if="isEditPermissionModalOpen && editingAdmin"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-charcoal/40 backdrop-blur-xs animate-in fade-in duration-200"
        >
            <div class="bg-white rounded-[28px] max-w-md w-full p-6 soft-shadow border border-[#ECEAE4] space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-[#ECEAE4]">
                    <div>
                        <h3 class="text-base font-black text-charcoal">Edit Wewenang Modul</h3>
                        <p class="text-xs text-subtext">{{ editingAdmin.name }} ({{ editingAdmin.roleBadge }})</p>
                    </div>
                    <button
                        type="button"
                        @click="isEditPermissionModalOpen = false"
                        class="cursor-pointer w-8 h-8 rounded-full bg-canvas text-subtext hover:text-charcoal flex items-center justify-center font-bold"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-2">
                    <div
                        v-for="mod in availableModules"
                        :key="mod"
                        @click="togglePermission(mod)"
                        class="cursor-pointer p-3 rounded-2xl border flex items-center justify-between transition-all"
                        :class="editingAdmin.permissions.includes(mod) ? 'bg-emerald-50/70 border-emerald-300' : 'bg-canvas border-[#ECEAE4] opacity-75'"
                    >
                        <span class="text-xs font-bold text-charcoal">{{ mod }}</span>
                        <span
                            class="w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold"
                            :class="editingAdmin.permissions.includes(mod) ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-transparent'"
                        >
                            ✓
                        </span>
                    </div>
                </div>

                <div class="pt-3 border-t border-[#ECEAE4] flex items-center justify-end gap-2">
                    <button
                        type="button"
                        @click="isEditPermissionModalOpen = false"
                        class="cursor-pointer px-4 py-2 rounded-xl bg-canvas text-subtext hover:text-charcoal font-bold text-xs"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="handleSavePermissions"
                        class="cursor-pointer px-5 py-2 rounded-xl bg-charcoal text-white font-black text-xs hover:opacity-90 active:scale-95 shadow-xs"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>
