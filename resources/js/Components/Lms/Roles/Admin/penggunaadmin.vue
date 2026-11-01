<script setup>
import { computed, ref } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import { initialUsersList, masterRombel } from './adminData';

const emit = defineEmits(['showToast']);

const userList = ref(JSON.parse(JSON.stringify(initialUsersList)));
const masterRombelList = ref(JSON.parse(JSON.stringify(masterRombel)));
const userRoleFilter = ref('semua'); // 'semua' | 'siswa' | 'guru' | 'wali' | 'kepsek'
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

// User CRUD Modal State
const isUserModalOpen = ref(false);
const isEditingUser = ref(false);
const userForm = ref({
    id: null,
    role: 'siswa',
    name: '',
    identifier: '',
    rombel: '',
    email: '',
    status: 'Aktif',
    sandiStatus: 'Default',
    lastLogin: 'Baru dibuat',
});

const openAddUserModal = (defaultRole = 'siswa') => {
    isEditingUser.value = false;
    userForm.value = {
        id: null,
        role: defaultRole,
        name: '',
        identifier: '',
        rombel:
            defaultRole === 'siswa'
                ? masterRombelList.value[0]?.name || 'X TBSM A'
                : defaultRole === 'wali'
                ? 'Wali Kelas ' + (masterRombelList.value[0]?.name || 'X TBSM A')
                : defaultRole === 'kepsek'
                ? 'Kepala Sekolah SMK SWJ'
                : 'Guru Pengampu',
        email: '',
        status: 'Aktif',
        sandiStatus: 'Sandi Default',
        lastLogin: 'Belum pernah login',
    };
    isUserModalOpen.value = true;
};

const openEditUserModal = (user) => {
    isEditingUser.value = true;
    userForm.value = JSON.parse(JSON.stringify(user));
    isUserModalOpen.value = true;
};

const saveUser = () => {
    if (!userForm.value.name.trim() || !userForm.value.email.trim() || !userForm.value.identifier.trim()) {
        emit('showToast', 'Lengkapi Nama, Email, dan NISN/NIP pengguna.');
        return;
    }
    if (isEditingUser.value) {
        const idx = userList.value.findIndex((u) => u.id === userForm.value.id);
        if (idx !== -1) {
            userList.value[idx] = { ...userForm.value };
            emit('showToast', `Data akun [${userForm.value.name}] berhasil diperbarui.`);
        }
    } else {
        const newUser = {
            ...userForm.value,
            id: Date.now(),
        };
        userList.value.unshift(newUser);
        emit('showToast', `Akun baru [${newUser.name}] berhasil didaftarkan dengan sandi inisial default.`);
    }
    isUserModalOpen.value = false;
};

const deleteUser = (userId) => {
    const target = userList.value.find((u) => u.id === userId);
    if (!target) return;
    userList.value = userList.value.filter((u) => u.id !== userId);
    emit('showToast', `Akun [${target.name}] berhasil dihapus dari sistem.`);
};

const handleToggleLockUser = (user) => {
    user.status = user.status === 'Aktif' ? 'Nonaktif' : 'Aktif';
    emit('showToast', `Status akun ${user.name} diperbarui menjadi ${user.status}.`);
};
</script>

