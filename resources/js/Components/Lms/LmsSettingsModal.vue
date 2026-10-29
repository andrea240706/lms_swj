<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    currentUser: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close', 'logout']);

// Active Tab inside Settings
const activeTab = ref('keamanan'); // 'keamanan' | 'notifikasi' | 'tampilan' | 'sesi'

// ==========================================
// 1. KEAMANAN & KATA SANDI
// ==========================================
const currentPassword = ref('');
const newPassword = ref('');
const confirmPassword = ref('');
const showCurrent = ref(false);
const showNew = ref(false);
const showConfirm = ref(false);
const passwordLoading = ref(false);
const passwordSuccess = ref(false);
const passwordError = ref('');

// Password strength calculation
const passwordStrength = computed(() => {
    const p = newPassword.value;
    if (!p) return { score: 0, label: 'Belum diisi', color: 'bg-neutral-200', text: 'text-subtext' };
    
    let score = 0;
    if (p.length >= 8) score++;
    if (/[A-Z]/.test(p) && /[a-z]/.test(p)) score++;
    if (/[0-9]/.test(p)) score++;
    if (/[^A-Za-z0-9]/.test(p)) score++;

    if (score <= 1) return { score: 1, label: 'Lemah', color: 'bg-rose-500', text: 'text-rose-600' };
    if (score === 2) return { score: 2, label: 'Cukup', color: 'bg-amber-500', text: 'text-amber-600' };
    if (score === 3) return { score: 3, label: 'Kuat', color: 'bg-emerald-500', text: 'text-emerald-600' };
    return { score: 4, label: 'Sangat Kuat', color: 'bg-emerald-600', text: 'text-emerald-700' };
});

const handleSavePassword = () => {
    passwordError.value = '';
    passwordSuccess.value = false;

    if (!currentPassword.value) {
        passwordError.value = 'Kata sandi saat ini harus diisi.';
        return;
    }
    if (newPassword.value.length < 8) {
        passwordError.value = 'Kata sandi baru minimal 8 karakter.';
        return;
    }
    if (newPassword.value !== confirmPassword.value) {
        passwordError.value = 'Konfirmasi kata sandi baru tidak cocok.';
        return;
    }

    passwordLoading.value = true;

    // Check if real authenticated session exists
    if (props.currentUser) {
        router.put(
            route('password.update'),
            {
                current_password: currentPassword.value,
                password: newPassword.value,
                password_confirmation: confirmPassword.value,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    passwordLoading.value = false;
                    passwordSuccess.value = true;
                    currentPassword.value = '';
                    newPassword.value = '';
                    confirmPassword.value = '';
                    setTimeout(() => {
                        passwordSuccess.value = false;
                    }, 4000);
                },
                onError: (errors) => {
                    passwordLoading.value = false;
                    passwordError.value =
                        errors.current_password ||
                        errors.password ||
                        'Terjadi kesalahan saat memperbarui kata sandi.';
                },
            }
        );
    } else {
        // Simulated responsive update for preview mode
        setTimeout(() => {
            passwordLoading.value = false;
            passwordSuccess.value = true;
            currentPassword.value = '';
            newPassword.value = '';
            confirmPassword.value = '';
            setTimeout(() => {
                passwordSuccess.value = false;
            }, 4000);
        }, 600);
    }
};

// ==========================================
// 2. PREFERENSI NOTIFIKASI
// ==========================================
const notifSettings = ref({
    tugas: true,
    deadline: true,
    nilai: true,
    pengumuman: true,
});
const notifSavedNotice = ref(false);

const toggleNotif = (key) => {
    notifSettings.value[key] = !notifSettings.value[key];
    try {
        localStorage.setItem(
            'lms_prefs_notifications',
            JSON.stringify(notifSettings.value)
        );
        notifSavedNotice.value = true;
        setTimeout(() => {
            notifSavedNotice.value = false;
        }, 2000);
    } catch {
        // localStorage fallback
    }
};

// ==========================================
// 3. TAMPILAN & PENGHEMAT KUOTA
// ==========================================
const fontSize = ref('standar'); // 'standar' | 'sedang' | 'besar'
const dataSaver = ref(false);
const smoothAnimation = ref(true);
const displaySavedNotice = ref(false);

