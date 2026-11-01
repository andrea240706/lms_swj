<script setup>
import { computed, ref } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import { masterJurusan } from './adminData';

const emit = defineEmits(['showToast']);

const masterJurusanList = ref(JSON.parse(JSON.stringify(masterJurusan)));

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

const isModalOpen = ref(false);
const isEditing = ref(false);
const jurusanForm = ref({
    code: '',
    name: '',
    tingkat: '3 Tingkat (X, XI, XII)',
    kepalaLab: 'Hendra Gunawan, S.T',
    rombelCount: 0,
    siswaCount: 0,
});

const openAddModal = () => {
    isEditing.value = false;
    jurusanForm.value = {
        code: '',
        name: '',
        tingkat: '3 Tingkat (X, XI, XII)',
        kepalaLab: allGuruList.value[0]?.name || '',
        rombelCount: 0,
        siswaCount: 0,
    };
    isModalOpen.value = true;
};

const openEditModal = (item) => {
    isEditing.value = true;
    jurusanForm.value = JSON.parse(JSON.stringify(item));
    isModalOpen.value = true;
};

const saveJurusan = () => {
    if (!jurusanForm.value.code.trim() || !jurusanForm.value.name.trim()) {
        emit('showToast', 'Lengkapi Kode dan Nama Jurusan.');
        return;
    }

    if (isEditing.value) {
        const idx = masterJurusanList.value.findIndex((j) => j.code === jurusanForm.value.code);
        if (idx !== -1) {
            masterJurusanList.value[idx] = { ...jurusanForm.value };
            emit('showToast', `Jurusan ${jurusanForm.value.name} berhasil diperbarui.`);
        }
    } else {
        const existing = masterJurusanList.value.find((j) => j.code.toLowerCase() === jurusanForm.value.code.toLowerCase());
        if (existing) {
            emit('showToast', 'Kode Jurusan sudah terdaftar.');
            return;
        }
        masterJurusanList.value.push({ ...jurusanForm.value });
        emit('showToast', `Program keahlian ${jurusanForm.value.name} (${jurusanForm.value.code}) berhasil ditambahkan.`);
    }
    isModalOpen.value = false;
};

const deleteJurusan = (code) => {
    masterJurusanList.value = masterJurusanList.value.filter((j) => j.code !== code);
    emit('showToast', `Jurusan ${code} berhasil dihapus.`);
};
</script>

<template>
    <div class="space-y-4">
        <!-- Top Bar -->
        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-extrabold text-charcoal">Data Master: Kompetensi Keahlian (Jurusan)</h3>
                <p class="text-xs text-subtext mt-0.5">Kelola program keahlian kejuruan, jenjang tingkat, dan kepala bengkel / laboratorium.</p>
            </div>

            <button
                type="button"
                @click="openAddModal"
                class="px-4 py-2 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs flex items-center gap-1.5 self-start sm:self-auto"
            >
                <span>+ Tambah Jurusan</span>
            </button>
        </div>

        <!-- Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                @click="openEditModal(j)"
                                class="px-2.5 py-1 rounded-lg bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                                title="Edit Jurusan"
                            >
                                Edit
                            </button>
                            <button
                                type="button"
                                @click="deleteJurusan(j.code)"
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

        <!-- MODAL: TAMBAH / EDIT JURUSAN -->
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isModalOpen"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
                @click.self="isModalOpen = false"
            >
                <div class="bg-white w-full max-w-lg rounded-t-[32px] sm:rounded-[32px] p-5 sm:p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto no-scrollbar">
                    <div class="flex items-center justify-between pb-3 border-b border-[#ECEAE4]">
                        <div>
                            <h3 class="text-base font-extrabold text-charcoal">
                                {{ isEditing ? 'Edit' : 'Tambah' }} Jurusan
                            </h3>
                            <p class="text-xs text-subtext mt-0.5">Konfigurasi program keahlian kejuruan.</p>
                        </div>
                        <button
                            type="button"
                            @click="isModalOpen = false"
                            class="w-8 h-8 rounded-full bg-canvas text-charcoal flex items-center justify-center hover:bg-muted font-bold cursor-pointer"
                        >
                            <LmsIcon name="x" className="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Kode Jurusan</label>
                                <input
                                    v-model="jurusanForm.code"
                                    type="text"
                                    :disabled="isEditing"
                                    placeholder="Contoh: TBSM / RPL"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold disabled:opacity-60"
                                />
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Jenjang Tingkat</label>
                                <input
                                    v-model="jurusanForm.tingkat"
                                    type="text"
                                    placeholder="3 Tingkat (X, XI, XII)"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Nama Program Keahlian (Jurusan)</label>
                            <input
                                v-model="jurusanForm.name"
                                type="text"
                                placeholder="Contoh: Teknik & Bisnis Sepeda Motor"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            />
                        </div>
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Kepala Bengkel / Laboratorium</label>
                            <select
                                v-model="jurusanForm.kepalaLab"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            >
                                <option v-for="g in allGuruList" :key="g.nip" :value="g.name">
                                    {{ g.name }} ({{ g.bidang }})
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button
                            type="button"
                            @click="isModalOpen = false"
                            class="flex-1 py-2.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-muted active:scale-95 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="saveJurusan"
                            class="flex-1 py-2.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                        >
                            {{ isEditing ? 'Simpan Perubahan' : 'Tambahkan' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
