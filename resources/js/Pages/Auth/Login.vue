<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
        default: true,
    },
    status: {
        type: String,
        default: '',
    },
});

// Flow Stage for Mobile View: 'onboarding' | 'login'
const currentScreen = ref('onboarding');

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const showHelpModal = ref(false);

const toggleShowPassword = () => {
    showPassword.value = !showPassword.value;
};

const goToLogin = () => {
    currentScreen.value = 'login';
};

const goToOnboarding = () => {
    currentScreen.value = 'onboarding';
};

const setQuickAccount = (role) => {
    if (role === 'siswa') {
        form.username = '008492104';
    } else if (role === 'guru') {
        form.username = '198804122015041002';
    } else if (role === 'wali') {
        form.username = 'wali.xdkv1@smk.sch.id';
    } else if (role === 'kepsek') {
        form.username = 'kepsek@smk.sch.id';
    } else if (role === 'admin') {
        form.username = 'admin@smk.sch.id';
    } else if (role === 'super_admin' || role === 'superadmin') {
        form.username = 'superadmin@smk.sch.id';
    }
    form.password = 'password';
};

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk ke Ruang Belajar — LMS SMK Sriwijaya" />

    <!-- Full-page Canvas: Warm Alabaster (#F9F8F6) -->
    <div class="min-h-screen bg-[#F9F8F6] text-[#141416] flex flex-col items-center justify-center p-3 sm:p-6 lg:p-8 relative overflow-hidden select-none font-sans">

        <!-- Soft Ambient Background Glows -->
        <div class="pointer-events-none absolute -top-32 -right-32 w-[520px] h-[520px] rounded-full bg-[#FFE8D6] opacity-70 blur-3xl z-0"></div>
        <div class="pointer-events-none absolute -bottom-32 -left-32 w-[480px] h-[480px] rounded-full bg-[#EBF2D5] opacity-50 blur-3xl z-0"></div>

        <!-- ============================================================== -->
        <!-- DESKTOP DUAL-PANEL LAYOUT — tampil di lg+ (≥1024px)            -->
        <!-- ============================================================== -->
        <main class="relative z-10 w-full max-w-[1140px] bg-white rounded-[36px] sm:rounded-[40px] shadow-[0_24px_70px_-15px_rgba(20,20,22,0.08)] border border-[#ECEAE4] overflow-hidden hidden lg:flex flex-row min-h-[660px]">

            <!-- ============================================== -->
            <!-- DESKTOP LEFT PANEL: ONBOARDING SHOWCASE        -->
            <!-- ============================================== -->
            <section class="w-[48%] xl:w-[47%] bg-[#FAF8F5] p-10 xl:p-12 relative overflow-hidden flex flex-col justify-between border-r border-[#ECEAE4]">

                <!-- Concentric Pastel Arcs (Background Decorative) -->
                <div class="absolute -right-24 -bottom-24 w-[480px] h-[480px] rounded-full border-[32px] border-[#D4E4D2]/70 pointer-events-none"></div>
                <div class="absolute -right-24 -bottom-24 w-[416px] h-[416px] rounded-full border-[28px] border-[#FDE4C3]/80 pointer-events-none"></div>
                <div class="absolute -right-24 -bottom-24 w-[360px] h-[360px] rounded-full border-[28px] border-[#FBD2E0]/75 pointer-events-none"></div>
                <div class="absolute -right-24 -bottom-24 w-[304px] h-[304px] rounded-full bg-[#F3EFFB]/60 pointer-events-none"></div>

                <!-- Authentic School Brand Lockup -->
                <div class="relative z-10 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#141416] text-white flex items-center justify-center font-bold text-xs shadow-xs tracking-wider">
                        SMK
                    </div>
                    <div class="leading-tight">
                        <span class="text-sm font-extrabold text-[#141416] tracking-tight">SMK SRIWIJAYA 3</span>
                        <span class="text-[11px] text-[#6E6E77] block font-medium">Sistem Pembelajaran &amp; Akademik Terpadu</span>
                    </div>
                </div>


                <!-- Middle Content: Slogan & Student Illustration -->
                <div class="relative z-10 my-auto py-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-[#6E6E77] mb-2">
                        Portal Akademik Vokasi
                    </p>

                    <h1 class="text-[34px] xl:text-[38px] font-extrabold text-[#141416] leading-[1.18] tracking-tight">
                        Siap Memulai Perjalanan Belajarmu!
                    </h1>

                    <p class="text-[14px] text-[#6E6E77] mt-3 leading-relaxed max-w-[420px]">
                        Akses modul praktik kejuruan, materi pembelajaran interaktif, dan pengumpulan tugas dalam satu portal terpadu.
                    </p>

                    <!-- Student Portrait & Program List -->
                    <div class="mt-8 flex items-center gap-6">
                        <div class="relative shrink-0">
                            <div class="w-[130px] h-[165px] rounded-[24px] bg-[#FFF9F3] border border-[#ECEAE4] flex items-end justify-center overflow-hidden shadow-xs">
                                <img
                                    src="/images/student-hero.png"
                                    alt="Pelajar SMK Vokasi"
                                    class="w-full h-full object-contain object-bottom"
                                />
                            </div>
                        </div>

                        <!-- Program Tags -->
                        <div class="space-y-2 flex-1">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-[#6E6E77]">Program Keahlian:</div>
                            <div class="flex flex-wrap gap-1.5">
                                <span class="px-3 py-1 rounded-xl bg-white border border-[#ECEAE4] text-xs font-bold text-[#141416] shadow-2xs">
                                    Rekayasa Perangkat Lunak
                                </span>
                                <span class="px-3 py-1 rounded-xl bg-white border border-[#ECEAE4] text-xs font-bold text-[#141416] shadow-2xs">
                                    Teknik Komputer Jaringan
                                </span>
                                <span class="px-3 py-1 rounded-xl bg-white border border-[#ECEAE4] text-xs font-bold text-[#141416] shadow-2xs">
                                    Desain Komunikasi Visual
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Footer Info -->
                <div class="relative z-10 flex items-center justify-between text-xs text-[#6E6E77] pt-4 border-t border-[#ECEAE4]/80">
                    <span>Kurikulum Merdeka SMK</span>
                    <span>SMK Sriwijaya 3</span>
                </div>
            </section>

            <!-- ============================================== -->
            <!-- DESKTOP RIGHT PANEL: LOGIN CREDENTIALS FORM    -->
            <!-- ============================================== -->
            <section class="flex-1 p-10 xl:p-14 flex flex-col justify-center bg-white">
                <div class="w-full max-w-[440px] mx-auto">

                    <!-- Form Heading -->
                    <div class="mb-8">
                        <p class="text-xs font-bold uppercase tracking-wider text-[#6E6E77] mb-1.5">
                            Otentikasi Pengguna
                        </p>

                        <h2 class="text-[30px] font-extrabold text-[#141416] tracking-tight leading-tight">
                            Masuk ke Ruang Belajar
                        </h2>
                        <p class="text-[13px] text-[#6E6E77] mt-1.5 leading-relaxed">
                            Masukkan NISN, NIP, atau kredensial akun akademik Anda untuk memulai sesi.
                        </p>
                    </div>

                    <!-- Flash Status Alert -->
                    <div
                        v-if="status"
                        class="mb-4 p-3.5 rounded-2xl bg-[#EBF2D5] border border-[#D4E4D2] text-[#3D5A45] text-xs flex items-center gap-2.5"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ status }}</span>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submit" class="space-y-4">

                        <!-- Input 1: NISN / NIP / Username -->
                        <div>
                            <label for="desktop-username" class="block text-xs font-bold text-[#141416] mb-1.5 ml-1">
                                NISN / NIP / Username
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#6E6E77]">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="8" r="4"></circle>
                                        <path d="M5 20c1.5-3.5 4.2-5 7-5s5.5 1.5 7 5"></path>
                                    </svg>
                                </div>
                                <input
                                    id="desktop-username"
                                    type="text"
                                    v-model="form.username"
                                    required
                                    autocomplete="username"
                                    placeholder="Masukkan NISN, NIP, atau Username"
                                    class="w-full pl-11 pr-4 py-3.5 bg-white border border-[#E6E4DF] rounded-2xl text-sm font-semibold text-[#141416] placeholder:text-[#A09E97] placeholder:font-normal focus:border-[#141416] focus:ring-2 focus:ring-[#141416]/10 focus:outline-none transition-all shadow-2xs"
                                    :class="{ 'border-rose-400 focus:border-rose-500': form.errors.username }"
                                />
                            </div>
                            <InputError class="mt-1 ml-2 text-xs text-rose-500" :message="form.errors.username" />
                        </div>

                        <!-- Input 2: Kata Sandi with Toggle -->
                        <div>
                            <label for="desktop-password" class="block text-xs font-bold text-[#141416] mb-1.5 ml-1">
                                Kata Sandi
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#6E6E77]">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="5" y="11" width="14" height="10" rx="2"></rect>
                                        <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
                                    </svg>
                                </div>
                                <input
                                    id="desktop-password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••••••"
                                    class="w-full pl-11 pr-11 py-3.5 bg-white border border-[#E6E4DF] rounded-2xl text-sm font-semibold text-[#141416] placeholder:text-[#A09E97] focus:border-[#141416] focus:ring-2 focus:ring-[#141416]/10 focus:outline-none transition-all shadow-2xs"
                                    :class="{ 'border-rose-400 focus:border-rose-500': form.errors.password }"
                                />
                                <button
                                    type="button"
                                    @click="toggleShowPassword"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-[#6E6E77] hover:text-[#141416] transition-colors cursor-pointer"
                                    title="Tampilkan / Sembunyikan Kata Sandi"
                                >
                                    <svg v-if="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg v-else class="w-4 h-4 text-[#141416]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            <InputError class="mt-1 ml-2 text-xs text-rose-500" :message="form.errors.password" />
                        </div>

                        <!-- Row: 'Ingat Sesi Saya' & 'Lupa Kata Sandi' -->
                        <div class="flex items-center justify-between pt-1 px-1">
                            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                <div class="relative">
                                    <input
                                        type="checkbox"
                                        v-model="form.remember"
                                        class="sr-only peer"
                                    />
                                    <div class="w-9 h-5 bg-[#ECEAE4] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#141416]"></div>
                                </div>
                                <span class="text-xs font-semibold text-[#6E6E77] peer-checked:text-[#141416]">Ingat Sesi Saya</span>
                            </label>

                            <button
                                type="button"
                                @click="showHelpModal = true"
                                class="text-xs font-semibold text-[#6E6E77] hover:text-[#141416] hover:underline transition-colors cursor-pointer"
                            >
                                Lupa Kata Sandi?
                            </button>
                        </div>

                        <!-- Submit CTA Button -->
                        <div class="pt-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full py-4 rounded-full bg-[#141416] hover:bg-[#2A2A2E] active:scale-[0.99] text-white text-sm font-bold shadow-[0_8px_24px_rgba(20,20,22,0.18)] flex items-center justify-center gap-2 transition-all disabled:opacity-70 disabled:pointer-events-none cursor-pointer"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>{{ form.processing ? 'Memverifikasi...' : 'Masuk Sekarang' }}</span>
                                <svg v-if="!form.processing" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M7 17L17 7"></path>
                                    <path d="M8 7h9v9"></path>
                                </svg>
                            </button>
                        </div>
                    </form>

                    <!-- Quick Testing Accounts -->
                    <div class="mt-6 pt-5 border-t border-[#ECEAE4]">
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#6E6E77]">Akun Uji Coba Cepat:</span>
                            <span class="text-[10px] text-[#A09E97]">Klik untuk isi otomatis</span>
                        </div>
                        <div class="grid grid-cols-6 gap-1.5">
                            <button type="button" @click="setQuickAccount('siswa')" class="px-2 py-2 rounded-xl bg-[#FAF8F5] hover:bg-[#EBF2D5] border border-[#ECEAE4] text-[11px] font-bold text-[#141416] text-center transition-colors cursor-pointer">Siswa</button>
                            <button type="button" @click="setQuickAccount('guru')" class="px-2 py-2 rounded-xl bg-[#FAF8F5] hover:bg-[#FBD2E0] border border-[#ECEAE4] text-[11px] font-bold text-[#141416] text-center transition-colors cursor-pointer">Guru</button>
                            <button type="button" @click="setQuickAccount('wali')" class="px-2 py-2 rounded-xl bg-[#FAF8F5] hover:bg-[#D0E8F8] border border-[#ECEAE4] text-[11px] font-bold text-[#141416] text-center transition-colors cursor-pointer">Wali</button>
                            <button type="button" @click="setQuickAccount('kepsek')" class="px-2 py-2 rounded-xl bg-[#FAF8F5] hover:bg-[#FDE4C3] border border-[#ECEAE4] text-[11px] font-bold text-[#141416] text-center transition-colors cursor-pointer">Kepsek</button>
                            <button type="button" @click="setQuickAccount('admin')" class="px-2 py-2 rounded-xl bg-[#FAF8F5] hover:bg-[#E2D9F3] border border-[#ECEAE4] text-[11px] font-bold text-[#141416] text-center transition-colors cursor-pointer">Admin</button>
                            <button type="button" @click="setQuickAccount('super_admin')" class="px-2 py-2 rounded-xl bg-[#FAF8F5] hover:bg-[#D4E4D2] border border-[#ECEAE4] text-[11px] font-bold text-[#141416] text-center transition-colors cursor-pointer">Super</button>
                        </div>
                    </div>

                    <!-- Footer Note -->
                    <div class="mt-6 text-center text-xs text-[#6E6E77]">
                        Belum punya akun? <button type="button" @click="showHelpModal = true" class="font-bold text-[#141416] hover:underline cursor-pointer">Hubungi Admin Sekolah</button>
                    </div>

                </div>
            </section>
        </main>

        <!-- ============================================================== -->
        <!-- MOBILE LAYOUT — tampil di bawah lg (<1024px) secara otomatis   -->
        <!-- ============================================================== -->
        <main class="relative z-10 w-full max-w-[420px] min-h-[100dvh] bg-[#F9F8F6] flex flex-col justify-between lg:hidden">

            <!-- ============================================== -->
            <!-- MOBILE SCREEN 1: ONBOARDING FLOW               -->
            <!-- ============================================== -->
            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 translate-x-4"
                enter-to-class="opacity-100 translate-x-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 translate-x-0"
                leave-to-class="opacity-0 -translate-x-4"
            >
                <div v-if="currentScreen === 'onboarding'" class="flex-1 flex flex-col justify-between">

                    <!-- Top Section: Concentric Pastel Rainbow Arcs & Student Illustration -->
                    <div class="relative flex-1 flex items-end justify-center pt-10 pb-0 px-6 overflow-hidden min-h-[380px]">

                        <!-- Concentric Soft Pastel Arcs -->
                        <div class="absolute bottom-[-110px] w-[360px] h-[360px] rounded-full border-[22px] border-[#D4E4D2]/80 pointer-events-none"></div>
                        <div class="absolute bottom-[-110px] w-[316px] h-[316px] rounded-full border-[20px] border-[#FDE4C3]/85 pointer-events-none"></div>
                        <div class="absolute bottom-[-110px] w-[276px] h-[276px] rounded-full border-[20px] border-[#FBD2E0]/80 pointer-events-none"></div>
                        <div class="absolute bottom-[-110px] w-[236px] h-[236px] rounded-full bg-[#F3EFFB]/60 pointer-events-none"></div>

                        <!-- Top Title and Skip Button -->
                        <div class="absolute top-6 inset-x-6 z-20 flex items-center justify-between">
                            <span class="text-xs font-extrabold text-[#141416] tracking-tight">SMK SRIWIJAYA 3</span>
                            <button
                                type="button"
                                @click="goToLogin"
                                class="text-xs font-bold text-[#6E6E77] hover:text-[#141416] transition-colors cursor-pointer"
                            >
                                Lewati
                            </button>
                        </div>

                        <!-- Student Hero Portrait -->
                        <div class="relative z-10 flex flex-col items-center">
                            <img
                                src="/images/student-hero.png"
                                alt="Pelajar SMK Vokasi"
                                class="w-[230px] h-[300px] object-contain object-bottom drop-shadow-xl"
                            />
                        </div>
                    </div>

                    <!-- Bottom Section: White Rounded Sheet -->
                    <div class="relative z-20 bg-white rounded-t-[32px] px-7 pt-7 pb-8 shadow-[0_-12px_40px_rgba(20,20,22,0.06)] flex flex-col items-center text-center">

                        <div class="w-10 h-1 bg-[#ECEAE4] rounded-full mb-5"></div>

                        <h1 class="text-[26px] sm:text-[28px] font-extrabold text-[#141416] leading-[1.2] tracking-tight">
                            Siap Memulai Perjalanan Belajarmu!
                        </h1>

                        <p class="text-[13px] text-[#6E6E77] mt-2.5 leading-relaxed max-w-[320px]">
                            Akses modul praktik kejuruan, materi pembelajaran, dan pengumpulan tugas dalam satu genggaman.
                        </p>

                        <div class="flex items-center gap-1.5 my-5">
                            <span class="w-6 h-1.5 rounded-full bg-[#141416]"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-[#ECEAE4]"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-[#ECEAE4]"></span>
                        </div>

                        <!-- Full-width Capsule Slide Button -->
                        <button
                            type="button"
                            @click="goToLogin"
                            class="w-full bg-[#F1EFEA] hover:bg-[#E8E5DD] active:scale-[0.99] transition-all p-2 rounded-full flex items-center justify-between group cursor-pointer"
                        >
                            <span class="w-12 h-12 rounded-full bg-white text-[#141416] flex items-center justify-center shadow-xs group-hover:translate-x-1.5 transition-transform shrink-0">
                                <svg class="w-5 h-5 text-[#141416]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span class="text-sm font-bold text-[#141416]">Masuk ke Akun</span>
                            <span class="pr-4 text-xs font-bold tracking-widest text-[#6E6E77] flex items-center gap-0.5 group-hover:translate-x-0.5 transition-transform">
                                <span class="opacity-40">&gt;</span>
                                <span class="opacity-70">&gt;</span>
                                <span class="text-[#141416]">&gt;</span>
                            </span>
                        </button>
                    </div>

                </div>
            </transition>

            <!-- ============================================== -->
            <!-- MOBILE SCREEN 2: LOGIN FLOW                    -->
            <!-- ============================================== -->
            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 translate-x-4"
                enter-to-class="opacity-100 translate-x-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 translate-x-0"
                leave-to-class="opacity-0 -translate-x-4"
            >
                <div v-if="currentScreen === 'login'" class="flex-1 flex flex-col justify-between px-6 sm:px-7 pt-5 pb-7 overflow-y-auto">

                    <div>
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">
                            <button
                                type="button"
                                @click="goToOnboarding"
                                class="w-11 h-11 rounded-full bg-white border border-[#ECEAE4] flex items-center justify-center text-[#141416] shadow-xs hover:bg-[#F9F8F6] active:scale-95 transition-all cursor-pointer"
                                title="Kembali ke Onboarding"
                            >
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 12H5"></path>
                                    <path d="M12 19l-7-7 7-7"></path>
                                </svg>
                            </button>

                            <span class="text-xs font-bold uppercase tracking-wider text-[#6E6E77]">SMK SRIWIJAYA 3</span>
                        </div>

                        <div class="mb-6">
                            <h2 class="text-[26px] sm:text-[28px] font-extrabold text-[#141416] tracking-tight leading-tight">
                                Masuk ke Ruang Belajar
                            </h2>
                            <p class="text-[13px] text-[#6E6E77] mt-1.5 leading-relaxed">
                                Selamat datang kembali! Silakan masuk ke akun Anda.
                            </p>
                        </div>

                        <div
                            v-if="status"
                            class="mb-4 p-3 rounded-2xl bg-[#EBF2D5] border border-[#D4E4D2] text-[#3D5A45] text-xs flex items-center gap-2"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ status }}</span>
                        </div>

                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label for="mobile-username" class="block text-xs font-bold text-[#141416] mb-1.5 ml-1">
                                    NISN / NIP / Username
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#6E6E77]">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="8" r="4"></circle>
                                            <path d="M5 20c1.5-3.5 4.2-5 7-5s5.5 1.5 7 5"></path>
                                        </svg>
                                    </div>
                                    <input
                                        id="mobile-username"
                                        type="text"
                                        v-model="form.username"
                                        required
                                        autocomplete="username"
                                        placeholder="Masukkan NISN, NIP, atau Username"
                                        class="w-full pl-11 pr-4 py-3.5 bg-white border border-[#E6E4DF] rounded-2xl text-sm font-semibold text-[#141416] placeholder:text-[#A09E97] placeholder:font-normal focus:border-[#141416] focus:ring-2 focus:ring-[#141416]/10 focus:outline-none transition-all shadow-2xs"
                                        :class="{ 'border-rose-400 focus:border-rose-500': form.errors.username }"
                                    />
                                </div>
                                <InputError class="mt-1 ml-2 text-xs text-rose-500" :message="form.errors.username" />
                            </div>

                            <div>
                                <label for="mobile-password" class="block text-xs font-bold text-[#141416] mb-1.5 ml-1">
                                    Kata Sandi
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#6E6E77]">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="5" y="11" width="14" height="10" rx="2"></rect>
                                            <path d="M8 11V7a4 4 0 0 1 8 0v4"></path>
                                        </svg>
                                    </div>
                                    <input
                                        id="mobile-password"
                                        :type="showPassword ? 'text' : 'password'"
                                        v-model="form.password"
                                        required
                                        autocomplete="current-password"
                                        placeholder="••••••••••••"
                                        class="w-full pl-11 pr-11 py-3.5 bg-white border border-[#E6E4DF] rounded-2xl text-sm font-semibold text-[#141416] placeholder:text-[#A09E97] focus:border-[#141416] focus:ring-2 focus:ring-[#141416]/10 focus:outline-none transition-all shadow-2xs"
                                        :class="{ 'border-rose-400 focus:border-rose-500': form.errors.password }"
                                    />
                                    <button
                                        type="button"
                                        @click="toggleShowPassword"
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-[#6E6E77] hover:text-[#141416] transition-colors cursor-pointer"
                                        title="Tampilkan / Sembunyikan Kata Sandi"
                                    >
                                        <svg v-if="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg v-else class="w-4 h-4 text-[#141416]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                                <InputError class="mt-1 ml-2 text-xs text-rose-500" :message="form.errors.password" />
                            </div>

                            <div class="flex items-center justify-between pt-1 px-1">
                                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                    <div class="relative">
                                        <input
                                            type="checkbox"
                                            v-model="form.remember"
                                            class="sr-only peer"
                                        />
                                        <div class="w-9 h-5 bg-[#ECEAE4] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#141416]"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-[#6E6E77] peer-checked:text-[#141416]">Ingat Sesi Saya</span>
                                </label>

                                <button
                                    type="button"
                                    @click="showHelpModal = true"
                                    class="text-xs font-semibold text-[#6E6E77] hover:text-[#141416] hover:underline transition-colors cursor-pointer"
                                >
                                    Lupa Kata Sandi?
                                </button>
                            </div>

                            <div class="pt-4">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full py-4 rounded-full bg-[#141416] hover:bg-[#2A2A2E] active:scale-[0.99] text-white text-sm font-bold shadow-[0_8px_24px_rgba(20,20,22,0.18)] flex items-center justify-center gap-2 transition-all disabled:opacity-70 disabled:pointer-events-none cursor-pointer"
                                >
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    <span>{{ form.processing ? 'Memverifikasi...' : 'Masuk Sekarang' }}</span>
                                    <svg v-if="!form.processing" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M7 17L17 7"></path>
                                        <path d="M8 7h9v9"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>

                        <div class="mt-6 pt-5 border-t border-[#ECEAE4]">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#6E6E77]">Akun Uji Coba Cepat:</span>
                            </div>
                            <div class="grid grid-cols-3 sm:grid-cols-6 gap-1.5">
                                <button type="button" @click="setQuickAccount('siswa')" class="px-2 py-1.5 rounded-xl bg-white hover:bg-[#EBF2D5] border border-[#E6E4DF] text-[11px] font-bold text-[#141416] text-center transition-colors cursor-pointer">Siswa</button>
                                <button type="button" @click="setQuickAccount('guru')" class="px-2 py-1.5 rounded-xl bg-white hover:bg-[#FBD2E0] border border-[#E6E4DF] text-[11px] font-bold text-[#141416] text-center transition-colors cursor-pointer">Guru</button>
                                <button type="button" @click="setQuickAccount('wali')" class="px-2 py-1.5 rounded-xl bg-white hover:bg-[#D0E8F8] border border-[#E6E4DF] text-[11px] font-bold text-[#141416] text-center transition-colors cursor-pointer">Wali</button>
                                <button type="button" @click="setQuickAccount('kepsek')" class="px-2 py-1.5 rounded-xl bg-white hover:bg-[#FDE4C3] border border-[#E6E4DF] text-[11px] font-bold text-[#141416] text-center transition-colors cursor-pointer">Kepsek</button>
                                <button type="button" @click="setQuickAccount('admin')" class="px-2 py-1.5 rounded-xl bg-white hover:bg-[#E2D9F3] border border-[#E6E4DF] text-[11px] font-bold text-[#141416] text-center transition-colors cursor-pointer">Admin</button>
                                <button type="button" @click="setQuickAccount('super_admin')" class="px-2 py-1.5 rounded-xl bg-white hover:bg-[#D4E4D2] border border-[#E6E4DF] text-[11px] font-bold text-[#141416] text-center transition-colors cursor-pointer">Super Admin</button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 text-center text-xs text-[#6E6E77]">
                        Belum punya akun? <button type="button" @click="showHelpModal = true" class="font-bold text-[#141416] hover:underline cursor-pointer">Hubungi Admin Sekolah</button>
                    </div>

                </div>
            </transition>

        </main>

        <!-- Help / Password Reset Modal -->
        <div
            v-if="showHelpModal"
            class="fixed inset-0 bg-[#141416]/40 backdrop-blur-xs flex items-center justify-center p-4 z-50"
            @click="showHelpModal = false"
        >
            <div
                class="bg-white rounded-[32px] p-6 max-w-sm w-full text-center shadow-2xl border border-white"
                @click.stop
            >
                <div class="w-12 h-12 rounded-2xl bg-[#FAF8F5] border border-[#ECEAE4] text-[#141416] mx-auto flex items-center justify-center mb-3.5">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 8v4"></path>
                        <path d="M12 16h.01"></path>
                    </svg>
                </div>
                <h3 class="text-base font-extrabold text-[#141416]">Bantuan Akses Akun</h3>
                <p class="text-xs text-[#6E6E77] mt-2 leading-relaxed">
                    Untuk keamanan data akademik SMK, akun dibuat dan diatur langsung oleh Operator Admin Sekolah. Silakan temui staf Tata Usaha (TU) untuk reset kata sandi instan.
                </p>
                <button
                    type="button"
                    @click="showHelpModal = false"
                    class="mt-5 w-full py-3 rounded-full bg-[#141416] text-white text-xs font-bold hover:bg-[#2A2A2E] transition-colors cursor-pointer"
                >
                    Saya Mengerti
                </button>
            </div>
        </div>

    </div>
</template>

<style scoped>
/* Micro-interaction focus effect */
input:focus {
    box-shadow: 0 0 0 3px rgba(20, 20, 22, 0.08);
}
</style>