const applyFontSize = (size) => {
    fontSize.value = size;
    try {
        localStorage.setItem('lms_prefs_font_size', size);
        if (size === 'sedang') {
            document.documentElement.style.fontSize = '17px';
        } else if (size === 'besar') {
            document.documentElement.style.fontSize = '18px';
        } else {
            document.documentElement.style.fontSize = '16px';
        }
        displaySavedNotice.value = true;
        setTimeout(() => {
            displaySavedNotice.value = false;
        }, 2000);
    } catch {
        // fallback
    }
};

const toggleDataSaver = () => {
    dataSaver.value = !dataSaver.value;
    try {
        localStorage.setItem('lms_prefs_data_saver', String(dataSaver.value));
        displaySavedNotice.value = true;
        setTimeout(() => {
            displaySavedNotice.value = false;
        }, 2000);
    } catch {
        // fallback
    }
};

const toggleSmoothAnimation = () => {
    smoothAnimation.value = !smoothAnimation.value;
    try {
        localStorage.setItem(
            'lms_prefs_smooth_animation',
            String(smoothAnimation.value)
        );
        displaySavedNotice.value = true;
        setTimeout(() => {
            displaySavedNotice.value = false;
        }, 2000);
    } catch {
        // fallback
    }
};

// Load preferences from localStorage on mount
onMounted(() => {
    try {
        const savedNotif = localStorage.getItem('lms_prefs_notifications');
        if (savedNotif) {
            notifSettings.value = { ...notifSettings.value, ...JSON.parse(savedNotif) };
        }
        const savedFontSize = localStorage.getItem('lms_prefs_font_size');
        if (savedFontSize) {
            fontSize.value = savedFontSize;
            applyFontSize(savedFontSize);
        }
        const savedDataSaver = localStorage.getItem('lms_prefs_data_saver');
        if (savedDataSaver !== null) {
            dataSaver.value = savedDataSaver === 'true';
        }
        const savedAnimation = localStorage.getItem('lms_prefs_smooth_animation');
        if (savedAnimation !== null) {
            smoothAnimation.value = savedAnimation === 'true';
        }
    } catch {
        // fallback ignore
    }
});
</script>