<template>
    <div class="space-y-4">
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
                <button
                    type="button"
                    @click="userRoleFilter = 'kepsek'"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                    :class="userRoleFilter === 'kepsek' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                >
                    Kepsek ({{ userList.filter(u => u.role === 'kepsek').length }})
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
                    @click="openAddUserModal(userRoleFilter === 'semua' ? 'siswa' : userRoleFilter)"
                    class="px-3.5 py-2 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs shrink-0 flex items-center gap-1.5"
                >
                    <span>+ Pengguna</span>
                </button>
            </div>
        </div>

        <!-- User List Cards -->
        <div class="space-y-2.5">
            <div
                v-for="(u, index) in filteredUsers"
                :key="u.id"
                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-charcoal/30 transition-all"
            >
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-6 text-xs font-black text-subtext/60 shrink-0 text-center select-none">
                        {{ index + 1 }}
                    </span>
                    <div
                        class="w-11 h-11 rounded-2xl flex items-center justify-center font-black text-sm shrink-0"
                        :class="u.role === 'siswa' ? 'bg-sky-light text-sky-dark' : u.role === 'guru' ? 'bg-sage-light text-sage-dark' : u.role === 'kepsek' ? 'bg-amberpastel-light text-amberpastel-dark' : 'bg-lavender-light text-lavender-dark'"
                    >
                        {{ u.name.charAt(0) }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="text-sm font-extrabold text-charcoal truncate">{{ u.name }}</h4>
                            <span
                                class="text-[9px] font-black px-2 py-0.5 rounded-full uppercase"
                                :class="u.role === 'siswa' ? 'bg-sky-light text-sky-dark' : u.role === 'guru' ? 'bg-sage-light text-sage-dark' : u.role === 'kepsek' ? 'bg-amberpastel-light text-amberpastel-dark' : 'bg-lavender-light text-lavender-dark'"
                            >
                                {{ u.role === 'wali' ? 'Wali Kelas' : u.role === 'kepsek' ? 'Kepala Sekolah' : u.role }}
                            </span>
                        </div>
                        <div class="text-[11px] text-subtext truncate mt-0.5">
                            {{ u.role === 'siswa' ? 'NISN: ' : 'NIP: ' }}{{ u.identifier }} • {{ u.rombel }} • {{ u.email }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto flex-wrap">
                    <span
                        class="text-[10px] font-bold px-2.5 py-1 rounded-full"
                        :class="u.status === 'Aktif' ? 'bg-[#E8F5E9] text-[#2E7D32]' : 'bg-muted text-subtext'"
                    >
                        {{ u.status }}
                    </span>
                    <button
                        type="button"
                        @click="openEditUserModal(u)"
                        class="px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal text-xs font-bold hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                        title="Edit Akun Pengguna"
                    >
                        Edit
                    </button>
                    <button
                        type="button"
                        @click="handleToggleLockUser(u)"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                        :class="u.status === 'Nonaktif' || u.status === 'Terkunci' ? 'bg-charcoal text-white hover:bg-black' : 'bg-canvas border border-[#ECEAE4] text-subtext hover:text-charcoal hover:bg-white'"
                        :title="u.status === 'Nonaktif' || u.status === 'Terkunci' ? 'Aktifkan Akun Pengguna' : 'Nonaktifkan Akun Pengguna'"
                    >
                        {{ u.status === 'Nonaktif' || u.status === 'Terkunci' ? 'Aktifkan' : 'Nonaktifkan' }}
                    </button>
                    <button
                        type="button"
                        @click="deleteUser(u.id)"
                        class="px-2.5 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-[#D32F2F] hover:bg-blossom-light text-xs font-bold active:scale-95 transition-all cursor-pointer shadow-2xs"
                        title="Hapus Akun Pengguna"
                    >
                        Hapus
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: TAMBAH / EDIT DATA PENGGUNA (CRUD AKUN) -->
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isUserModalOpen"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
                @click.self="isUserModalOpen = false"
            >
                <div class="bg-white w-full max-w-lg rounded-t-[32px] sm:rounded-[32px] p-5 sm:p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto no-scrollbar">
                    <div class="flex items-center justify-between pb-3 border-b border-[#ECEAE4]">
                        <div>
                            <h3 class="text-base font-extrabold text-charcoal">
                                {{ isEditingUser ? 'Edit Akun Pengguna' : 'Tambah Pengguna Baru' }}
                            </h3>
                            <p class="text-xs text-subtext mt-0.5">Kelola data kredensial dan hak akses akun civitas sekolah.</p>
                        </div>
                        <button
                            type="button"
                            @click="isUserModalOpen = false"
                            class="w-8 h-8 rounded-full bg-canvas text-charcoal flex items-center justify-center hover:bg-muted font-bold cursor-pointer"
                        >
                            <LmsIcon name="x" className="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <div class="space-y-3 text-xs">
                        <!-- Privacy Guarantee Banner -->
                        <div class="p-3.5 rounded-2xl bg-sky-light border border-[#BAE6FD] text-sky-dark space-y-1">
                            <div class="flex items-center gap-2 font-bold text-xs">
                                <LmsIcon name="lock" className="w-4 h-4 text-sky-dark" />
                                <span>Privasi &amp; Keamanan Kata Sandi</span>
                            </div>
                            <p class="text-[11px] leading-relaxed text-sky-dark/90">
                                Admin tidak memiliki akses untuk membuat, mengatur, atau melihat kata sandi pengguna. Akun baru diaktifkan dengan kredensial inisial sekolah (berdasarkan NISN/NIP), dan pengguna wajib memperbarui kata sandi secara mandiri saat login.
                            </p>
                        </div>

                        <!-- Peran / Role -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Peran / Role</label>
                                <select
                                    v-model="userForm.role"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                >
                                    <option value="siswa">Siswa (Peserta Didik)</option>
                                    <option value="guru">Guru (Pengampu Mapel)</option>
                                    <option value="wali">Guru Wali Kelas</option>
                                    <option value="kepsek">Kepala Sekolah</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Status Akun</label>
                                <select
                                    v-model="userForm.status"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                >
                                    <option value="Aktif">Aktif</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Nama Lengkap &amp; Gelar</label>
                            <input
                                v-model="userForm.name"
                                type="text"
                                placeholder="Contoh: Muhammad Farhan / Siti Rahma, M.Pd"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            />
                        </div>

                        <!-- Nomor Induk & Email -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">
                                    {{ userForm.role === 'siswa' ? 'NISN (10 Digit)' : 'NIP / NUPTK' }}
                                </label>
                                <input
                                    v-model="userForm.identifier"
                                    type="text"
                                    :placeholder="userForm.role === 'siswa' ? '0078129384' : '19850314 201101 1 004'"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>

                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Email Resmi / Sekolah</label>
                                <input
                                    v-model="userForm.email"
                                    type="email"
                                    placeholder="nama@smkswj.sch.id"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                        </div>

                        <!-- Rombel / Penugasan -->
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">
                                {{ userForm.role === 'siswa' ? 'Rombel Kelas' : userForm.role === 'wali' ? 'Wali Rombel' : 'Penugasan / Jabatan' }}
                            </label>
                            <input
                                v-model="userForm.rombel"
                                type="text"
                                :placeholder="userForm.role === 'siswa' ? 'X TBSM A' : userForm.role === 'wali' ? 'Wali Kelas X TBSM A' : userForm.role === 'kepsek' ? 'Kepala Sekolah SMK SWJ' : 'Guru Produktif TBSM'"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            />
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button
                            type="button"
                            @click="isUserModalOpen = false"
                            class="flex-1 py-2.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-muted active:scale-95 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="saveUser"
                            class="flex-1 py-2.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                        >
                            {{ isEditingUser ? 'Simpan Perubahan' : 'Daftarkan Akun' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
