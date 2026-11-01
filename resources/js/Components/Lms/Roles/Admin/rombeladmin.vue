<script setup>
import { computed, ref } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import { masterJurusan, masterRombel, masterRuanganList } from './adminData';

const emit = defineEmits(['showToast']);

const masterRombelList = ref(JSON.parse(JSON.stringify(masterRombel)));
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
const rombelForm = ref({
    code: '',
    name: '',
    jurusan: 'TBSM',
    tingkat: 'Kelas X',
    kapasitas: 36,
    terisi: 0,
    ruang: 'Bengkel TBSM 1',
    wali: 'Hendra Gunawan, S.T',
});

const openAddModal = () => {
    isEditing.value = false;
    rombelForm.value = {
        code: `RMB-${Date.now()}`,
        name: '',
        jurusan: masterJurusanList.value[0]?.code || 'TBSM',
        tingkat: 'Kelas X',
        kapasitas: 36,
        terisi: 0,
        ruang: masterRuanganList[0] || 'Ruang Teori 1',
        wali: allGuruList.value[0]?.name || '',
    };
    isModalOpen.value = true;
};

const openEditModal = (r) => {
    isEditing.value = true;
    rombelForm.value = JSON.parse(JSON.stringify(r));
    isModalOpen.value = true;
};

const saveRombel = () => {
    if (!rombelForm.value.name.trim()) {
        emit('showToast', 'Lengkapi Nama Rombel terlebih dahulu.');
        return;
    }

    if (isEditing.value) {
        const idx = masterRombelList.value.findIndex((r) => r.code === rombelForm.value.code);
        if (idx !== -1) {
            masterRombelList.value[idx] = { ...rombelForm.value };
            emit('showToast', `Rombel ${rombelForm.value.name} berhasil diperbarui.`);
        }
    } else {
        const newRombel = {
            ...rombelForm.value,
            code: rombelForm.value.name.replace(/\s+/g, '-'),
        };
        masterRombelList.value.push(newRombel);
        emit('showToast', `Rombel ${newRombel.name} berhasil ditambahkan.`);
    }
    isModalOpen.value = false;
};

const deleteRombel = (code) => {
    masterRombelList.value = masterRombelList.value.filter((r) => r.code !== code);
    emit('showToast', `Rombel ${code} berhasil dihapus.`);
};
</script>

<template>
    <div class="space-y-4">
        <!-- Top Bar -->
        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-extrabold text-charcoal">Data Master: Rombongan Belajar (Rombel / Kelas)</h3>
                <p class="text-xs text-subtext mt-0.5">Daftar kelas aktif, homebase ruangan, wali kelas, dan kapasitas siswa.</p>
            </div>

            <button
                type="button"
                @click="openAddModal"
                class="px-4 py-2 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs flex items-center gap-1.5 self-start sm:self-auto"
            >
                <span>+ Tambah Rombel</span>
            </button>
        </div>

        <!-- Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3.5">
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
                            @click="openEditModal(r)"
                            class="px-2.5 py-1 rounded-lg bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-white active:scale-95 transition-all cursor-pointer shadow-2xs"
                        >
                            Edit
                        </button>
                        <button
                            type="button"
                            @click="deleteRombel(r.code)"
                            class="px-2 py-1 rounded-lg bg-canvas border border-[#ECEAE4] text-[#D32F2F] hover:bg-blossom-light text-xs font-bold active:scale-95 transition-all cursor-pointer shadow-2xs"
                        >
                            <LmsIcon name="trash" className="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: TAMBAH / EDIT ROMBEL -->
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
                                {{ isEditing ? 'Edit' : 'Tambah' }} Rombongan Belajar (Rombel)
                            </h3>
                            <p class="text-xs text-subtext mt-0.5">Konfigurasi kelas dan pembagian kapasitas.</p>
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
                                <label class="block font-extrabold text-charcoal mb-1">Nama Rombel</label>
                                <input
                                    v-model="rombelForm.name"
                                    type="text"
                                    placeholder="Contoh: X TBSM C"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Program Keahlian</label>
                                <select
                                    v-model="rombelForm.jurusan"
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
                                v-model="rombelForm.wali"
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
                                    v-model="rombelForm.ruang"
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
                                    v-model="rombelForm.kapasitas"
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
                            @click="saveRombel"
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
