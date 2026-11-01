<script setup>
import { ref } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import { masterMapel } from './adminData';

const emit = defineEmits(['showToast']);

const masterMapelList = ref(JSON.parse(JSON.stringify(masterMapel)));

const isModalOpen = ref(false);
const isEditing = ref(false);
const mapelForm = ref({
    code: '',
    name: '',
    jurusan: 'Umum',
    kategori: 'Kejuruan Produktif',
    rombelTingkat: 'Kelas X',
    jamPerMinggu: 4,
    kkm: 75,
});

const openAddModal = () => {
    isEditing.value = false;
    mapelForm.value = {
        code: '',
        name: '',
        jurusan: 'Umum',
        kategori: 'Kejuruan Produktif',
        rombelTingkat: 'Kelas X',
        jamPerMinggu: 4,
        kkm: 75,
    };
    isModalOpen.value = true;
};

const openEditModal = (m) => {
    isEditing.value = true;
    mapelForm.value = JSON.parse(JSON.stringify(m));
    isModalOpen.value = true;
};

const saveMapel = () => {
    if (!mapelForm.value.code.trim() || !mapelForm.value.name.trim()) {
        emit('showToast', 'Lengkapi Kode dan Nama Mata Pelajaran.');
        return;
    }

    if (isEditing.value) {
        const idx = masterMapelList.value.findIndex((m) => m.code === mapelForm.value.code);
        if (idx !== -1) {
            masterMapelList.value[idx] = { ...mapelForm.value };
            emit('showToast', `Mata pelajaran ${mapelForm.value.name} berhasil diperbarui.`);
        }
    } else {
        masterMapelList.value.push({ ...mapelForm.value });
        emit('showToast', `Mata pelajaran baru ${mapelForm.value.name} (${mapelForm.value.code}) berhasil ditambahkan.`);
    }
    isModalOpen.value = false;
};

const deleteMapel = (code) => {
    masterMapelList.value = masterMapelList.value.filter((m) => m.code !== code);
    emit('showToast', `Mata pelajaran ${code} berhasil dihapus.`);
};
</script>

<template>
    <div class="space-y-4">
        <!-- Top Bar -->
        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-extrabold text-charcoal">Data Master: Mata Pelajaran Kurikulum</h3>
                <p class="text-xs text-subtext mt-0.5">Kelola muatan produktif kejuruan, normatif umum, beban jam mengajar mingguan, dan standar KKM.</p>
            </div>

            <button
                type="button"
                @click="openAddModal"
                class="px-4 py-2 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs flex items-center gap-1.5 self-start sm:self-auto"
            >
                <span>+ Tambah Mata Pelajaran</span>
            </button>
        </div>

        <!-- List Cards -->
        <div class="space-y-2.5">
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
                        @click="openEditModal(m)"
                        class="px-3 py-1 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                    >
                        Edit
                    </button>
                    <button
                        type="button"
                        @click="deleteMapel(m.code)"
                        class="px-2 py-1 rounded-xl bg-canvas border border-[#ECEAE4] text-[#D32F2F] hover:bg-blossom-light text-xs font-bold active:scale-95 transition-all cursor-pointer shadow-2xs"
                    >
                        <LmsIcon name="trash" className="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: TAMBAH / EDIT MAPEL -->
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
                                {{ isEditing ? 'Edit' : 'Tambah' }} Mata Pelajaran
                            </h3>
                            <p class="text-xs text-subtext mt-0.5">Konfigurasi struktur kurikulum dan bobot penilaian.</p>
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
                                <label class="block font-extrabold text-charcoal mb-1">Kode Mapel</label>
                                <input
                                    v-model="mapelForm.code"
                                    type="text"
                                    placeholder="Contoh: PMSM"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Kategori</label>
                                <select
                                    v-model="mapelForm.kategori"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-3 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                >
                                    <option value="Kejuruan Produktif">Kejuruan Produktif</option>
                                    <option value="Muatan Umum">Muatan Umum</option>
                                    <option value="Muatan Lokal">Muatan Lokal</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Nama Mata Pelajaran</label>
                            <input
                                v-model="mapelForm.name"
                                type="text"
                                placeholder="Contoh: Pemeliharaan Mesin Sepeda Motor"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            />
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Sasaran</label>
                                <select
                                    v-model="mapelForm.rombelTingkat"
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
                                    v-model="mapelForm.jamPerMinggu"
                                    type="number"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Standar KKM</label>
                                <input
                                    v-model="mapelForm.kkm"
                                    type="number"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
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
                            @click="saveMapel"
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
