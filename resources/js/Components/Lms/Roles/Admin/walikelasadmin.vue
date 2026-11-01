<script setup>
import { computed, ref } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import { initialPlottingWali, masterRuanganList } from './adminData';

const emit = defineEmits(['showToast']);

const plottingWali = ref(JSON.parse(JSON.stringify(initialPlottingWali)));

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
    waliForm.value = {
        id: w.id,
        rombel: w.rombel,
        waliName: w.waliName,
        nip: w.nip,
        ruang: w.ruang,
    };
    isWaliModalOpen.value = true;
};

const saveWali = () => {
    const idx = plottingWali.value.findIndex((w) => w.id === waliForm.value.id);
    if (idx !== -1) {
        plottingWali.value[idx].waliName = waliForm.value.waliName;
        plottingWali.value[idx].nip = waliForm.value.nip;
        plottingWali.value[idx].ruang = waliForm.value.ruang;
        emit('showToast', `Wali Kelas untuk rombel ${waliForm.value.rombel} berhasil ditetapkan kepada ${waliForm.value.waliName}.`);
    }
    isWaliModalOpen.value = false;
};
</script>

<template>
    <div class="space-y-4">
        <!-- Hero Header -->
        <div class="hero-lavender rounded-[28px] py-7 px-6 sm:py-8 sm:px-8 soft-shadow relative overflow-hidden flex flex-col sm:flex-row sm:items-center justify-between gap-4 min-h-[115px]">
            <div>
                <h3 class="text-[20px] sm:text-[23px] font-black text-charcoal leading-tight">Status Penetapan Pembina Rombel (Wali Kelas)</h3>
                <p class="text-xs sm:text-[13px] text-charcoal/80 mt-1.5 max-w-xl leading-relaxed">Penetapan guru wali kelas per rombel aktif tahun ajaran berjalan.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3.5 py-1.5 rounded-full bg-white text-charcoal text-xs font-black shadow-2xs">
                    {{ plottingWali.length }} Rombel Terplot
                </span>
            </div>
        </div>

        <!-- Grid Cards -->
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

        <!-- MODAL: UBAH WALI KELAS -->
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
    </div>
</template>
