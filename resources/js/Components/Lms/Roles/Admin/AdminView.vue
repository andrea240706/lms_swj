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
    masterRuanganList,
    masterTahunAjaran,
    slotJamPelajaran,
    initialSystemSettings,
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
// Sub-tabs state
const masterSubTab = ref('jurusan'); // 'ta' | 'jurusan' | 'rombel' | 'mapel'
const penugasanMode = ref('jadwal'); // 'wali' | 'guruMapel' | 'jadwal'

const setMasterSubTab = (tab) => {
    masterSubTab.value = tab;
    emit('selectTab', `master_${tab}`);
};

const setPenugasanMode = (mode) => {
    penugasanMode.value = mode;
    const tabMap = {
        jadwal: 'penugasan_jadwal',
        guruMapel: 'penugasan_guru',
        wali: 'penugasan_wali',
    };
    emit('selectTab', tabMap[mode] || 'penugasan_jadwal');
};

watch(
    () => props.activeTab,
    (newTab) => {
        triggerLoading(350);
        if (newTab === 'master_ta') masterSubTab.value = 'ta';
        else if (newTab === 'master_jurusan') masterSubTab.value = 'jurusan';
        else if (newTab === 'master_rombel') masterSubTab.value = 'rombel';
        else if (newTab === 'master_mapel') masterSubTab.value = 'mapel';
        else if (newTab === 'penugasan_jadwal') penugasanMode.value = 'jadwal';
        else if (newTab === 'penugasan_guru') penugasanMode.value = 'guruMapel';
        else if (newTab === 'penugasan_wali') penugasanMode.value = 'wali';
    },
    { immediate: true }
);

// Dynamic Page Titles
const pageTitle = computed(() => {
    const map = {
        home: 'Operasional Akademik Sekolah',
        operasional: 'Operasional Akademik Sekolah',
        pengguna: 'Manajemen Pengguna (Siswa, Guru, Wali, Kepsek)',
        master: 'Data Master (TA, Jurusan, Kelas, Mapel)',
        master_ta: 'Data Master • Tahun Ajaran',
        master_jurusan: 'Data Master • Kompetensi Keahlian (Jurusan)',
        master_rombel: 'Data Master • Rombongan Belajar (Kelas)',
        master_mapel: 'Data Master • Mata Pelajaran Kurikulum',
        penugasan: 'Penugasan & Jadwal (Plotting Guru & Jadwal)',
        penugasan_jadwal: 'Penugasan • Jadwal Pelajaran Mingguan',
        penugasan_guru: 'Penugasan • Plotting Guru Pengampu Mapel',
        penugasan_wali: 'Penugasan • Penetapan Wali Kelas',
        pengumuman: 'Pengumuman & Broadcast Sekolah',
        sistem: 'Pengaturan Sistem & Pemeliharaan LMS',
    };
    return map[props.activeTab] || 'Operasional Akademik Sekolah';
});

// ========================================================
// LIVE KPI DASHBOARD STATS
// ========================================================
const masterJurusanList = ref(JSON.parse(JSON.stringify(masterJurusan)));
const masterRombelList = ref(JSON.parse(JSON.stringify(masterRombel)));
const masterMapelList = ref(JSON.parse(JSON.stringify(masterMapel)));
const masterTahunAjaranList = ref(JSON.parse(JSON.stringify(masterTahunAjaran)));

// ========================================================
// TAB 2: MANAJEMEN PENGGUNA STATE & METHODS (CRUD)
// ========================================================
const userList = ref(JSON.parse(JSON.stringify(initialUsersList)));
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

// ========================================================
// TAB 3: DATA MASTER STATE & METHODS (CRUD)
// ========================================================

const isMasterModalOpen = ref(false);
const isEditingMaster = ref(false);
const masterTargetType = ref('jurusan'); // 'jurusan' | 'rombel' | 'mapel' | 'ta'

const masterItemForm = ref({
    // Jurusan
    code: '',
    name: '',
    tingkat: '3 Tingkat (X, XI, XII)',
    kepalaLab: '',
    rombelCount: 0,
    siswaCount: 0,
    // Rombel
    jurusan: 'TBSM',
    ruang: 'Bengkel TBSM 1',
    kapasitas: 36,
    terisi: 0,
    wali: '',
    // Mapel
    kategori: 'Kejuruan Produktif',
    rombelTingkat: 'Kelas X',
    jamPerMinggu: 4,
    kkm: 75,
    // Tahun Ajaran
    id: null,
    tahun: '2027/2028',
    semester: 'Ganjil',
    status: 'Arsip',
    mulai: '12 Juli 2027',
    selesai: '18 Des 2027',
    totalHariEfektif: 108,
});

const openAddMasterModal = (type) => {
    masterTargetType.value = type;
    isEditingMaster.value = false;
    masterItemForm.value = {
        code: '',
        name: '',
        tingkat: '3 Tingkat (X, XI, XII)',
        kepalaLab: allGuruList.value[0]?.name || '',
        rombelCount: 0,
        siswaCount: 0,
        jurusan: masterJurusanList.value[0]?.code || 'TBSM',
        ruang: masterRuanganList[0] || 'Bengkel TBSM 1',
        kapasitas: 36,
        terisi: 0,
        wali: allGuruList.value[0]?.name || '',
        kategori: 'Kejuruan Produktif',
        rombelTingkat: 'Kelas X',
        jamPerMinggu: 4,
        kkm: 75,
        id: null,
        tahun: '2027/2028',
        semester: 'Ganjil',
        status: 'Arsip',
        mulai: '12 Juli 2027',
        selesai: '18 Des 2027',
        totalHariEfektif: 108,
    };
    isMasterModalOpen.value = true;
};

const openEditMasterModal = (type, item) => {
    masterTargetType.value = type;
    isEditingMaster.value = true;
    masterItemForm.value = JSON.parse(JSON.stringify(item));
    isMasterModalOpen.value = true;
};

const saveMasterItem = () => {
    if (!masterItemForm.value.name && masterTargetType.value !== 'ta') {
        emit('showToast', 'Lengkapi nama data master.');
        return;
    }

    if (masterTargetType.value === 'jurusan') {
        if (isEditingMaster.value) {
            const idx = masterJurusanList.value.findIndex((j) => j.code === masterItemForm.value.code);
            if (idx !== -1) masterJurusanList.value[idx] = { ...masterItemForm.value };
            emit('showToast', `Jurusan [${masterItemForm.value.code}] berhasil diperbarui.`);
        } else {
            masterJurusanList.value.push({
                ...masterItemForm.value,
                rombelCount: 0,
                siswaCount: 0,
            });
            emit('showToast', `Jurusan baru [${masterItemForm.value.code}] berhasil ditambahkan.`);
        }
    } else if (masterTargetType.value === 'rombel') {
        if (isEditingMaster.value) {
            const idx = masterRombelList.value.findIndex((r) => r.code === masterItemForm.value.code);
            if (idx !== -1) masterRombelList.value[idx] = { ...masterItemForm.value };
            emit('showToast', `Rombel [${masterItemForm.value.name}] berhasil diperbarui.`);
        } else {
            masterRombelList.value.push({
                ...masterItemForm.value,
                code: masterItemForm.value.name.toUpperCase().replace(/\s+/g, '-'),
            });
            emit('showToast', `Rombel [${masterItemForm.value.name}] berhasil ditambahkan.`);
        }
    } else if (masterTargetType.value === 'mapel') {
        if (isEditingMaster.value) {
            const idx = masterMapelList.value.findIndex((m) => m.code === masterItemForm.value.code);
            if (idx !== -1) masterMapelList.value[idx] = { ...masterItemForm.value };
            emit('showToast', `Mata Pelajaran [${masterItemForm.value.name}] berhasil diperbarui.`);
        } else {
            masterMapelList.value.push({
                ...masterItemForm.value,
                code: masterItemForm.value.code || masterItemForm.value.name.substring(0, 4).toUpperCase(),
            });
            emit('showToast', `Mata Pelajaran [${masterItemForm.value.name}] berhasil ditambahkan.`);
        }
    } else if (masterTargetType.value === 'ta') {
        if (isEditingMaster.value) {
            const idx = masterTahunAjaranList.value.findIndex((t) => t.id === masterItemForm.value.id);
            if (idx !== -1) masterTahunAjaranList.value[idx] = { ...masterItemForm.value };
            emit('showToast', `Tahun ajaran berhasil diperbarui.`);
        } else {
            masterTahunAjaranList.value.push({
                ...masterItemForm.value,
                id: Date.now(),
            });
            emit('showToast', `Tahun ajaran baru berhasil ditambahkan.`);
        }
    }
    isMasterModalOpen.value = false;
};

const deleteMasterItem = (type, keyOrId) => {
    if (type === 'jurusan') {
        masterJurusanList.value = masterJurusanList.value.filter((j) => j.code !== keyOrId);
        emit('showToast', `Jurusan ${keyOrId} berhasil dihapus.`);
    } else if (type === 'rombel') {
        masterRombelList.value = masterRombelList.value.filter((r) => r.code !== keyOrId);
        emit('showToast', `Rombel ${keyOrId} berhasil dihapus.`);
    } else if (type === 'mapel') {
        masterMapelList.value = masterMapelList.value.filter((m) => m.code !== keyOrId);
        emit('showToast', `Mata pelajaran ${keyOrId} berhasil dihapus.`);
    }
};

const activateTahunAjaran = (ta) => {
    masterTahunAjaranList.value.forEach((t) => {
        t.status = t.id === ta.id ? 'Aktif' : 'Arsip';
    });
    selectedTahunAjaran.value = `${ta.tahun} • ${ta.semester}`;
    emit('showToast', `Tahun ajaran ${ta.tahun} ${ta.semester} resmi diaktifkan.`);
};

