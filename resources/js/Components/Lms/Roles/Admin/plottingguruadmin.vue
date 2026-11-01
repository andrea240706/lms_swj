<script setup>
import { computed, ref } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import {
    initialPlottingGuruMapel,
    masterMapel,
    masterRombel,
    masterRuanganList,
} from './adminData';

const emit = defineEmits(['showToast']);

const plottingGuruMapel = ref(JSON.parse(JSON.stringify(initialPlottingGuruMapel)));
const masterMapelList = ref(JSON.parse(JSON.stringify(masterMapel)));
const masterRombelList = ref(JSON.parse(JSON.stringify(masterRombel)));

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

// Modal: Tambah Plotting Guru Mapel
const isGuruMapelModalOpen = ref(false);
const guruMapelForm = ref({
    guruName: 'Hendra Gunawan, S.T',
    nip: '19850314 201101 1 004',
    mapel: 'Pemeliharaan Mesin Sepeda Motor',
    rombelList: ['X TBSM A'],
    totalJam: 12,
    ruang: 'Bengkel TBSM 1',
});

const openAddGuruMapelModal = () => {
    guruMapelForm.value = {
        guruName: allGuruList.value[0].name,
        nip: allGuruList.value[0].nip,
        mapel: masterMapelList.value[0].name,
        rombelList: [masterRombelList.value[0].name],
        totalJam: 12,
        ruang: masterRuanganList[0],
    };
    isGuruMapelModalOpen.value = true;
};

const saveGuruMapel = () => {
    if (!guruMapelForm.value.rombelList || guruMapelForm.value.rombelList.length === 0) {
        emit('showToast', 'Pilih minimal satu rombel kelas.');
        return;
    }

    const newPlot = {
        id: Date.now(),
        guruName: guruMapelForm.value.guruName,
        nip: guruMapelForm.value.nip,
        mapel: guruMapelForm.value.mapel,
        rombelList: [...guruMapelForm.value.rombelList],
        totalJam: Number(guruMapelForm.value.totalJam),
        ruang: guruMapelForm.value.ruang,
    };

    plottingGuruMapel.value.unshift(newPlot);
    isGuruMapelModalOpen.value = false;
    emit('showToast', `Plotting pengampu ${guruMapelForm.value.mapel} untuk ${guruMapelForm.value.guruName} berhasil disimpan.`);
};
</script>

<template>
    <div class="space-y-4">
        <!-- Header -->
        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-extrabold text-charcoal">Distribusi Pengampu Mata Pelajaran (Plotting Guru Mapel)</h3>
                <p class="text-xs text-subtext mt-0.5">Menentukan guru mata pelajaran untuk tiap rombel &amp; total beban jam mengajar mingguan.</p>
            </div>
            <button
                type="button"
                @click="openAddGuruMapelModal"
                class="px-4 py-2 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs shrink-0 flex items-center gap-1.5 self-start sm:self-auto"
            >
                <span>+ Plot Guru Mapel</span>
            </button>
        </div>

        <!-- List Cards -->
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
                        @click="$emit('showToast', `Status plotting untuk ${g.guruName} valid.`)"
                        class="px-3 py-1.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:opacity-90 active:scale-95 transition-all cursor-pointer shadow-2xs"
                    >
                        Validasi
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: TAMBAH PLOTTING GURU MAPEL -->
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
    </div>
</template>
