<script setup>
import { computed, ref } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import {
    initialJadwalMingguan,
    masterMapel,
    masterRombel,
    masterRuanganList,
    masterTahunAjaran,
    slotJamPelajaran,
} from './adminData';

const emit = defineEmits(['showToast']);

const selectedTahunAjaran = ref('2026/2027 • Ganjil');
const jadwalViewMode = ref('rombel'); // 'rombel' | 'guru'
const selectedHariJadwal = ref('Senin');
const selectedRombelFilter = ref('X TBSM A'); // 'semua' | Rombel Name
const selectedGuruFilter = ref('Hendra Gunawan, S.T');

const jadwalMingguan = ref(JSON.parse(JSON.stringify(initialJadwalMingguan)));
const masterRombelList = ref(JSON.parse(JSON.stringify(masterRombel)));
const masterMapelList = ref(JSON.parse(JSON.stringify(masterMapel)));
const masterTahunAjaranList = ref(JSON.parse(JSON.stringify(masterTahunAjaran)));

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
            // 1. Bentrok Guru
            if (form.guru && item.guru === form.guru) {
                return {
                    conflict: true,
                    type: 'guru',
                    message: `BENTROK GURU: ${form.guru} sudah memiliki jadwal mengajar di kelas [${item.rombel}] pada ${form.hari}, ${form.jamKe}!`,
                };
            }
            // 2. Bentrok Rombel
            if (form.rombel && item.rombel === form.rombel) {
                return {
                    conflict: true,
                    type: 'rombel',
                    message: `BENTROK KELAS: Rombel [${form.rombel}] sudah memiliki jadwal [${item.mapel}] pada ${form.hari}, ${form.jamKe}!`,
                };
            }
            // 3. Bentrok Ruangan
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
    const conflict = detectConflict(jadwalForm.value, isEditingJadwal.value ? jadwalForm.value.id : null);
    if (conflict.conflict) {
        emit('showToast', conflict.message);
        return;
    }

    if (isEditingJadwal.value) {
        const idx = jadwalMingguan.value.findIndex((j) => j.id === jadwalForm.value.id);
        if (idx !== -1) {
            jadwalMingguan.value[idx] = { ...jadwalForm.value };
            emit('showToast', `Jadwal ${jadwalForm.value.mapel} (${jadwalForm.value.rombel}) berhasil diperbarui.`);
        }
    } else {
        const newJadwal = {
            ...jadwalForm.value,
            id: Date.now(),
        };
        jadwalMingguan.value.push(newJadwal);
        emit('showToast', `Jadwal baru ${newJadwal.mapel} untuk kelas ${newJadwal.rombel} berhasil diplot.`);
    }
    isJadwalModalOpen.value = false;
};

const deleteJadwal = (id) => {
    jadwalMingguan.value = jadwalMingguan.value.filter((j) => j.id !== id);
    emit('showToast', 'Jadwal pelajaran berhasil dihapus.');
};

// Modal: Salin Template Jadwal (Clone)
const isCloneModalOpen = ref(false);
const cloneForm = ref({
    fromRombel: 'X TBSM A',
    toRombel: 'X TBSM B',
    keepGuru: true,
});

const openCloneModal = () => {
    cloneForm.value = {
        fromRombel: selectedRombelFilter.value !== 'semua' ? selectedRombelFilter.value : masterRombel[0].name,
        toRombel: masterRombel[1]?.name || masterRombel[0].name,
        keepGuru: true,
    };
    isCloneModalOpen.value = true;
};

const executeCloneJadwal = () => {
    if (cloneForm.value.fromRombel === cloneForm.value.toRombel) {
        emit('showToast', 'Rombel tujuan tidak boleh sama dengan rombel sumber.');
        return;
    }

    const sourceJadwals = jadwalMingguan.value.filter((j) => j.rombel === cloneForm.value.fromRombel);
    if (sourceJadwals.length === 0) {
        emit('showToast', `Tidak ada jadwal di rombel sumber [${cloneForm.value.fromRombel}] untuk disalin.`);
        return;
    }

    jadwalMingguan.value = jadwalMingguan.value.filter((j) => j.rombel !== cloneForm.value.toRombel);

    sourceJadwals.forEach((sj, i) => {
        jadwalMingguan.value.push({
            ...sj,
            id: Date.now() + i,
            rombel: cloneForm.value.toRombel,
            ruang: cloneForm.value.toRombel.includes('TBSM') ? 'Bengkel TBSM 2' : 'Studio / Lab Paralel',
        });
    });

    isCloneModalOpen.value = false;
    selectedRombelFilter.value = cloneForm.value.toRombel;
    emit(
        'showToast',
        `Sukses menyalin ${sourceJadwals.length} sesi jadwal dari [${cloneForm.value.fromRombel}] ke [${cloneForm.value.toRombel}].`
    );
};
</script>

<template>
    <div class="space-y-4">
        <!-- Filter & Control Panel -->
        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow space-y-3.5">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-[#F2F0EC]">
                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Tahun Ajaran Switcher -->
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
                            <span>Jadwal per Rombel</span>
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

                    <button
                        type="button"
                        @click="$emit('showToast', 'Jadwal pelajaran resmi berhasil diekspor format Excel (.xlsx).')"
                        class="px-3.5 py-2 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal text-xs font-bold hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs flex items-center gap-1.5"
                        title="Ekspor Jadwal ke Excel"
                    >
                        <LmsIcon name="download" className="w-3.5 h-3.5" />
                        <span>Ekspor</span>
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

        <!-- Banner Info Jadwal -->
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

        <!-- MODAL: TAMBAH / EDIT JADWAL (DENGAN LIVE COLLISION DETECTION) -->
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

        <!-- MODAL: SALIN TEMPLATE JADWAL (CLONE) -->
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
    </div>
</template>