// ========================================================
// TAB 4: PENUGASAN & JADWAL STATE & METHODS
// ========================================================
const selectedTahunAjaran = ref('2026/2027 • Ganjil');
const jadwalViewMode = ref('rombel'); // 'rombel' | 'guru'
const selectedHariJadwal = ref('Senin');
const selectedRombelFilter = ref('X TBSM A'); // 'semua' | Rombel Name
const selectedGuruFilter = ref('Hendra Gunawan, S.T');

const plottingWali = ref(JSON.parse(JSON.stringify(initialPlottingWali)));
const plottingGuruMapel = ref(JSON.parse(JSON.stringify(initialPlottingGuruMapel)));
const jadwalMingguan = ref(JSON.parse(JSON.stringify(initialJadwalMingguan)));

// Guru list for selectors
const allGuruList = computed(() => {
    return [
        { name: 'Hendra Gunawan, S.T', nip: '19850314 201101 1 004', bidang: 'Teknik Sepeda Motor' },
        { name: 'Dedi Kurniawan, S.Pd', nip: '19890422 201602 1 003', bidang: 'Teknik Sepeda Motor' },
        { name: 'Nurul Hidayah, S.E.I', nip: '19910815 201903 2 006', bidang: 'Perbankan Syariah' },
        { name: 'Zulfa Ramadhani, M.E', nip: '19920317 202001 2 002', bidang: 'Perbankan Syariah' },
        { name: 'Budi Santoso, S.Kom', nip: '19880412 201504 1 002', bidang: 'RPL & Informatika' },
        { name: 'Dra. Sri Mulyani', nip: '19680214 199403 2 001', bidang: 'Bahasa Indonesia' },
        { name: 'Agus Salim, M.Pd', nip: '19850719 201101 1 003', bidang: 'Matematika Vokasi' },
        { name: 'Ahmad Fauzi, S.Pd.I', nip: '19850614 201201 1 004', bidang: 'Pendidikan Agama Islam' },
        { name: 'Andi Saputra, S.Sn', nip: '19870311 201602 1 003', bidang: 'DKV & Fotografi' },
        { name: 'Irfan Maulana, S.T', nip: '19910522 201802 1 005', bidang: 'Teknik Komputer Jaringan' },
        { name: 'Rina Kusuma, M.Kom', nip: '19900915 201903 2 004', bidang: 'Multimedia & Desain' },
    ];
});

// Filtered schedules for Rombel view
const filteredJadwal = computed(() => {
    return jadwalMingguan.value.filter((j) => {
        const matchHari = j.hari === selectedHariJadwal.value;
        const matchRombel = selectedRombelFilter.value === 'semua' || j.rombel === selectedRombelFilter.value;
        const matchTa = !j.ta || j.ta === selectedTahunAjaran.value;
        return matchHari && matchRombel && matchTa;
    });
});

// Filtered schedules for Guru Timeline view
const guruTimelineJadwal = computed(() => {
    return jadwalMingguan.value.filter((j) => {
        const matchGuru = j.guru === selectedGuruFilter.value;
        const matchTa = !j.ta || j.ta === selectedTahunAjaran.value;
        return matchGuru && matchTa;
    });
});

// Jadwal Guru for selected day
const guruDayJadwal = computed(() => {
    return guruTimelineJadwal.value.filter((j) => j.hari === selectedHariJadwal.value);
});

// Total jam mengajar guru dalam seminggu
const guruTotalJamMinggu = computed(() => {
    return guruTimelineJadwal.value.length * 3;
});

// Engine Deteksi Bentrok Jadwal (Collision Detection Engine)
const detectConflict = (form, excludeId = null) => {
    for (const item of jadwalMingguan.value) {
        if (excludeId && item.id === excludeId) continue;
        if (item.hari === form.hari && item.jamKe === form.jamKe) {
            // 1. Bentrok Guru (Guru yang sama tidak bisa di 2 kelas di jam yang sama)
            if (form.guru && item.guru === form.guru) {
                return {
                    conflict: true,
                    type: 'guru',
                    message: `BENTROK GURU: ${form.guru} sudah memiliki jadwal mengajar di kelas [${item.rombel}] pada ${form.hari}, ${form.jamKe}!`,
                };
            }
            // 2. Bentrok Rombel (Kelas yang sama tidak bisa ada 2 mapel di jam yang sama)
            if (form.rombel && item.rombel === form.rombel) {
                return {
                    conflict: true,
                    type: 'rombel',
                    message: `BENTROK KELAS: Rombel [${form.rombel}] sudah memiliki jadwal [${item.mapel}] pada ${form.hari}, ${form.jamKe}!`,
                };
            }
            // 3. Bentrok Ruangan (Lab / Bengkel tidak bisa dipakai 2 kelas bersamaan)
            if (form.ruang && item.ruang === form.ruang) {
                return {
                    conflict: true,
                    type: 'ruang',
                    message: `BENTROK RUANGAN: Ruangan [${form.ruang}] sedang digunakan oleh rombel [${item.rombel}] (${item.mapel})!`,
                };
            }
        }
    }
    return { conflict: false, type: '', message: '' };
};

// Modal: Tambah/Edit Jadwal
const isJadwalModalOpen = ref(false);
const isEditingJadwal = ref(false);
const jadwalForm = ref({
    id: null,
    ta: '2026/2027 • Ganjil',
    hari: 'Senin',
    jamKe: 'Jam 1 - 3 (07:15 - 09:30)',
    rombel: 'X TBSM A',
    mapel: 'Pemeliharaan Mesin Sepeda Motor',
    guru: 'Hendra Gunawan, S.T',
    ruang: 'Bengkel TBSM 1',
    status: 'Terjadwal',
});

const currentConflict = computed(() => {
    if (!isJadwalModalOpen.value) return { conflict: false, message: '' };
    return detectConflict(jadwalForm.value, isEditingJadwal.value ? jadwalForm.value.id : null);
});

const openAddJadwalModal = (prefillHari = null, prefillRombel = null) => {
    isEditingJadwal.value = false;
    jadwalForm.value = {
        id: null,
        ta: selectedTahunAjaran.value,
        hari: prefillHari || selectedHariJadwal.value,
        jamKe: slotJamPelajaran[0].label,
        rombel: prefillRombel || (selectedRombelFilter.value !== 'semua' ? selectedRombelFilter.value : masterRombel[0].name),
        mapel: masterMapel[0].name,
        guru: allGuruList.value[0].name,
        ruang: masterRuanganList[0],
        status: 'Terjadwal',
    };
    isJadwalModalOpen.value = true;
};

const openEditJadwalModal = (item) => {
    isEditingJadwal.value = true;
    jadwalForm.value = JSON.parse(JSON.stringify(item));
    isJadwalModalOpen.value = true;
};

const saveJadwal = () => {
    if (currentConflict.value.conflict) {
        emit('showToast', currentConflict.value.message);
        return;
    }
    if (!jadwalForm.value.mapel || !jadwalForm.value.guru || !jadwalForm.value.rombel) {
        emit('showToast', 'Lengkapi seluruh data jadwal.');
        return;
    }

    if (isEditingJadwal.value) {
        const idx = jadwalMingguan.value.findIndex((j) => j.id === jadwalForm.value.id);
        if (idx !== -1) {
            jadwalMingguan.value[idx] = { ...jadwalForm.value };
            emit('showToast', `Jadwal [${jadwalForm.value.mapel} - ${jadwalForm.value.rombel}] berhasil diperbarui.`);
        }
    } else {
        jadwalMingguan.value.push({
            ...jadwalForm.value,
            id: Date.now(),
        });
        emit('showToast', `Jadwal baru untuk rombel ${jadwalForm.value.rombel} berhasil diplot.`);
    }
    isJadwalModalOpen.value = false;
};

const deleteJadwal = (id) => {
    const item = jadwalMingguan.value.find((j) => j.id === id);
    jadwalMingguan.value = jadwalMingguan.value.filter((j) => j.id !== id);
    emit('showToast', `Jadwal [${item?.mapel} - ${item?.rombel}] berhasil dihapus.`);
};

// Modal: Salin Template Jadwal ke Rombel Lain (Clone)
const isCloneModalOpen = ref(false);
const cloneForm = ref({
    fromRombel: 'X TBSM A',
    toRombel: 'X TBSM B',
    keepGuru: true,
});

const openCloneModal = () => {
    cloneForm.value = {
        fromRombel: selectedRombelFilter.value !== 'semua' ? selectedRombelFilter.value : 'X TBSM A',
        toRombel: 'X TBSM B',
        keepGuru: true,
    };
    isCloneModalOpen.value = true;
};

const executeCloneJadwal = () => {
    if (cloneForm.value.fromRombel === cloneForm.value.toRombel) {
        emit('showToast', 'Rombel tujuan tidak boleh sama dengan rombel sumber.');
        return;
    }

    const sourceJadwal = jadwalMingguan.value.filter(
        (j) => j.rombel === cloneForm.value.fromRombel && (!j.ta || j.ta === selectedTahunAjaran.value)
    );
    if (sourceJadwal.length === 0) {
        emit('showToast', `Tidak ada jadwal di rombel ${cloneForm.value.fromRombel} untuk disalin.`);
        return;
    }

    let clonedCount = 0;
    sourceJadwal.forEach((src) => {
        const newEntry = {
            ...src,
            id: Date.now() + Math.floor(Math.random() * 10000),
            rombel: cloneForm.value.toRombel,
            guru: cloneForm.value.keepGuru ? src.guru : allGuruList.value[0].name,
            ruang: src.ruang.includes('1') ? src.ruang.replace('1', '2') : src.ruang,
        };
        const exists = jadwalMingguan.value.some(
            (j) => j.rombel === cloneForm.value.toRombel && j.hari === newEntry.hari && j.jamKe === newEntry.jamKe
        );
        if (!exists) {
            jadwalMingguan.value.push(newEntry);
            clonedCount++;
        }
    });

    selectedRombelFilter.value = cloneForm.value.toRombel;
    isCloneModalOpen.value = false;
    emit('showToast', `Berhasil menyalin ${clonedCount} slot jadwal dari ${cloneForm.value.fromRombel} ke ${cloneForm.value.toRombel}!`);
};

