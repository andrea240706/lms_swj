<script setup>
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import LmsSettingsModal from '@/Components/Lms/LmsSettingsModal.vue';

defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    sheetType: {
        type: String,
        default: '',
    },
    sheetData: {
        type: Object,
        default: () => ({}),
    },
    roles: {
        type: Object,
        default: () => ({}),
    },
    activeRole: {
        type: String,
        default: 'siswa',
    },
    currentUser: {
        type: Object,
        default: null,
    },
});

defineEmits([
    'close',
    'submitAssignment',
    'submitGrade',
    'selectRole',
    'openSettings',
    'logout',
]);
</script>

<template>
    <!-- Modal / Bottom Sheet Backdrop -->
    <div
        v-if="isOpen"
        @click="$emit('close')"
        class="fixed inset-0 bg-charcoal/40 backdrop-blur-xs flex items-end sm:items-center justify-center z-50 p-0 sm:p-4"
    >
        <div
            @click.stop
            class="w-full bg-white shadow-2xl transition-all overflow-hidden"
            :class="
                sheetType === 'settings'
                    ? 'max-w-lg sm:max-w-xl rounded-t-[32px] sm:rounded-[32px]'
                    : 'max-w-[400px] rounded-t-[32px] sm:rounded-[32px] p-6 max-h-[90vh] overflow-y-auto no-scrollbar'
            "
        >
            <!-- 0. SETTINGS MODAL -->
            <div v-if="sheetType === 'settings'">
                <LmsSettingsModal
                    :role="sheetData?.userName ? sheetData : (roles[activeRole] || {})"
                    :currentUser="currentUser"
                    @close="$emit('close')"
                    @logout="$emit('logout')"
                />
            </div>

            <!-- 1. LESSON VIEWER / DOWNLOAD -->
            <div v-else-if="sheetType === 'lesson'">
                <span
                    class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-blossom-light text-blossom-dark"
                >
                    {{ sheetData.subject }}
                </span>
                <h3 class="text-lg font-extrabold text-charcoal mt-2">
                    {{ sheetData.title }}
                </h3>
                <p class="text-xs text-subtext mt-1 leading-relaxed">
                    Modul ini membahas implementasi struktur Controller, Route Group berbasis Role Middleware, serta integrasi tampilan mobile-first.
                </p>
                <div
                    class="mt-4 p-3 rounded-2xl bg-canvas border border-[#ECEAE4] flex items-center justify-between"
                >
                    <span class="text-xs font-bold text-charcoal flex items-center gap-2">
                        <LmsIcon name="file" className="w-4 h-4 text-subtext shrink-0" />
                        <span>Modul_Praktikum_RPL.pdf (2.4 MB)</span>
                    </span>
                    <span class="text-xs font-extrabold text-charcoal underline cursor-pointer">Unduh</span>
                </div>
                <button
                    type="button"
                    @click="$emit('close')"
                    class="mt-4 w-full py-3 rounded-full bg-charcoal text-white text-xs font-bold cursor-pointer hover:opacity-90"
                >
                    Selesai Membaca
                </button>
            </div>

            <!-- 3. STUDENT ASSIGNMENT UPLOAD -->
            <div v-else-if="sheetType === 'upload_assignment'">
                <h3 class="text-lg font-extrabold text-charcoal">
                    Unggah Jawaban / Revisi Tugas
                </h3>
                <p class="text-xs text-subtext mt-1">
                    Format didukung: PDF, ZIP/RAR, atau Gambar (Maks 15 MB).
                </p>
                <div
                    class="mt-4 border-2 border-dashed border-[#D5D2C9] rounded-2xl p-5 text-center bg-canvas cursor-pointer hover:border-charcoal/40 transition-colors"
                >
                    <div class="text-xs font-bold text-charcoal">
                        Ketuk untuk memilih berkas dari HP
                    </div>
                    <div class="text-[11px] text-subtext mt-0.5 font-medium">
                        Tugas_NadiaPutri_XIIRPL1.zip (Siap kirim)
                    </div>
                </div>
                <button
                    type="button"
                    @click="$emit('submitAssignment', sheetData.assignmentId)"
                    class="mt-4 w-full py-3.5 rounded-full bg-charcoal text-white text-xs font-bold cursor-pointer hover:opacity-90 active:scale-[0.99] transition-all"
                >
                    Kirim Berkas Tugas
                </button>
            </div>

            <!-- 4. TEACHER GRADE MODAL -->
            <div v-else-if="sheetType === 'teacher_grade'">
                <h3 class="text-lg font-extrabold text-charcoal">
                    Penilaian Cepat: {{ sheetData.studentName }}
                </h3>
                <p class="text-xs text-subtext mt-0.5">
                    Berkas: Praktikum_API_{{ (sheetData.studentName || '').replace(/\s+/g, '') }}.zip
                </p>
                <div class="mt-3">
                    <label class="text-xs font-bold text-charcoal block mb-1">
                        Nilai Angka (0 - 100)
                    </label>
                    <input
                        type="number"
                        v-model="sheetData.score"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6E4DF] text-sm font-bold focus:outline-none focus:ring-1 focus:ring-charcoal"
                    />
                </div>
                <div class="mt-3">
                    <label class="text-xs font-bold text-charcoal block mb-1">
                        Catatan Feedback Guru
                    </label>
                    <input
                        type="text"
                        v-model="sheetData.feedback"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-[#E6E4DF] text-xs focus:outline-none focus:ring-1 focus:ring-charcoal"
                    />
                </div>
                <button
                    type="button"
                    @click="$emit('submitGrade', sheetData)"
                    class="mt-4 w-full py-3.5 rounded-full bg-charcoal text-white text-xs font-bold cursor-pointer hover:opacity-90 active:scale-[0.99] transition-all"
                >
                    Simpan Nilai &amp; Kirim Feedback
                </button>
            </div>

            <!-- 5. ROLE SWITCHER SHEET -->
            <div v-else-if="sheetType === 'role_switcher'">
                <h3 class="text-base font-extrabold text-charcoal mb-3">
                    Pindah Role Simulasi Cepat
                </h3>
                <div class="grid grid-cols-2 gap-2">
                    <button
                        v-for="r in Object.values(roles)"
                        :key="r.id"
                        type="button"
                        @click="$emit('selectRole', r.id)"
                        class="p-3 rounded-2xl border text-left cursor-pointer transition-all"
                        :class="
                            activeRole === r.id
                                ? 'bg-charcoal text-white border-charcoal shadow-xs'
                                : 'bg-canvas text-charcoal border-[#E6E4DF] hover:border-charcoal/40'
                        "
                    >
                        <div class="text-xs font-bold">{{ r.label }}</div>
                        <div class="text-[10px]" :class="activeRole === r.id ? 'opacity-75' : 'text-subtext'">
                            {{ r.badge }}
                        </div>
                    </button>
                </div>
            </div>

            <!-- 6. PROFILE & ACCOUNT OPTIONS -->
            <div v-else-if="sheetType === 'profile'">
                <div class="text-center">
                    <img
                        :src="sheetData.avatar"
                        :alt="sheetData.userName"
                        class="w-16 h-16 rounded-full mx-auto object-cover border-2 border-[#E6E4DF] shadow-xs"
                    />
                    <h3 class="text-base font-extrabold text-charcoal mt-2">
                        {{ sheetData.userName }}
                    </h3>
                    <p class="text-xs text-subtext mt-0.5">
                        {{ sheetData.userSub }}
                    </p>
                </div>

                <div class="mt-5 space-y-2">
                    <button
                        type="button"
                        @click="$emit('openSettings')"
                        class="w-full py-3 px-4 rounded-2xl bg-canvas border border-[#ECEAE4] text-charcoal font-bold text-xs hover:bg-white flex items-center justify-center gap-2 transition-colors cursor-pointer shadow-2xs"
                    >
                        <LmsIcon name="settings" className="w-4 h-4 text-charcoal" />
                        <span>Pengaturan Akun & Preferensi</span>
                    </button>
                    <button
                        type="button"
                        @click="$emit('logout')"
                        class="w-full py-3 rounded-2xl bg-rose-50 text-rose-700 font-bold text-xs hover:bg-rose-100 transition-colors cursor-pointer"
                    >
                        Keluar dari Akun (Logout)
                    </button>
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="w-full py-3 rounded-2xl bg-canvas border border-[#ECEAE4] text-charcoal font-bold text-xs hover:bg-muted transition-colors cursor-pointer"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