<template>
    <div class="flex flex-col h-full max-h-[85vh] w-full text-charcoal">
        <!-- Modal Top Bar Header -->
        <div class="px-6 py-4.5 border-b border-[#ECEAE4] flex items-center justify-between shrink-0 bg-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-canvas border border-[#ECEAE4] flex items-center justify-center text-charcoal shrink-0">
                    <LmsIcon name="settings" className="w-5 h-5 text-charcoal" />
                </div>
                <div class="flex flex-col">
                    <h2 class="text-base sm:text-lg font-black text-charcoal leading-tight">
                        Pengaturan LMS
                    </h2>
                    <span class="text-xs font-semibold text-subtext">
                        {{ role.label }} • {{ role.userName }}
                    </span>
                </div>
            </div>

            <!-- Close Button -->
            <button
                type="button"
                @click="$emit('close')"
                class="w-9 h-9 rounded-full bg-canvas hover:bg-black/5 flex items-center justify-center text-subtext hover:text-charcoal transition-all cursor-pointer"
                title="Tutup Pengaturan"
            >
                <LmsIcon name="x" className="w-5 h-5" />
            </button>
        </div>

        <!-- Navigation Tabs Bar -->
        <div class="px-6 pt-3 pb-2 border-b border-[#ECEAE4]/80 flex items-center gap-1.5 overflow-x-auto no-scrollbar shrink-0 bg-canvas/30">
            <button
                type="button"
                @click="activeTab = 'keamanan'"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 shrink-0"
                :class="
                    activeTab === 'keamanan'
                        ? 'bg-charcoal text-white shadow-xs'
                        : 'text-subtext hover:text-charcoal hover:bg-white'
                "
            >
                <LmsIcon name="shield" className="w-4 h-4" />
                <span>Keamanan</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'notifikasi'"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 shrink-0"
                :class="
                    activeTab === 'notifikasi'
                        ? 'bg-charcoal text-white shadow-xs'
                        : 'text-subtext hover:text-charcoal hover:bg-white'
                "
            >
                <LmsIcon name="bell" className="w-4 h-4" />
                <span>Notifikasi</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'tampilan'"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 shrink-0"
                :class="
                    activeTab === 'tampilan'
                        ? 'bg-charcoal text-white shadow-xs'
                        : 'text-subtext hover:text-charcoal hover:bg-white'
                "
            >
                <LmsIcon name="book" className="w-4 h-4" />
                <span>Tampilan</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'sesi'"
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2 shrink-0"
                :class="
                    activeTab === 'sesi'
                        ? 'bg-charcoal text-white shadow-xs'
                        : 'text-subtext hover:text-charcoal hover:bg-white'
                "
            >
                <LmsIcon name="smartphone" className="w-4 h-4" />
                <span>Sesi & Akun</span>
            </button>
        </div>

        <!-- Scrollable Tab Content Body -->
        <div class="flex-1 overflow-y-auto no-scrollbar p-6 space-y-6">
            <!-- ======================================================== -->
            <!-- TAB 1: KEAMANAN & KATA SANDI                             -->
            <!-- ======================================================== -->
            <div v-if="activeTab === 'keamanan'" class="space-y-5">
                <div>
                    <h3 class="text-sm font-extrabold text-charcoal">
                        Perbarui Kata Sandi Akun
                    </h3>
                    <p class="text-xs text-subtext mt-0.5 leading-relaxed">
                        Gunakan kombinasi minimal 8 karakter dengan huruf kapital, huruf kecil, dan angka agar akun Anda tetap aman.
                    </p>
                </div>

                <!-- Success Alert -->
                <div
                    v-if="passwordSuccess"
                    class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-emerald-800 text-xs font-bold animate-fadeIn"
                >
                    <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                        <LmsIcon name="check" className="w-3.5 h-3.5" />
                    </div>
                    <span>Kata sandi Anda berhasil diperbarui!</span>
                </div>

                <!-- Error Alert -->
                <div
                    v-if="passwordError"
                    class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 flex items-center gap-3 text-rose-800 text-xs font-bold animate-fadeIn"
                >
                    <div class="w-6 h-6 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0">
                        <LmsIcon name="x" className="w-3.5 h-3.5" />
                    </div>
                    <span>{{ passwordError }}</span>
                </div>

                <!-- Form Fields -->
                <form @submit.prevent="handleSavePassword" class="space-y-4">
                    <!-- Current Password -->
                    <div>
                        <label class="block text-xs font-bold text-charcoal mb-1.5">
                            Kata Sandi Saat Ini
                        </label>
                        <div class="relative">
                            <input
                                :type="showCurrent ? 'text' : 'password'"
                                v-model="currentPassword"
                                placeholder="Masukkan kata sandi lama"
                                class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-[#ECEAE4] bg-canvas/40 text-xs font-medium text-charcoal focus:bg-white focus:outline-none focus:ring-1 focus:ring-charcoal"
                                required
                            />
                            <button
                                type="button"
                                @click="showCurrent = !showCurrent"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-subtext hover:text-charcoal cursor-pointer"
                            >
                                <LmsIcon :name="showCurrent ? 'eyeOff' : 'eye'" className="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-charcoal">
                                Kata Sandi Baru
                            </label>
                            <span v-if="newPassword" class="text-[11px] font-bold" :class="passwordStrength.text">
                                Kekuatan: {{ passwordStrength.label }}
                            </span>
                        </div>
                        <div class="relative">
                            <input
                                :type="showNew ? 'text' : 'password'"
                                v-model="newPassword"
                                placeholder="Minimal 8 karakter"
                                class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-[#ECEAE4] bg-canvas/40 text-xs font-medium text-charcoal focus:bg-white focus:outline-none focus:ring-1 focus:ring-charcoal"
                                required
                            />
                            <button
                                type="button"
                                @click="showNew = !showNew"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-subtext hover:text-charcoal cursor-pointer"
                            >
                                <LmsIcon :name="showNew ? 'eyeOff' : 'eye'" className="w-4 h-4" />
                            </button>
                        </div>

                        <!-- Strength meter bar -->
                        <div class="grid grid-cols-4 gap-1.5 mt-2">
                            <div
                                v-for="i in 4"
                                :key="i"
                                class="h-1.5 rounded-full transition-all duration-300"
                                :class="i <= passwordStrength.score ? passwordStrength.color : 'bg-canvas'"
                            ></div>
                        </div>
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <label class="block text-xs font-bold text-charcoal mb-1.5">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <div class="relative">
                            <input
                                :type="showConfirm ? 'text' : 'password'"
                                v-model="confirmPassword"
                                placeholder="Ulangi kata sandi baru"
                                class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-[#ECEAE4] bg-canvas/40 text-xs font-medium text-charcoal focus:bg-white focus:outline-none focus:ring-1 focus:ring-charcoal"
                                required
                            />
                            <button
                                type="button"
                                @click="showConfirm = !showConfirm"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-subtext hover:text-charcoal cursor-pointer"
                            >
                                <LmsIcon :name="showConfirm ? 'eyeOff' : 'eye'" className="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="passwordLoading"
                            class="w-full py-3 rounded-2xl bg-charcoal text-white text-xs font-bold hover:bg-black transition-all cursor-pointer flex items-center justify-center gap-2 active:scale-[0.99] disabled:opacity-50"
                        >
                            <span v-if="passwordLoading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            <span>{{ passwordLoading ? 'Menyimpan...' : 'Perbarui Kata Sandi' }}</span>
                        </button>
                    </div>
                </form>

                <!-- Security Info Note -->
                <div class="p-3.5 rounded-2xl bg-canvas border border-[#ECEAE4] flex items-start gap-3">
                    <LmsIcon name="shield" className="w-4 h-4 text-subtext shrink-0 mt-0.5" />
                    <p class="text-[11px] text-subtext leading-relaxed font-medium">
                        Akun dilindungi enkripsi standar Laravel Bcrypt. Jangan pernah membagikan kata sandi Anda kepada teman atau pihak lain.
                    </p>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- TAB 2: PREFERENSI NOTIFIKASI                             -->
            <!-- ======================================================== -->
            <div v-else-if="activeTab === 'notifikasi'" class="space-y-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-charcoal">
                            Preferensi Notifikasi
                        </h3>
                        <p class="text-xs text-subtext mt-0.5">
                            Atur notifikasi yang ingin Anda terima di portal LMS.
                        </p>
                    </div>
                    <span
                        v-if="notifSavedNotice"
                        class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 animate-fadeIn"
                    >
                        Tersimpan
                    </span>
                </div>

                <div class="space-y-3">
                    <!-- Switch 1: Tugas & Modul Baru -->
                    <div class="p-3.5 rounded-2xl bg-canvas/50 border border-[#ECEAE4] flex items-center justify-between gap-4">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-charcoal">
                                Modul & Tugas Pembelajaran Baru
                            </span>
                            <span class="text-[11px] text-subtext mt-0.5">
                                Dapatkan pemberitahuan setiap ada rilis modul materi atau tugas di kelas.
                            </span>
                        </div>
                        <button
                            type="button"
                            @click="toggleNotif('tugas')"
                            class="w-11 h-6 rounded-full transition-colors cursor-pointer relative shrink-0 p-0.5"
                            :class="notifSettings.tugas ? 'bg-charcoal' : 'bg-neutral-300'"
                        >
                            <span
                                class="block w-5 h-5 rounded-full bg-white shadow-xs transition-transform duration-200"
                                :class="notifSettings.tugas ? 'translate-x-5' : 'translate-x-0'"
                            ></span>
                        </button>
                    </div>

                    <!-- Switch 2: Pengingat Deadline H-1 -->
                    <div class="p-3.5 rounded-2xl bg-canvas/50 border border-[#ECEAE4] flex items-center justify-between gap-4">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-charcoal">
                                Pengingat Tenggat Waktu (H-1)
                            </span>
                            <span class="text-[11px] text-subtext mt-0.5">
                                Notifikasi 24 jam sebelum batas waktu pengumpulan tugas atau agenda evaluasi.
                            </span>
                        </div>
                        <button
                            type="button"
                            @click="toggleNotif('deadline')"
                            class="w-11 h-6 rounded-full transition-colors cursor-pointer relative shrink-0 p-0.5"
                            :class="notifSettings.deadline ? 'bg-charcoal' : 'bg-neutral-300'"
                        >
                            <span
                                class="block w-5 h-5 rounded-full bg-white shadow-xs transition-transform duration-200"
                                :class="notifSettings.deadline ? 'translate-x-5' : 'translate-x-0'"
                            ></span>
                        </button>
                    </div>

                    <!-- Switch 3: Penilaian & Feedback Guru -->
                    <div class="p-3.5 rounded-2xl bg-canvas/50 border border-[#ECEAE4] flex items-center justify-between gap-4">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-charcoal">
                                Hasil Penilaian & Feedback
                            </span>
                            <span class="text-[11px] text-subtext mt-0.5">
                                Pemberitahuan ketika tugas Anda sudah dinilai atau diberi catatan perbaikan.
                            </span>
                        </div>
                        <button
                            type="button"
                            @click="toggleNotif('nilai')"
                            class="w-11 h-6 rounded-full transition-colors cursor-pointer relative shrink-0 p-0.5"
                            :class="notifSettings.nilai ? 'bg-charcoal' : 'bg-neutral-300'"
                        >
                            <span
                                class="block w-5 h-5 rounded-full bg-white shadow-xs transition-transform duration-200"
                                :class="notifSettings.nilai ? 'translate-x-5' : 'translate-x-0'"
                            ></span>
                        </button>
                    </div>

                    <!-- Switch 4: Pengumuman Sekolah -->
                    <div class="p-3.5 rounded-2xl bg-canvas/50 border border-[#ECEAE4] flex items-center justify-between gap-4">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-charcoal">
                                Pengumuman Resmi Akademik
                            </span>
                            <span class="text-[11px] text-subtext mt-0.5">
                                Informasi penting dari Kepala Sekolah, Kurikulum, atau Admin Operator.
                            </span>
                        </div>
                        <button
                            type="button"
                            @click="toggleNotif('pengumuman')"
                            class="w-11 h-6 rounded-full transition-colors cursor-pointer relative shrink-0 p-0.5"
                            :class="notifSettings.pengumuman ? 'bg-charcoal' : 'bg-neutral-300'"
                        >
                            <span
                                class="block w-5 h-5 rounded-full bg-white shadow-xs transition-transform duration-200"
                                :class="notifSettings.pengumuman ? 'translate-x-5' : 'translate-x-0'"
                            ></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- TAB 3: TAMPILAN & AKSESIBILITAS                          -->
            <!-- ======================================================== -->
            <div v-else-if="activeTab === 'tampilan'" class="space-y-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-extrabold text-charcoal">
                            Tampilan & Aksesibilitas
                        </h3>
                        <p class="text-xs text-subtext mt-0.5">
                            Sesuaikan ukuran teks dan efisiensi kuota sesuai perangkat Anda.
                        </p>
                    </div>
                    <span
                        v-if="displaySavedNotice"
                        class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 animate-fadeIn"
                    >
                        Tersimpan
                    </span>
                </div>

                <!-- Font Size Selector -->
                <div class="p-4 rounded-2xl bg-canvas/50 border border-[#ECEAE4] space-y-3">
                    <div>
                        <span class="text-xs font-bold text-charcoal block">
                            Ukuran Teks Antarmuka
                        </span>
                        <span class="text-[11px] text-subtext">
                            Atur skala huruf antarmuka agar nyaman dibaca di layar HP atau Laptop.
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 pt-1">
                        <button
                            type="button"
                            @click="applyFontSize('standar')"
                            class="py-2.5 px-3 rounded-xl text-xs font-bold border transition-all cursor-pointer text-center"
                            :class="
                                fontSize === 'standar'
                                    ? 'bg-charcoal text-white border-charcoal shadow-xs'
                                    : 'bg-white text-charcoal border-[#ECEAE4] hover:border-charcoal/40'
                            "
                        >
                            Standar (100%)
                        </button>

                        <button
                            type="button"
                            @click="applyFontSize('sedang')"
                            class="py-2.5 px-3 rounded-xl text-xs font-bold border transition-all cursor-pointer text-center"
                            :class="
                                fontSize === 'sedang'
                                    ? 'bg-charcoal text-white border-charcoal shadow-xs'
                                    : 'bg-white text-charcoal border-[#ECEAE4] hover:border-charcoal/40'
                            "
                        >
                            Sedang (+10%)
                        </button>

                        <button
                            type="button"
                            @click="applyFontSize('besar')"
                            class="py-2.5 px-3 rounded-xl text-xs font-bold border transition-all cursor-pointer text-center"
                            :class="
                                fontSize === 'besar'
                                    ? 'bg-charcoal text-white border-charcoal shadow-xs'
                                    : 'bg-white text-charcoal border-[#ECEAE4] hover:border-charcoal/40'
                            "
                        >
                            Besar (+20%)
                        </button>
                    </div>
                </div>

                <!-- Data Saver Mode -->
                <div class="p-4 rounded-2xl bg-canvas/50 border border-[#ECEAE4] flex items-center justify-between gap-4">
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-charcoal">
                            Mode Penghemat Kuota Internet
                        </span>
                        <span class="text-[11px] text-subtext mt-0.5">
                            Matikan muat otomatis file gambar besar dan pratinjau berat untuk menghemat paket data.
                        </span>
                    </div>
                    <button
                        type="button"
                        @click="toggleDataSaver"
                        class="w-11 h-6 rounded-full transition-colors cursor-pointer relative shrink-0 p-0.5"
                        :class="dataSaver ? 'bg-charcoal' : 'bg-neutral-300'"
                    >
                        <span
                            class="block w-5 h-5 rounded-full bg-white shadow-xs transition-transform duration-200"
                            :class="dataSaver ? 'translate-x-5' : 'translate-x-0'"
                        ></span>
                    </button>
                </div>

                <!-- Smooth Transition Effect -->
                <div class="p-4 rounded-2xl bg-canvas/50 border border-[#ECEAE4] flex items-center justify-between gap-4">
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-charcoal">
                            Animasi & Transisi Halus
                        </span>
                        <span class="text-[11px] text-subtext mt-0.5">
                            Transisi pergerakan tab dan drawer yang halus dan responsif.
                        </span>
                    </div>
                    <button
                        type="button"
                        @click="toggleSmoothAnimation"
                        class="w-11 h-6 rounded-full transition-colors cursor-pointer relative shrink-0 p-0.5"
                        :class="smoothAnimation ? 'bg-charcoal' : 'bg-neutral-300'"
                    >
                        <span
                            class="block w-5 h-5 rounded-full bg-white shadow-xs transition-transform duration-200"
                            :class="smoothAnimation ? 'translate-x-5' : 'translate-x-0'"
                        ></span>
                    </button>
                </div>
            </div>

            <!-- ======================================================== -->
            <!-- TAB 4: SESI & AKUN                                       -->
            <!-- ======================================================== -->
            <div v-else-if="activeTab === 'sesi'" class="space-y-5">
                <div>
                    <h3 class="text-sm font-extrabold text-charcoal">
                        Informasi Sesi Login & Akun
                    </h3>
                    <p class="text-xs text-subtext mt-0.5">
                        Pantau sesi yang sedang aktif di perangkat ini.
                    </p>
                </div>

                <!-- Account Badge Card -->
                <div class="p-4 rounded-2xl bg-canvas border border-[#ECEAE4] flex items-center gap-3.5">
                    <img
                        :src="role.avatar"
                        :alt="role.userName"
                        class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-xs shrink-0"
                    />
                    <div class="flex flex-col min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-charcoal truncate">
                                {{ role.userName }}
                            </span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white border border-[#ECEAE4] text-subtext shrink-0">
                                {{ role.badge }}
                            </span>
                        </div>
                        <span class="text-xs text-subtext truncate mt-0.5">
                            {{ role.userSub }}
                        </span>
                    </div>
                </div>

                <!-- Active Session Device Card -->
                <div class="p-4 rounded-2xl bg-white border border-[#ECEAE4] shadow-xs space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-canvas flex items-center justify-center text-charcoal shrink-0">
                                <LmsIcon name="smartphone" className="w-4 h-4 text-charcoal" />
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-charcoal">
                                    Perangkat Ini (Sesi Aktif)
                                </span>
                                <span class="text-[10px] text-subtext">
                                    Web Browser • Terverifikasi
                                </span>
                            </div>
                        </div>

                        <span class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Online</span>
                        </span>
                    </div>

                    <div class="pt-2 border-t border-[#ECEAE4]/60 grid grid-cols-2 gap-2 text-[11px]">
                        <div>
                            <span class="text-subtext block text-[10px] font-medium">Jaringan:</span>
                            <span class="font-bold text-charcoal">Internal SMK SWJ</span>
                        </div>
                        <div>
                            <span class="text-subtext block text-[10px] font-medium">Waktu Akses:</span>
                            <span class="font-bold text-charcoal">Hari Ini, Aktif</span>
                        </div>
                    </div>
                </div>

                <!-- Logout Action -->
                <div class="pt-3 border-t border-[#ECEAE4] space-y-2">
                    <button
                        type="button"
                        @click="$emit('logout')"
                        class="w-full py-3 rounded-2xl bg-rose-50 text-rose-700 font-bold text-xs hover:bg-rose-100 transition-colors cursor-pointer flex items-center justify-center gap-2"
                    >
                        <LmsIcon name="logout" className="w-4 h-4 text-rose-700" />
                        <span>Keluar dari Akun (Logout)</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer Bar with Close -->
        <div class="px-6 py-3.5 border-t border-[#ECEAE4] bg-canvas/30 flex items-center justify-end shrink-0">
            <button
                type="button"
                @click="$emit('close')"
                class="px-5 py-2.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black transition-all cursor-pointer shadow-xs active:scale-95"
            >
                Selesai
            </button>
        </div>
    </div>
</template>