// Modal: Ubah Wali Kelas
const isWaliModalOpen = ref(false);
const waliForm = ref({
    id: null,
    rombel: '',
    waliName: '',
    nip: '',
    ruang: '',
});

const openEditWaliModal = (w) => {
    waliForm.value = JSON.parse(JSON.stringify(w));
    isWaliModalOpen.value = true;
};

const saveWali = () => {
    const idx = plottingWali.value.findIndex((w) => w.id === waliForm.value.id);
    if (idx !== -1) {
        plottingWali.value[idx] = { ...waliForm.value };
        emit('showToast', `Wali kelas untuk ${waliForm.value.rombel} berhasil diubah menjadi ${waliForm.value.waliName}.`);
    }
    isWaliModalOpen.value = false;
};

// Modal: Tambah Plotting Guru Mapel
const isGuruMapelModalOpen = ref(false);
const guruMapelForm = ref({
    id: null,
    guruName: '',
    nip: '',
    mapel: '',
    rombelList: [],
    totalJam: 12,
    ruang: '',
});

const openAddGuruMapelModal = () => {
    guruMapelForm.value = {
        id: null,
        guruName: allGuruList.value[0].name,
        nip: allGuruList.value[0].nip,
        mapel: masterMapel[0].name,
        rombelList: ['X TBSM A', 'X TBSM B'],
        totalJam: 12,
        ruang: masterRuanganList[0],
    };
    isGuruMapelModalOpen.value = true;
};

const saveGuruMapel = () => {
    if (!guruMapelForm.value.guruName || !guruMapelForm.value.mapel || guruMapelForm.value.rombelList.length === 0) {
        emit('showToast', 'Lengkapi guru, mapel, dan minimal pilih 1 rombel.');
        return;
    }
    plottingGuruMapel.value.unshift({
        ...guruMapelForm.value,
        id: Date.now(),
    });
    isGuruMapelModalOpen.value = false;
    emit('showToast', `Plotting pengampu ${guruMapelForm.value.mapel} untuk ${guruMapelForm.value.guruName} berhasil disimpan.`);
};

// ========================================================
// TAB 5: PENGUMUMAN STATE & METHODS (CRUD)
// ========================================================
const pengumumanList = ref(JSON.parse(JSON.stringify(initialPengumumanList)));
const isPengumumanModalOpen = ref(false);
const isEditingPengumuman = ref(false);
const editingPengumumanId = ref(null);
const newPengumuman = ref({
    title: '',
    target: 'Semua Pengguna',
    urgensi: 'Normal',
    content: '',
});

const openNewPengumumanModal = () => {
    isEditingPengumuman.value = false;
    editingPengumumanId.value = null;
    newPengumuman.value = {
        title: '',
        target: 'Semua Pengguna',
        urgensi: 'Normal',
        content: '',
    };
    isPengumumanModalOpen.value = true;
};

const openEditPengumumanModal = (p) => {
    isEditingPengumuman.value = true;
    editingPengumumanId.value = p.id;
    newPengumuman.value = {
        title: p.title,
        target: p.target,
        urgensi: p.urgensi,
        content: p.content,
    };
    isPengumumanModalOpen.value = true;
};

const deletePengumuman = (id) => {
    const item = pengumumanList.value.find((p) => p.id === id);
    pengumumanList.value = pengumumanList.value.filter((p) => p.id !== id);
    emit('showToast', `Pengumuman '${item?.title}' berhasil dihapus.`);
};

const savePengumuman = () => {
    if (!newPengumuman.value.title.trim() || !newPengumuman.value.content.trim()) {
        emit('showToast', 'Lengkapi judul dan isi pengumuman terlebih dahulu.');
        return;
    }

    if (isEditingPengumuman.value) {
        const idx = pengumumanList.value.findIndex((p) => p.id === editingPengumumanId.value);
        if (idx !== -1) {
            pengumumanList.value[idx] = {
                ...pengumumanList.value[idx],
                title: newPengumuman.value.title.trim(),
                target: newPengumuman.value.target,
                targetBadge:
                    newPengumuman.value.target === 'Semua Pengguna'
                        ? 'bg-charcoal text-white'
                        : 'bg-sky-light text-sky-dark',
                urgensi: newPengumuman.value.urgensi,
                urgensiBadge:
                    newPengumuman.value.urgensi === 'Penting'
                        ? 'bg-blossom-light text-blossom-dark'
                        : 'bg-sage-light text-sage-dark',
                content: newPengumuman.value.content.trim(),
            };
            emit('showToast', `Pengumuman '${newPengumuman.value.title}' berhasil diperbarui.`);
        }
    } else {
        pengumumanList.value.unshift({
            id: Date.now(),
            title: newPengumuman.value.title.trim(),
            target: newPengumuman.value.target,
            targetBadge:
                newPengumuman.value.target === 'Semua Pengguna'
                    ? 'bg-charcoal text-white'
                    : 'bg-sky-light text-sky-dark',
            urgensi: newPengumuman.value.urgensi,
            urgensiBadge:
                newPengumuman.value.urgensi === 'Penting'
                    ? 'bg-blossom-light text-blossom-dark'
                    : 'bg-sage-light text-sage-dark',
            status: 'Tayang',
            date: 'Hari ini',
            author: 'Rizky Pratama (Operator TU)',
            content: newPengumuman.value.content.trim(),
            views: 1,
        });
        emit('showToast', 'Pengumuman sekolah berhasil dipublikasikan ke seluruh target pengguna.');
    }
    isPengumumanModalOpen.value = false;
};

// ========================================================
// TAB 6: PENGATURAN SISTEM & PEMELIHARAAN LMS
// ========================================================
const systemSettings = ref(JSON.parse(JSON.stringify(initialSystemSettings)));
const isBackingUp = ref(false);

const handleRunBackup = () => {
    isBackingUp.value = true;
    emit('showToast', 'Memulai proses pencadangan basis data snapshot...');
    setTimeout(() => {
        isBackingUp.value = false;
        const now = new Date();
        const timeStr = `Hari ini, ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')} WIB`;
        systemSettings.value.lastBackupTime = timeStr;
        emit('showToast', 'Pencadangan basis data (snapshot SQL) berhasil disimpan.');
    }, 1200);
};

