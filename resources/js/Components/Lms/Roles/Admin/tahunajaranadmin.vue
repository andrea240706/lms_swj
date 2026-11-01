<script setup>
import { ref } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import { masterTahunAjaran } from './adminData';

const emit = defineEmits(['showToast']);

const masterTahunAjaranList = ref(JSON.parse(JSON.stringify(masterTahunAjaran)));

const isTaModalOpen = ref(false);
const isEditingTa = ref(false);
const taForm = ref({
    id: null,
    tahun: '2027/2028',
    semester: 'Ganjil',
    status: 'Arsip',
    mulai: '12 Juli 2027',
    selesai: '18 Des 2027',
    totalHariEfektif: 108,
});

const openAddTaModal = () => {
    isEditingTa.value = false;
    taForm.value = {
        id: null,
        tahun: '2027/2028',
        semester: 'Ganjil',
        status: 'Arsip',
        mulai: '12 Juli 2027',
        selesai: '18 Des 2027',
        totalHariEfektif: 108,
    };
    isTaModalOpen.value = true;
};

const openEditTaModal = (ta) => {
    isEditingTa.value = true;
    taForm.value = JSON.parse(JSON.stringify(ta));
    isTaModalOpen.value = true;
};

const saveTa = () => {
    if (!taForm.value.tahun.trim() || !taForm.value.mulai.trim() || !taForm.value.selesai.trim()) {
        emit('showToast', 'Lengkapi seluruh data tahun ajaran.');
        return;
    }

    if (isEditingTa.value) {
        const idx = masterTahunAjaranList.value.findIndex((t) => t.id === taForm.value.id);
        if (idx !== -1) {
            masterTahunAjaranList.value[idx] = { ...taForm.value };
            emit('showToast', `Tahun ajaran ${taForm.value.tahun} berhasil diperbarui.`);
        }
    } else {
        const newTa = {
            ...taForm.value,
            id: Date.now(),
        };
        masterTahunAjaranList.value.push(newTa);
        emit('showToast', `Tahun ajaran baru ${newTa.tahun} (${newTa.semester}) berhasil ditambahkan.`);
    }
    isTaModalOpen.value = false;
};

const activateTahunAjaran = (ta) => {
    masterTahunAjaranList.value.forEach((t) => {
        t.status = t.id === ta.id ? 'Aktif' : 'Arsip';
    });
    emit('showToast', `Tahun ajaran ${ta.tahun} ${ta.semester} resmi diaktifkan sebagai periode KBM aktif.`);
};
</script>

<template>
    <div class="space-y-4">
        <!-- Header & Action Bar -->
        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-extrabold text-charcoal">Data Master: Tahun Ajaran &amp; Semester</h3>
                <p class="text-xs text-subtext mt-0.5">Atur kalender semester, periode aktif KBM, dan arsip tahun ajaran.</p>
            </div>

            <button
                type="button"
                @click="openAddTaModal"
                class="px-4 py-2 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs flex items-center gap-1.5 self-start sm:self-auto"
            >
                <span>+ Tambah Tahun Ajaran</span>
            </button>
        </div>

        <!-- List Tahun Ajaran Cards -->
        <div class="space-y-3">
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
                        @click="openEditTaModal(ta)"
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

        <!-- MODAL: TAMBAH / EDIT TAHUN AJARAN -->
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isTaModalOpen"
                class="fixed inset-0 bg-charcoal/50 backdrop-blur-xs z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
                @click.self="isTaModalOpen = false"
            >
                <div class="bg-white w-full max-w-lg rounded-t-[32px] sm:rounded-[32px] p-5 sm:p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto no-scrollbar">
                    <div class="flex items-center justify-between pb-3 border-b border-[#ECEAE4]">
                        <div>
                            <h3 class="text-base font-extrabold text-charcoal">
                                {{ isEditingTa ? 'Edit' : 'Tambah' }} Tahun Ajaran
                            </h3>
                            <p class="text-xs text-subtext mt-0.5">Konfigurasi rentang waktu dan semester tahun akademik.</p>
                        </div>
                        <button
                            type="button"
                            @click="isTaModalOpen = false"
                            class="w-8 h-8 rounded-full bg-canvas text-charcoal flex items-center justify-center hover:bg-muted font-bold cursor-pointer"
                        >
                            <LmsIcon name="x" className="w-3.5 h-3.5" />
                        </button>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Tahun Ajaran</label>
                                <input
                                    v-model="taForm.tahun"
                                    type="text"
                                    placeholder="2027/2028"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Semester</label>
                                <select
                                    v-model="taForm.semester"
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
                                    v-model="taForm.mulai"
                                    type="text"
                                    placeholder="12 Juli 2027"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                            <div>
                                <label class="block font-extrabold text-charcoal mb-1">Tanggal Selesai KBM</label>
                                <input
                                    v-model="taForm.selesai"
                                    type="text"
                                    placeholder="18 Des 2027"
                                    class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block font-extrabold text-charcoal mb-1">Total Hari Efektif KBM</label>
                            <input
                                v-model="taForm.totalHariEfektif"
                                type="number"
                                placeholder="108"
                                class="w-full bg-canvas border border-[#ECEAE4] rounded-2xl px-4 py-2.5 text-xs text-charcoal focus:outline-none focus:border-charcoal/40 font-bold"
                            />
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button
                            type="button"
                            @click="isTaModalOpen = false"
                            class="flex-1 py-2.5 rounded-xl bg-canvas border border-[#ECEAE4] text-xs font-bold text-charcoal hover:bg-muted active:scale-95 transition-all cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="saveTa"
                            class="flex-1 py-2.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs"
                        >
                            {{ isEditingTa ? 'Simpan Perubahan' : 'Tambahkan' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>
