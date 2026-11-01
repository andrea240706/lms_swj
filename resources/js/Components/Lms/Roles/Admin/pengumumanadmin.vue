<script setup>
import { ref } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import { initialPengumumanList } from './adminData';

const emit = defineEmits(['showToast']);

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
</script>

<template>
    <div class="space-y-4">
        <!-- Top Bar -->
        <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between">
            <div>
                <h3 class="text-sm font-extrabold text-charcoal">Papan Pengumuman &amp; Broadcast Sekolah</h3>
                <p class="text-xs text-subtext mt-0.5">Publikasikan informasi resmi ke akun Siswa, Guru, atau Wali Kelas.</p>
            </div>
            <button
                type="button"
                @click="openNewPengumumanModal"
                class="px-4 py-2 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all cursor-pointer shadow-xs flex items-center gap-1.5"
            >
                <span>+ Buat Pengumuman</span>
            </button>
        </div>

        <!-- Announcement Cards -->
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

        <!-- MODAL: BUAT / EDIT PENGUMUMAN -->
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
    </div>
</template>