const handleToggleMaintenance = () => {
    systemSettings.value.maintenanceMode = !systemSettings.value.maintenanceMode;
    emit(
        'showToast',
        systemSettings.value.maintenanceMode
            ? 'PERINGATAN: Mode Pemeliharaan aktif. Akses siswa dan guru dikunci sementara.'
            : 'Mode Pemeliharaan dinonaktifkan. Seluruh akses sistem normal kembali.'
    );
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
                    :type="activeTab === 'home' ? 'dashboard' : (activeTab === 'master' || activeTab.startsWith('master_')) ? 'mapel-list' : 'card-list'"
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
                                    <span class="text-[11px] font-bold text-subtext">Total Siswa Terdata</span>
                                    <span class="w-8 h-8 rounded-full bg-sky-light text-sky-dark flex items-center justify-center font-bold text-xs">
                                        <LmsIcon name="user" className="w-4 h-4" />
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <div class="text-2xl font-black text-charcoal">{{ userList.filter(u => u.role === 'siswa').length }} Akun</div>
                                    <div class="text-[11px] font-medium text-subtext mt-0.5">{{ masterRombelList.length }} Rombel Aktif</div>
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
                                    <div class="text-2xl font-black text-charcoal">{{ userList.filter(u => u.role === 'guru' || u.role === 'wali').length }} Guru</div>
                                    <div class="text-[11px] font-medium text-subtext mt-0.5">{{ userList.filter(u => u.role === 'wali').length }} Wali Kelas Terplot</div>
                                </div>
                            </div>

                            <!-- KPI 3: Plotting Jadwal -->
                            <div
                                @click="$emit('selectTab', 'penugasan_jadwal')"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between cursor-pointer hover:border-charcoal/30 transition-colors"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-subtext">Jadwal Mingguan</span>
                                    <span class="w-8 h-8 rounded-full bg-lavender-light text-lavender-dark flex items-center justify-center font-bold text-xs">
                                        <LmsIcon name="clock" className="w-4 h-4" />
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <div class="text-2xl font-black text-charcoal">{{ jadwalMingguan.length }} Sesi</div>
                                    <div class="text-[11px] font-medium text-subtext mt-0.5">100% Terdistribusi Valid</div>
                                </div>
                            </div>

                            <!-- KPI 4: Konflik Jadwal -->
                            <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-subtext">Status Konflik</span>
                                    <span class="w-8 h-8 rounded-full bg-[#E8F5E9] text-[#2E7D32] flex items-center justify-center font-black text-xs">
                                        <LmsIcon name="check" className="w-4 h-4" />
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
                                        @click="$emit('selectTab', 'penugasan_jadwal')"
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
                                            <p class="text-[11px] text-subtext mt-1">Kelola akun Siswa, Guru, Wali Kelas, dan Kepala Sekolah. Aktivasi status akun.</p>
                                        </div>
                                        <div class="text-xs font-bold text-charcoal mt-3 flex items-center gap-1">
                                            Kelola Akun →
                                        </div>
                                    </div>

                                    <div
                                        @click="$emit('selectTab', 'master_jurusan')"
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
                                                <div class="font-extrabold text-charcoal">Aktivasi Akun: Dimas Anggara (XII RPL 2)</div>
                                                <div class="text-[10px] text-subtext">Hari ini, 07:15 WIB • Status akun diaktifkan kembali</div>
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
                    <!-- TAB 2: MANAJEMEN PENGGUNA (SISWA, GURU, WALI, KEPSEK)    -->
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
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 3: DATA MASTER (TA, JURUSAN, KELAS, MAPEL)           -->
                    <!-- ======================================================== -->
                    <div v-else-if="activeTab === 'master' || activeTab.startsWith('master_')" class="space-y-4">
                        <!-- Sub-tab switcher -->
                        <div class="bg-white rounded-[26px] p-3 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-1.5 p-1 bg-muted/70 rounded-2xl w-full sm:w-auto flex-wrap">
                                <button
                                    type="button"
                                    @click="setMasterSubTab('jurusan')"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="masterSubTab === 'jurusan' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Jurusan ({{ masterJurusanList.length }})
                                </button>
                                <button
                                    type="button"
                                    @click="setMasterSubTab('rombel')"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="masterSubTab === 'rombel' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Rombel Kelas ({{ masterRombelList.length }})
                                </button>
                                <button
                                    type="button"
                                    @click="setMasterSubTab('mapel')"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="masterSubTab === 'mapel' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Mata Pelajaran ({{ masterMapelList.length }})
                                </button>
                                <button
                                    type="button"
                                    @click="setMasterSubTab('ta')"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="masterSubTab === 'ta' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Tahun Ajaran ({{ masterTahunAjaranList.length }})
                                </button>
                            </div>

                            <button
                                type="button"
                                @click="openAddMasterModal(masterSubTab)"
                                class="px-4 py-2 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs self-start sm:self-auto flex items-center gap-1.5"
                            >
                                <span>+ Tambah {{ masterSubTab === 'ta' ? 'Tahun Ajaran' : masterSubTab === 'jurusan' ? 'Jurusan' : masterSubTab === 'rombel' ? 'Rombel' : 'Mapel' }}</span>
                            </button>
                        </div>

                        <!-- SUB 1: JURUSAN -->
                        <div v-if="masterSubTab === 'jurusan'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                v-for="j in masterJurusanList"
                                :key="j.code"
                                class="bg-white rounded-[26px] p-5 border border-[#ECEAE4] soft-shadow flex flex-col justify-between hover:border-charcoal/30 transition-all"
                            >
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-black px-2.5 py-1 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal">
                                            Kode: {{ j.code }}
                                        </span>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[11px] font-bold text-subtext mr-1">{{ j.tingkat }}</span>
                                            <button
                                                type="button"
                                                @click="openEditMasterModal('jurusan', j)"
                                                class="px-2.5 py-1 rounded-lg bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                                                title="Edit Jurusan"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                type="button"
                                                @click="deleteMasterItem('jurusan', j.code)"
                                                class="px-2 py-1 rounded-lg bg-canvas border border-[#ECEAE4] text-[#D32F2F] hover:bg-blossom-light text-xs font-bold active:scale-95 transition-all cursor-pointer shadow-2xs"
                                                title="Hapus Jurusan"
                                            >
                                                <LmsIcon name="trash" className="w-3.5 h-3.5" />
                                            </button>
                                        </div>
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
                                v-for="r in masterRombelList"
                                :key="r.code"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between hover:border-charcoal/30 transition-all"
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
                                    <span class="text-subtext">Kapasitas: <strong class="text-charcoal">{{ r.terisi }} / {{ r.kapasitas }}</strong></span>
                                    <div class="flex items-center gap-1.5">
                                        <button
                                            type="button"
                                            @click="openEditMasterModal('rombel', r)"
                                            class="px-2.5 py-1 rounded-lg bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            @click="deleteMasterItem('rombel', r.code)"
                                            class="px-2 py-1 rounded-lg bg-canvas border border-[#ECEAE4] text-[#D32F2F] hover:bg-blossom-light text-xs font-bold active:scale-95 transition-all cursor-pointer shadow-2xs"
                                        >
                                            <LmsIcon name="trash" className="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SUB 3: MATA PELAJARAN -->
                        <div v-else-if="masterSubTab === 'mapel'" class="space-y-2.5">
                            <div
                                v-for="m in masterMapelList"
                                :key="m.code"
                                class="bg-white rounded-[22px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-charcoal/30 transition-all"
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
                                <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                                    <span class="text-xs font-extrabold px-3 py-1 rounded-full bg-canvas border border-[#ECEAE4] text-charcoal">
                                        KKM: {{ m.kkm }}
                                    </span>
                                    <button
                                        type="button"
                                        @click="openEditMasterModal('mapel', m)"
                                        class="px-3 py-1 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        @click="deleteMasterItem('mapel', m.code)"
                                        class="px-2 py-1 rounded-xl bg-canvas border border-[#ECEAE4] text-[#D32F2F] hover:bg-blossom-light text-xs font-bold active:scale-95 transition-all cursor-pointer shadow-2xs"
                                    >
                                        <LmsIcon name="trash" className="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- SUB 4: TAHUN AJARAN -->
                        <div v-else class="space-y-3">
                            <div
                                v-for="ta in masterTahunAjaranList"
                                :key="ta.id"
                                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between gap-3 flex-wrap hover:border-charcoal/30 transition-all"
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
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        @click="openEditMasterModal('ta', ta)"
                                        class="px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        v-if="ta.status !== 'Aktif'"
                                        type="button"
                                        @click="activateTahunAjaran(ta)"
                                        class="px-3.5 py-1.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-2xs"
                                    >
                                        Aktifkan TA
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 4: PENUGASAN & JADWAL (PLOTTING GURU & JADWAL)       -->
                    <!-- ======================================================== -->
                    <div v-else-if="activeTab === 'penugasan' || activeTab.startsWith('penugasan_')" class="space-y-4">
                        <!-- Mode Switcher -->
                        <div class="bg-white rounded-[26px] p-3 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-1.5 p-1 bg-muted/70 rounded-2xl w-full sm:w-auto flex-wrap">
                                <button
                                    type="button"
                                    @click="setPenugasanMode('wali')"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="penugasanMode === 'wali' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Plotting Wali Kelas (42 Rombel)
                                </button>
                                <button
                                    type="button"
                                    @click="setPenugasanMode('guruMapel')"
                                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                    :class="penugasanMode === 'guruMapel' ? 'bg-white text-charcoal shadow-xs' : 'text-subtext hover:text-charcoal'"
                                >
                                    Plotting Guru Mapel
                                </button>
                                <button
                                    type="button"
                                    @click="setPenugasanMode('jadwal')"
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
                            <div class="hero-lavender rounded-[26px] p-4 soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-base font-extrabold text-charcoal">Status Plotting Pembina Rombel (Wali Kelas)</h3>
                                    <p class="text-xs text-charcoal/75 mt-0.5">Penetapan guru wali kelas per rombel aktif tahun ajaran {{ selectedTahunAjaran }}.</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 rounded-full bg-white text-charcoal text-xs font-black shadow-2xs">
                                        {{ plottingWali.length }} Rombel Terplot
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                                <div
                                    v-for="w in plottingWali"
                                    :key="w.id"
                                    class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between hover:border-charcoal/20 transition-all"
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
                                        <span class="text-subtext font-medium">{{ w.siswaCount }} Siswa Terdaftar</span>
                                        <button
                                            type="button"
                                            @click="openEditWaliModal(w)"
                                            class="px-2.5 py-1 rounded-lg bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                                        >
                                            Ubah Wali →
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MODE 2: PLOTTING GURU MAPEL -->
                        <div v-else-if="penugasanMode === 'guruMapel'" class="space-y-4">
                            <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-sm font-extrabold text-charcoal">Distribusi Pengampu Mata Pelajaran</h3>
                                    <p class="text-xs text-subtext mt-0.5">Menentukan guru mata pelajaran untuk tiap rombel & total beban jam mengajar mingguan.</p>
                                </div>
                                <button
                                    type="button"
                                    @click="openAddGuruMapelModal"
                                    class="px-4 py-2 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs shrink-0"
                                >
                                    + Plot Guru Mapel
                                </button>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="g in plottingGuruMapel"
                                    :key="g.id"
                                    class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-charcoal/20 transition-all"
                                >
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-sm font-extrabold text-charcoal">{{ g.guruName }}</h4>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-canvas border border-[#ECEAE4] text-subtext">NIP: {{ g.nip }}</span>
                                        </div>
                                        <div class="text-xs font-extrabold text-sky-dark mt-1 flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-dark"></span>
                                            {{ g.mapel }}
                                        </div>
                                        <div class="text-[11px] text-subtext mt-1">
                                            Rombel Ajar: <span class="font-bold text-charcoal">{{ g.rombelList.join(', ') }}</span> • Ruang: {{ g.ruang }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                                        <span class="px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-extrabold text-charcoal">
                                            {{ g.totalJam }} Jam/Minggu
                                        </span>
                                        <button
                                            type="button"
                                            @click="openAddJadwalModal(null, g.rombelList[0])"
                                            class="px-3 py-1.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer shadow-2xs"
                                        >
                                            Jadwalkan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MODE 3: JADWAL PELAJARAN MINGGUAN (KOMPREHENSIF) -->
                        <div v-else class="space-y-4">
                            <!-- Filter & Control Panel -->
                            <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow space-y-3.5">
                                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-[#F2F0EC]">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <!-- Tahun Ajaran & Semester Switcher -->
                                        <div class="flex items-center gap-2 bg-canvas px-3 py-1.5 rounded-2xl border border-[#ECEAE4]">
                                            <span class="text-[11px] font-bold text-subtext">Tahun Ajaran:</span>
                                            <select
                                                v-model="selectedTahunAjaran"
                                                class="bg-transparent text-xs font-black text-charcoal focus:outline-none cursor-pointer"
                                            >
                                                <option v-for="ta in masterTahunAjaranList" :key="ta.id" :value="`${ta.tahun} • ${ta.semester}`">
                                                    {{ ta.tahun }} • {{ ta.semester }} ({{ ta.status }})
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Dual View Mode: Per Rombel vs Timeline Guru -->
                                        <div class="flex items-center p-1 bg-muted/70 rounded-xl">
                                            <button
                                                type="button"
                                                @click="jadwalViewMode = 'rombel'"
                                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5"
                                                :class="jadwalViewMode === 'rombel' ? 'bg-white text-charcoal shadow-2xs' : 'text-subtext hover:text-charcoal'"
                                            >
                                                <LmsIcon name="user" className="w-3.5 h-3.5" />
                                                <span>Jadwal per Rombel (Kelas)</span>
                                            </button>
                                            <button
                                                type="button"
                                                @click="jadwalViewMode = 'guru'"
                                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5"
                                                :class="jadwalViewMode === 'guru' ? 'bg-white text-charcoal shadow-2xs' : 'text-subtext hover:text-charcoal'"
                                            >
                                                <LmsIcon name="clock" className="w-3.5 h-3.5" />
                                                <span>Timeline Harian Guru</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-2 self-start lg:self-auto flex-wrap">
                                        <button
                                            type="button"
                                            @click="openCloneModal"
                                            class="px-3.5 py-2 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal text-xs font-bold hover:bg-muted active:scale-95 transition-all cursor-pointer shadow-2xs flex items-center gap-1.5"
                                            title="Salin template jadwal dari satu rombel ke rombel lain"
                                        >
                                            <svg class="w-3.5 h-3.5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                                            </svg>
                                            <span>Salin ke Rombel Lain</span>
                                        </button>

                                        <button
                                            type="button"
                                            @click="openAddJadwalModal()"
                                            class="px-4 py-2 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs flex items-center gap-1.5"
                                        >
                                            <span>+ Plot Jadwal Baru</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- SUB-FILTER VIEW 1: PER ROMBEL -->
                                <div v-if="jadwalViewMode === 'rombel'" class="space-y-3">
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-1">
                                            <span class="text-xs font-bold text-subtext mr-1 shrink-0">Pilih Rombel:</span>
                                            <button
                                                type="button"
                                                @click="selectedRombelFilter = 'semua'"
                                                class="px-3 py-1 rounded-full text-xs font-bold transition-all cursor-pointer shrink-0"
                                                :class="selectedRombelFilter === 'semua' ? 'bg-charcoal text-white' : 'bg-canvas text-subtext border border-[#ECEAE4] hover:text-charcoal'"
                                            >
                                                Semua
                                            </button>
                                            <button
                                                v-for="r in masterRombelList"
                                                :key="r.code"
                                                type="button"
                                                @click="selectedRombelFilter = r.name"
                                                class="px-3 py-1 rounded-full text-xs font-bold transition-all cursor-pointer shrink-0"
                                                :class="selectedRombelFilter === r.name ? 'bg-charcoal text-white shadow-2xs' : 'bg-canvas text-charcoal border border-[#ECEAE4] hover:bg-muted'"
                                            >
                                                {{ r.name }}
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Hari Selector -->
                                    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pt-1">
                                        <button
                                            v-for="h in ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']"
                                            :key="h"
                                            type="button"
                                            @click="selectedHariJadwal = h"
                                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0"
                                            :class="selectedHariJadwal === h ? 'bg-sage-light text-sage-dark border border-[#C8DFCD] font-black' : 'bg-canvas border border-[#ECEAE4] text-subtext hover:text-charcoal'"
                                        >
                                            {{ h }}
                                        </button>
                                    </div>
                                </div>

                                <!-- SUB-FILTER VIEW 2: PER GURU (TIMELINE HARIAN) -->
                                <div v-else class="space-y-3">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-canvas p-3 rounded-2xl border border-[#ECEAE4]">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-subtext shrink-0">Pilih Guru:</span>
                                            <select
                                                v-model="selectedGuruFilter"
                                                class="bg-white border border-[#ECEAE4] rounded-xl px-3 py-1.5 text-xs font-extrabold text-charcoal focus:outline-none cursor-pointer"
                                            >
                                                <option v-for="g in allGuruList" :key="g.nip" :value="g.name">
                                                    {{ g.name }} ({{ g.bidang }})
                                                </option>
                                            </select>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs">
                                            <span class="text-subtext">Total Beban Mengajar:</span>
                                            <span class="font-black px-2.5 py-0.5 rounded-lg bg-sage-light text-sage-dark">
                                                {{ guruTotalJamMinggu }} Jam / Minggu ({{ guruTimelineJadwal.length }} Sesi)
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Hari Selector for Guru View -->
                                    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pt-1">
                                        <button
                                            v-for="h in ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']"
                                            :key="h"
                                            type="button"
                                            @click="selectedHariJadwal = h"
                                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0"
                                            :class="selectedHariJadwal === h ? 'bg-sky-light text-sky-dark border border-[#BAE6FD] font-black' : 'bg-canvas border border-[#ECEAE4] text-subtext hover:text-charcoal'"
                                        >
                                            {{ h }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- BANNER INFO JADWAL -->
                            <div class="px-2 flex items-center justify-between text-xs text-subtext">
                                <span v-if="jadwalViewMode === 'rombel'">
                                    Jadwal <strong>{{ selectedHariJadwal }}</strong> • Rombel: <strong>{{ selectedRombelFilter }}</strong> • {{ filteredJadwal.length }} Mata Pelajaran Terjadwal
                                </span>
                                <span v-else>
                                    Timeline Mengajar <strong>{{ selectedGuruFilter }}</strong> pada hari <strong>{{ selectedHariJadwal }}</strong> • {{ guruDayJadwal.length }} Kelas
                                </span>
                                <span class="text-[11px] font-bold text-sage-dark flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-[#2E7D32]"></span>
                                    Sistem Anti-Bentrok Aktif
                                </span>
                            </div>

                            <!-- LIST JADWAL: MODE ROMBEL -->
                            <div v-if="jadwalViewMode === 'rombel'" class="space-y-3">
                                <div
                                    v-if="filteredJadwal.length === 0"
                                    class="bg-white rounded-[26px] p-8 text-center border border-dashed border-[#ECEAE4] space-y-3"
                                >
                                    <div class="w-12 h-12 rounded-full bg-canvas mx-auto flex items-center justify-center text-subtext">
                                        <svg class="w-6 h-6 text-subtext/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                            <line x1="16" y1="2" x2="16" y2="6" />
                                            <line x1="8" y1="2" x2="8" y2="6" />
                                            <line x1="3" y1="10" x2="21" y2="10" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-extrabold text-charcoal">Belum Ada Jadwal di Hari {{ selectedHariJadwal }}</h4>
                                        <p class="text-xs text-subtext mt-0.5">Rombel {{ selectedRombelFilter }} belum memiliki mata pelajaran di hari ini.</p>
                                    </div>
                                    <button
                                        type="button"
                                        @click="openAddJadwalModal(selectedHariJadwal, selectedRombelFilter !== 'semua' ? selectedRombelFilter : null)"
                                        class="px-4 py-2 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs inline-block"
                                    >
                                        + Plot Jadwal {{ selectedHariJadwal }}
                                    </button>
                                </div>

                                <div
                                    v-for="j in filteredJadwal"
                                    :key="j.id"
                                    class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col md:flex-row md:items-center justify-between gap-3 hover:border-charcoal/25 transition-all"
                                >
                                    <div class="flex items-start gap-3.5 min-w-0">
                                        <div class="px-3 py-2 rounded-2xl bg-canvas border border-[#ECEAE4] text-center shrink-0">
                                            <div class="text-[10px] font-extrabold text-subtext uppercase">{{ j.hari }}</div>
                                            <div class="text-xs font-black text-charcoal whitespace-nowrap">{{ j.jamKe.split('(')[0] }}</div>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-xs font-black px-2.5 py-0.5 rounded-lg bg-charcoal text-white">
                                                    {{ j.rombel }}
                                                </span>
                                                <h4 class="text-sm font-extrabold text-charcoal">{{ j.mapel }}</h4>
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-sage-light text-sage-dark">
                                                    {{ j.status }}
                                                </span>
                                            </div>
                                            <div class="text-xs text-subtext mt-1.5 flex items-center gap-3.5 flex-wrap">
                                                <span class="inline-flex items-center gap-1.5">
                                                    <LmsIcon name="user" className="w-3.5 h-3.5 text-subtext/80" />
                                                    <span>Guru: <strong class="text-charcoal">{{ j.guru }}</strong></span>
                                                </span>
                                                <span class="inline-flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5 text-subtext/80 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                                        <circle cx="12" cy="10" r="3" />
                                                    </svg>
                                                    <span>Ruang: <strong class="text-charcoal">{{ j.ruang }}</strong></span>
                                                </span>
                                                <span class="inline-flex items-center gap-1.5">
                                                    <LmsIcon name="clock" className="w-3.5 h-3.5 text-subtext/80" />
                                                    <span>Waktu: <strong class="text-charcoal">{{ j.jamKe.includes('(') ? j.jamKe.split('(')[1].replace(')', '') : '' }}</strong></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0 self-end md:self-auto">
                                        <button
                                            type="button"
                                            @click="openEditJadwalModal(j)"
                                            class="px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            type="button"
                                            @click="deleteJadwal(j.id)"
                                            class="px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-blossom-dark hover:bg-blossom-light active:scale-95 transition-all cursor-pointer"
                                            title="Hapus jadwal ini"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- LIST JADWAL: MODE GURU (TIMELINE HARIAN) -->
                            <div v-else class="space-y-3">
                                <div
                                    v-if="guruDayJadwal.length === 0"
                                    class="bg-white rounded-[26px] p-8 text-center border border-dashed border-[#ECEAE4] space-y-2"
                                >
                                    <div class="w-12 h-12 rounded-full bg-canvas mx-auto flex items-center justify-center text-subtext">
                                        <svg class="w-6 h-6 text-subtext/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 8h1a4 4 0 0 1 0 8h-1" />
                                            <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z" />
                                            <line x1="6" y1="1" x2="6" y2="4" />
                                            <line x1="10" y1="1" x2="10" y2="4" />
                                            <line x1="14" y1="1" x2="14" y2="4" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-extrabold text-charcoal">Guru Bebas Tugas di Hari {{ selectedHariJadwal }}</h4>
                                    <p class="text-xs text-subtext">Tidak ada jadwal mengajar untuk {{ selectedGuruFilter }} pada hari {{ selectedHariJadwal }}.</p>
                                </div>

                                <div
                                    v-for="j in guruDayJadwal"
                                    :key="j.id"
                                    class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col md:flex-row md:items-center justify-between gap-3"
                                >
                                    <div class="flex items-start gap-3 min-w-0">
                                        <div class="px-3 py-2 rounded-2xl bg-sky-light text-sky-dark border border-[#BAE6FD] text-center shrink-0">
                                            <div class="text-[10px] font-bold uppercase">{{ j.hari }}</div>
                                            <div class="text-xs font-black">{{ j.jamKe.split('(')[0] }}</div>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-black px-2.5 py-0.5 rounded-lg bg-canvas border border-[#ECEAE4] text-charcoal">
                                                    Kelas: {{ j.rombel }}
                                                </span>
                                                <h4 class="text-sm font-extrabold text-charcoal">{{ j.mapel }}</h4>
                                            </div>
                                            <div class="text-xs text-subtext mt-1">
                                                Lokasi: <strong>{{ j.ruang }}</strong> • Durasi: {{ j.jamKe }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 shrink-0 self-end md:self-auto">
                                        <button
                                            type="button"
                                            @click="openEditJadwalModal(j)"
                                            class="px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                                        >
                                            Ubah
                                        </button>
                                    </div>
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

                                <div class="flex items-center gap-2 pt-1 text-xs flex-wrap">
                                    <button
                                        type="button"
                                        @click="$emit('showToast', `Pengumuman '${p.title}' disiarkan ulang.`)"
                                        class="px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal font-bold hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                                    >
                                        Broadcast Ulang
                                    </button>
                                    <button
                                        type="button"
                                        @click="openEditPengumumanModal(p)"
                                        class="px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal font-bold hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        @click="deletePengumuman(p.id)"
                                        class="px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-[#D32F2F] hover:bg-blossom-light font-bold active:scale-95 transition-all cursor-pointer shadow-2xs"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ======================================================== -->
                    <!-- TAB 6: PENGATURAN SISTEM & PEMELIHARAAN LMS              -->
                    <!-- ======================================================== -->
                    <div v-else-if="activeTab === 'sistem'" class="space-y-4">
                        <!-- Top Banner -->
                        <div class="bg-white rounded-[26px] p-5 border border-[#ECEAE4] soft-shadow flex flex-col md:flex-row md:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-xl bg-charcoal text-white flex items-center justify-center font-bold">
                                        <LmsIcon name="settings" className="w-4 h-4" />
                                    </span>
                                    <h3 class="text-base font-extrabold text-charcoal">Pengaturan Sistem &amp; Batas Operasional LMS</h3>
                                </div>
                                <p class="text-xs text-subtext">
                                    Konfigurasi kapasitas berkas pembelajaran, batas waktu sesi keamanan, status pemeliharaan, dan pencadangan basis data.
                                </p>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-[11px] font-bold text-subtext">
                                    Zona Waktu: <strong class="text-charcoal">{{ systemSettings.serverTimezone }}</strong>
                                </span>
                                <span class="px-3 py-1.5 rounded-xl bg-sage-light text-sage-dark text-[11px] font-black">
                                    Dapodik: {{ systemSettings.dapodikStatus }}
                                </span>
                            </div>
                        </div>

                        <!-- Parameter Operasional Card -->
                        <div class="bg-white rounded-[26px] p-6 border border-[#ECEAE4] soft-shadow space-y-6">
                            <div class="flex items-center justify-between pb-3 border-b border-[#ECEAE4]">
                                <div>
                                    <h4 class="text-sm font-black text-charcoal">Parameter Operasional &amp; Batas Berkas</h4>
                                    <p class="text-xs text-subtext mt-0.5">Konfigurasi batas penyimpanan dan keamanan sesi pengguna.</p>
                                </div>
                                <span class="text-xs font-bold px-3 py-1 rounded-full bg-canvas border border-[#ECEAE4] text-charcoal">
                                    Tahun Ajaran: {{ systemSettings.tahunAjaranAktif }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Parameter 1: Batas Maksimal Upload Berkas -->
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-charcoal">Batas Maksimal Upload Berkas (Per File):</label>
                                    <div class="grid grid-cols-4 gap-2">
                                        <button
                                            v-for="size in [10, 15, 25, 50]"
                                            :key="size"
                                            type="button"
                                            @click="systemSettings.maxUploadMb = size; $emit('showToast', `Batas upload disetel ke ${size} MB`)"
                                            class="cursor-pointer py-2 text-xs font-bold rounded-xl border text-center transition-all"
                                            :class="systemSettings.maxUploadMb === size ? 'bg-charcoal text-white border-charcoal shadow-xs' : 'bg-canvas text-subtext border-[#ECEAE4] hover:text-charcoal hover:bg-white'"
                                        >
                                            {{ size }} MB
                                        </button>
                                    </div>
                                    <p class="text-[11px] text-subtext">Berlaku untuk materi modul guru, pengumpulan tugas siswa, dan lampiran pengumuman.</p>
                                </div>

                                <!-- Parameter 2: Session Timeout -->
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-charcoal">Batas Waktu Sesi Tidak Aktif (Session Timeout):</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        <button
                                            v-for="min in [30, 60, 120]"
                                            :key="min"
                                            type="button"
                                            @click="systemSettings.sessionTimeoutMinutes = min; $emit('showToast', `Timeout sesi disetel ke ${min} Menit`)"
                                            class="cursor-pointer py-2 text-xs font-bold rounded-xl border text-center transition-all"
                                            :class="systemSettings.sessionTimeoutMinutes === min ? 'bg-charcoal text-white border-charcoal shadow-xs' : 'bg-canvas text-subtext border-[#ECEAE4] hover:text-charcoal hover:bg-white'"
                                        >
                                            {{ min }} Menit
                                        </button>
                                    </div>
                                    <p class="text-[11px] text-subtext">Pengguna akan logout otomatis bila tidak ada aktivitas dalam durasi ini.</p>
                                </div>

                                <!-- Parameter 3: Mode Pemeliharaan -->
                                <div class="space-y-2 lg:col-span-2 p-4 rounded-2xl bg-canvas border border-[#ECEAE4]">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-xs font-bold text-charcoal">Mode Pemeliharaan Sistem (Maintenance Mode)</div>
                                            <div class="text-[11px] text-subtext mt-0.5">Kunci akses seluruh siswa, guru, dan staf saat pembaharuan sistem atau rekap nilai semester.</div>
                                        </div>
                                        <button
                                            type="button"
                                            @click="handleToggleMaintenance"
                                            class="cursor-pointer shrink-0 w-12 h-6 rounded-full p-0.5 transition-colors duration-200"
                                            :class="systemSettings.maintenanceMode ? 'bg-blossom-dark' : 'bg-gray-300'"
                                            :title="systemSettings.maintenanceMode ? 'Nonaktifkan Mode Pemeliharaan' : 'Aktifkan Mode Pemeliharaan'"
                                        >
                                            <div
                                                class="w-5 h-5 rounded-full bg-white shadow-md transform transition-transform duration-200"
                                                :class="systemSettings.maintenanceMode ? 'translate-x-6' : 'translate-x-0'"
                                            ></div>
                                        </button>
                                    </div>
                                    <div
                                        class="mt-3 p-3 rounded-xl text-xs font-medium border"
                                        :class="systemSettings.maintenanceMode ? 'bg-blossom-light text-blossom-dark border-[#F8B4B4]' : 'bg-white text-subtext border-[#ECEAE4]'"
                                    >
                                        {{ systemSettings.maintenanceMode ? '[PERINGATAN] Mode Pemeliharaan Aktif: Sistem dikunci sementara, hanya akun Administrator yang dapat login.' : 'LMS beroperasi normal untuk seluruh siswa, guru, wali kelas, dan kepala sekolah.' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Format Berkas Diizinkan -->
                            <div class="pt-4 border-t border-[#ECEAE4]">
                                <div class="text-xs font-bold text-charcoal mb-2">Format Ekstensi Berkas Pembelajaran Resmi:</div>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="ext in systemSettings.allowedExtensions"
                                        :key="ext"
                                        class="text-xs font-mono font-bold px-3 py-1 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal"
                                    >
                                        .{{ ext.toLowerCase() }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-subtext mt-2">Membatasi upload berkas berbahaya demi keamanan server.</p>
                            </div>

                            <!-- Cadangan Basis Data (Snapshot) -->
                            <div class="pt-4 border-t border-[#ECEAE4] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <div class="text-xs font-bold text-charcoal">Pencadangan Basis Data Otomatis &amp; Pemulihan:</div>
                                    <div class="text-[11px] text-subtext mt-0.5">
                                        Jadwal: {{ systemSettings.backupSchedule }} • Terakhir berhasil: <span class="font-bold text-charcoal">{{ systemSettings.lastBackupTime }}</span>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    :disabled="isBackingUp"
                                    @click="handleRunBackup"
                                    class="cursor-pointer px-4 py-2.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all shadow-xs inline-flex items-center gap-1.5 shrink-0 disabled:opacity-50"
                                >
                                    <LmsIcon name="download" className="w-3.5 h-3.5" />
                                    <span>{{ isBackingUp ? 'Mencadangkan...' : 'Cadangkan Sekarang' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODAL: BUAT / EDIT PENGUMUMAN                                -->
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
                        <h3 class="text-base font-extrabold text-charcoal">
                            {{ isEditingPengumuman ? 'Edit Pengumuman Sekolah' : 'Buat Pengumuman Sekolah' }}
                        </h3>
                        <button
                            type="button"
                            @click="isPengumumanModalOpen = false"
                            class="w-8 h-8 rounded-full bg-canvas text-charcoal flex items-center justify-center hover:bg-muted font-bold cursor-pointer"
                        >
                            <LmsIcon name="x" className="w-3.5 h-3.5" />
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
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
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
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
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
                            {{ isEditingPengumuman ? 'Simpan Perubahan' : 'Publikasikan' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- ============================================================ -->
        <!-- MODAL: TAMBAH / EDIT JADWAL PELAJARAN (DENGAN ANTI-BENTROK)   -->
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
                v-if="isJadwalModalOpen"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
                @click.self="isJadwalModalOpen = false"
            >
                <div class="bg-white w-full max-w-lg rounded-t-[32px] sm:rounded-[32px] p-5 sm:p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto no-scrollbar">
                    <div class="flex items-center justify-between pb-3 border-b border-[#ECEAE4]">
                        <div>
                            <h3 class="text-base font-extrabold text-charcoal">
                                {{ isEditingJadwal ? 'Edit Jadwal Pelajaran' : 'Plot Jadwal Pelajaran Baru' }}
                            </h3>
                            <p class="text-xs text-subtext mt-0.5">Tahun Ajaran: {{ jadwalForm.ta }}</p>
                        </div>
                        <button
                            type="button"
                            @click="isJadwalModalOpen = false"
                            class="w-8 h-8 rounded-full bg-canvas text-charcoal flex items-center justify-center hover:bg-muted font-bold cursor-pointer"
                        >
                            <LmsIcon name="x" className="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <!-- BENTROK ALERT (LIVE COLLISION DETECTION) -->
                    <div
                        v-if="currentConflict.conflict"
                        class="p-3.5 rounded-2xl bg-blossom-light border border-[#F8B4B4] text-blossom-dark space-y-1 animate-pulse"
                    >
                        <div class="flex items-center gap-2 font-black text-xs">
                            <svg class="w-4 h-4 text-blossom-dark shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" />
                            </svg>
                            <span>TERDETEKSI BENTROK JADWAL!</span>
                        </div>
                        <p class="text-xs font-semibold leading-relaxed">
                            {{ currentConflict.message }}
                        </p>
                        <div class="text-[11px] text-blossom-dark/80 pt-0.5">
                            *Ubah guru, kelas, hari, atau slot jam untuk menghindari tabrakan waktu KBM.
                        </div>
                    </div>

                    <div class="space-y-3 text-xs">
                        <!-- Rombel & Hari -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Target Rombel (Kelas)</label>
                                <select
                                    v-model="jadwalForm.rombel"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                >
                                    <option v-for="r in masterRombelList" :key="r.code" :value="r.name">
                                        {{ r.name }} ({{ r.jurusan }})
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Hari Belajar</label>
                                <select
                                    v-model="jadwalForm.hari"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                >
                                    <option v-for="h in ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']" :key="h" :value="h">
                                        {{ h }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Slot Jam Pelajaran -->
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Sesi / Slot Jam Pelajaran</label>
                            <select
                                v-model="jadwalForm.jamKe"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            >
                                <option v-for="s in slotJamPelajaran" :key="s.id" :value="s.label">
                                    {{ s.label }}
                                </option>
                            </select>
                        </div>

                        <!-- Mata Pelajaran -->
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Mata Pelajaran</label>
                            <select
                                v-model="jadwalForm.mapel"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            >
                                <option v-for="m in masterMapelList" :key="m.code" :value="m.name">
                                    [{{ m.code }}] {{ m.name }} • ({{ m.jurusan }})
                                </option>
                            </select>
                        </div>

                        <!-- Guru Pengampu -->
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Guru Pengampu</label>
                            <select
                                v-model="jadwalForm.guru"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            >
                                <option v-for="g in allGuruList" :key="g.nip" :value="g.name">
                                    {{ g.name }} — {{ g.bidang }}
                                </option>
                            </select>
                        </div>

                        <!-- Ruangan / Bengkel / Lab -->
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Ruangan / Bengkel / Laboratorium</label>
                            <select
                                v-model="jadwalForm.ruang"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            >
                                <option v-for="ruang in masterRuanganList" :key="ruang" :value="ruang">
                                    {{ ruang }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button
                            v-if="isEditingJadwal"
                            type="button"
                            @click="deleteJadwal(jadwalForm.id); isJadwalModalOpen = false"
                            class="px-3.5 py-2.5 rounded-xl bg-canvas border border-[#ECEAE4] text-[#D32F2F] hover:bg-blossom-light text-xs font-bold active:scale-95 transition-all cursor-pointer shadow-2xs"
                        >
                            Hapus
                        </button>
                        <button
                            type="button"
                            @click="isJadwalModalOpen = false"
                            class="flex-1 py-2.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-muted active:scale-95 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="saveJadwal"
                            :disabled="currentConflict.conflict"
                            class="flex-1 py-2.5 rounded-xl text-xs font-bold active:scale-95 transition-all cursor-pointer shadow-xs"
                            :class="currentConflict.conflict ? 'bg-muted text-subtext cursor-not-allowed' : 'bg-charcoal text-white hover:bg-black'"
                        >
                            {{ isEditingJadwal ? 'Simpan Perubahan' : 'Tetapkan Jadwal' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- ============================================================ -->
        <!-- MODAL: SALIN TEMPLATE JADWAL KE ROMBEL LAIN (CLONE SCHEDULE) -->
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
                v-if="isCloneModalOpen"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
                @click.self="isCloneModalOpen = false"
            >
                <div class="bg-white w-full max-w-md rounded-t-[32px] sm:rounded-[32px] p-5 sm:p-6 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[#ECEAE4]">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-amberpastel-light text-amberpastel-dark flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-base font-extrabold text-charcoal">Salin Template Jadwal</h3>
                                <p class="text-xs text-subtext mt-0.5">Duplikasi seluruh susunan mapel antar rombel paralel.</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="isCloneModalOpen = false"
                            class="w-8 h-8 rounded-full bg-canvas text-charcoal flex items-center justify-center hover:bg-muted font-bold cursor-pointer"
                        >
                            <LmsIcon name="x" className="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <div class="p-3 bg-sage-light text-sage-dark rounded-2xl text-xs space-y-1">
                        <div class="font-bold">Contoh Penggunaan:</div>
                        <p class="leading-relaxed">
                            Jika jadwal <strong>X TBSM A</strong> sudah selesai disusun, Anda bisa langsung menyalinnya ke <strong>X TBSM B</strong> tanpa perlu input satu per satu!
                        </p>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Rombel Sumber (Asal)</label>
                            <select
                                v-model="cloneForm.fromRombel"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            >
                                <option v-for="r in masterRombelList" :key="r.code" :value="r.name">
                                    {{ r.name }} ({{ r.jurusan }})
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Rombel Tujuan (Sasaran Duplikasi)</label>
                            <select
                                v-model="cloneForm.toRombel"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            >
                                <option v-for="r in masterRombelList" :key="r.code" :value="r.name">
                                    {{ r.name }} ({{ r.jurusan }})
                                </option>
                            </select>
                        </div>

                        <label class="flex items-center gap-2 cursor-pointer pt-1">
                            <input
                                v-model="cloneForm.keepGuru"
                                type="checkbox"
                                class="rounded border-[#ECEAE4] text-charcoal focus:ring-0"
                            />
                            <span class="text-xs text-charcoal font-medium">Gunakan guru pengampu yang sama dengan rombel asal</span>
                        </label>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button
                            type="button"
                            @click="isCloneModalOpen = false"
                            class="flex-1 py-2.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-muted active:scale-95 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="executeCloneJadwal"
                            class="flex-1 py-2.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                        >
                            Duplikasi Jadwal Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- ============================================================ -->
        <!-- MODAL: UBAH WALI KELAS                                        -->
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
                v-if="isWaliModalOpen"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
                @click.self="isWaliModalOpen = false"
            >
                <div class="bg-white w-full max-w-md rounded-t-[32px] sm:rounded-[32px] p-5 sm:p-6 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[#ECEAE4]">
                        <h3 class="text-base font-extrabold text-charcoal">Penetapan Wali Kelas</h3>
                        <button
                            type="button"
                            @click="isWaliModalOpen = false"
                            class="w-8 h-8 rounded-full bg-canvas text-charcoal flex items-center justify-center hover:bg-muted font-bold cursor-pointer"
                        >
                            <LmsIcon name="x" className="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-subtext mb-1">Rombel Target</label>
                            <input
                                :value="waliForm.rombel"
                                disabled
                                class="w-full bg-muted/60 border border-[#ECEAE4] rounded-2xl px-3 py-2 text-xs text-charcoal font-black"
                            />
                        </div>

                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Pilih Guru Pembina (Wali Kelas)</label>
                            <select
                                v-model="waliForm.waliName"
                                @change="waliForm.nip = allGuruList.find(g => g.name === waliForm.waliName)?.nip || ''"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            >
                                <option v-for="g in allGuruList" :key="g.nip" :value="g.name">
                                    {{ g.name }} — NIP: {{ g.nip }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Ruang Homebase Kelas</label>
                            <select
                                v-model="waliForm.ruang"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            >
                                <option v-for="r in masterRuanganList" :key="r" :value="r">
                                    {{ r }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button
                            type="button"
                            @click="isWaliModalOpen = false"
                            class="flex-1 py-2.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-muted active:scale-95 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="saveWali"
                            class="flex-1 py-2.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                        >
                            Simpan SK Wali
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- ============================================================ -->
        <!-- MODAL: TAMBAH PLOTTING GURU MAPEL                             -->
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
                v-if="isGuruMapelModalOpen"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
                @click.self="isGuruMapelModalOpen = false"
            >
                <div class="bg-white w-full max-w-md rounded-t-[32px] sm:rounded-[32px] p-5 sm:p-6 shadow-2xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[#ECEAE4]">
                        <h3 class="text-base font-extrabold text-charcoal">Plotting Pengampu Mata Pelajaran</h3>
                        <button
                            type="button"
                            @click="isGuruMapelModalOpen = false"
                            class="w-8 h-8 rounded-full bg-canvas text-charcoal flex items-center justify-center hover:bg-muted font-bold cursor-pointer"
                        >
                            <LmsIcon name="x" className="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Guru Pengampu</label>
                            <select
                                v-model="guruMapelForm.guruName"
                                @change="guruMapelForm.nip = allGuruList.find(g => g.name === guruMapelForm.guruName)?.nip || ''"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            >
                                <option v-for="g in allGuruList" :key="g.nip" :value="g.name">
                                    {{ g.name }} ({{ g.bidang }})
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Mata Pelajaran</label>
                            <select
                                v-model="guruMapelForm.mapel"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            >
                                <option v-for="m in masterMapelList" :key="m.code" :value="m.name">
                                    [{{ m.code }}] {{ m.name }} ({{ m.jurusan }})
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Target Rombel yang Diajar (Pilih minimal 1)</label>
                            <div class="grid grid-cols-2 gap-2 bg-canvas p-2.5 rounded-2xl border border-[#ECEAE4] max-h-36 overflow-y-auto no-scrollbar">
                                <label
                                    v-for="r in masterRombelList"
                                    :key="r.code"
                                    class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-white cursor-pointer"
                                >
                                    <input
                                        type="checkbox"
                                        :value="r.name"
                                        v-model="guruMapelForm.rombelList"
                                        class="rounded text-charcoal focus:ring-0"
                                    />
                                    <span class="text-xs font-bold text-charcoal">{{ r.name }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Total Jam / Minggu</label>
                                <input
                                    type="number"
                                    v-model="guruMapelForm.totalJam"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Ruangan Praktek</label>
                                <select
                                    v-model="guruMapelForm.ruang"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                >
                                    <option v-for="r in masterRuanganList" :key="r" :value="r">
                                        {{ r }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button
                            type="button"
                            @click="isGuruMapelModalOpen = false"
                            class="flex-1 py-2.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-muted active:scale-95 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="saveGuruMapel"
                            class="flex-1 py-2.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                        >
                            Simpan Penugasan
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- ============================================================ -->
        <!-- MODAL: TAMBAH / EDIT DATA PENGGUNA (CRUD AKUN)               -->
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

        <!-- ============================================================ -->
        <!-- MODAL: TAMBAH / EDIT DATA MASTER (JURUSAN, ROMBEL, MAPEL, TA) -->
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
                v-if="isMasterModalOpen"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
                @click.self="isMasterModalOpen = false"
            >
                <div class="bg-white w-full max-w-lg rounded-t-[32px] sm:rounded-[32px] p-5 sm:p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto no-scrollbar">
                    <div class="flex items-center justify-between pb-3 border-b border-[#ECEAE4]">
                        <div>
                            <h3 class="text-base font-extrabold text-charcoal">
                                {{ isEditingMaster ? 'Edit' : 'Tambah' }} Data Master:
                                <span class="capitalize">{{ masterTargetType === 'ta' ? 'Tahun Ajaran' : masterTargetType }}</span>
                            </h3>
                            <p class="text-xs text-subtext mt-0.5">Konfigurasi struktur akademik sekolah SMK SWJ.</p>
                        </div>
                        <button
                            type="button"
                            @click="isMasterModalOpen = false"
                            class="w-8 h-8 rounded-full bg-canvas text-charcoal flex items-center justify-center hover:bg-muted font-bold cursor-pointer"
                        >
                            <LmsIcon name="x" className="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <!-- FORM JURUSAN -->
                    <div v-if="masterTargetType === 'jurusan'" class="space-y-3 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Kode Jurusan</label>
                                <input
                                    v-model="masterItemForm.code"
                                    type="text"
                                    :disabled="isEditingMaster"
                                    placeholder="Contoh: TBSM / RPL"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold disabled:opacity-60"
                                />
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Jenjang Tingkat</label>
                                <input
                                    v-model="masterItemForm.tingkat"
                                    type="text"
                                    placeholder="3 Tingkat (X, XI, XII)"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Nama Program Keahlian (Jurusan)</label>
                            <input
                                v-model="masterItemForm.name"
                                type="text"
                                placeholder="Contoh: Teknik Bisnis Sepeda Motor"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            />
                        </div>
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Kepala Bengkel / Laboratorium</label>
                            <select
                                v-model="masterItemForm.kepalaLab"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            >
                                <option v-for="g in allGuruList" :key="g.nip" :value="g.name">
                                    {{ g.name }} ({{ g.bidang }})
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- FORM ROMBEL -->
                    <div v-else-if="masterTargetType === 'rombel'" class="space-y-3 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Nama Rombel</label>
                                <input
                                    v-model="masterItemForm.name"
                                    type="text"
                                    placeholder="Contoh: X TBSM C"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Program Keahlian</label>
                                <select
                                    v-model="masterItemForm.jurusan"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                >
                                    <option v-for="j in masterJurusanList" :key="j.code" :value="j.code">
                                        {{ j.name }} ({{ j.code }})
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Wali Kelas</label>
                            <select
                                v-model="masterItemForm.wali"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            >
                                <option v-for="g in allGuruList" :key="g.nip" :value="g.name">
                                    {{ g.name }} ({{ g.bidang }})
                                </option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Ruangan Homebase</label>
                                <select
                                    v-model="masterItemForm.ruang"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                >
                                    <option v-for="ruang in masterRuanganList" :key="ruang" :value="ruang">
                                        {{ ruang }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Kapasitas Maksimal</label>
                                <input
                                    v-model="masterItemForm.kapasitas"
                                    type="number"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- FORM MAPEL -->
                    <div v-else-if="masterTargetType === 'mapel'" class="space-y-3 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Kode Mapel</label>
                                <input
                                    v-model="masterItemForm.code"
                                    type="text"
                                    placeholder="Contoh: PMSM"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Kategori</label>
                                <select
                                    v-model="masterItemForm.kategori"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                >
                                    <option value="Kejuruan Produktif">Kejuruan Produktif</option>
                                    <option value="Umum & Normatif">Umum &amp; Normatif</option>
                                    <option value="Muatan Lokal">Muatan Lokal</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Nama Mata Pelajaran</label>
                            <input
                                v-model="masterItemForm.name"
                                type="text"
                                placeholder="Contoh: Pemeliharaan Mesin Sepeda Motor"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            />
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Sasaran</label>
                                <select
                                    v-model="masterItemForm.rombelTingkat"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                >
                                    <option value="Kelas X">Kelas X</option>
                                    <option value="Kelas XI">Kelas XI</option>
                                    <option value="Kelas XII">Kelas XII</option>
                                    <option value="Semua Tingkat">Semua Tingkat</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Beban Jam/Mgg</label>
                                <input
                                    v-model="masterItemForm.jamPerMinggu"
                                    type="number"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Standar KKM</label>
                                <input
                                    v-model="masterItemForm.kkm"
                                    type="number"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- FORM TAHUN AJARAN -->
                    <div v-else class="space-y-3 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Tahun Ajaran</label>
                                <input
                                    v-model="masterItemForm.tahun"
                                    type="text"
                                    placeholder="2027/2028"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Semester</label>
                                <select
                                    v-model="masterItemForm.semester"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                >
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Tanggal Mulai KBM</label>
                                <input
                                    v-model="masterItemForm.mulai"
                                    type="text"
                                    placeholder="12 Juli 2027"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Tanggal Selesai KBM</label>
                                <input
                                    v-model="masterItemForm.selesai"
                                    type="text"
                                    placeholder="18 Des 2027"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Total Hari Efektif KBM</label>
                            <input
                                v-model="masterItemForm.totalHariEfektif"
                                type="number"
                                placeholder="108"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            />
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button
                            type="button"
                            @click="isMasterModalOpen = false"
                            class="flex-1 py-2.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-muted active:scale-95 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="saveMasterItem"
                            class="flex-1 py-2.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                        >
                            {{ isEditingMaster ? 'Simpan Perubahan' : 'Tambahkan' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
