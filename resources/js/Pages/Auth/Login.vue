<script setup>
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
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

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const toggleShowPassword = () => {
    showPassword.value = !showPassword.value;
};

const setQuickAccount = (account) => {
    if (account === 'admin') {
        form.username = 'admin@smk.sch.id';
    } else {
        form.username = account;
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
    <Head title="Masuk ke Panel Admin - SMK Sriwijaya 3" />

    <!-- Full-page background canvas -->
    <div class="min-h-screen bg-[#F0F3F8] flex items-center justify-center p-3.5 sm:p-6 lg:p-8 select-none font-sans">

        <!-- Unified Card Container: Responsive for Mobile & Desktop -->
        <div class="w-full max-w-[1040px] bg-white rounded-[24px] sm:rounded-[32px] shadow-[0_20px_50px_rgba(23,26,130,0.12)] border border-slate-200/80 overflow-hidden flex flex-col lg:flex-row transition-all duration-300">

            <!-- ============================================== -->
            <!-- LEFT / TOP PANEL (Deep Blue + Batik Pattern) -->
            <!-- ============================================== -->
            <div
                class="w-full lg:w-[46%] xl:w-[45%] relative overflow-hidden flex flex-col justify-between p-6 sm:p-8 lg:p-10 text-white min-h-[360px] lg:min-h-[620px]"
                style="background: linear-gradient(155deg, #0A0F4D 0%, #171A82 45%, #1C2694 100%);"
            >
                <!-- Batik Pattern Overlay (25% opacity, mix-blend-mode overlay) -->
                <div
                    class="absolute inset-0 bg-repeat pointer-events-none"
                    style="
                        background-image: url('/images/batik-pattern.png');
                        background-size: 280px auto;
                        opacity: 0.25;
                        mix-blend-mode: overlay;
                    "
                ></div>

                <!-- Ambient lighting effects -->
                <div class="absolute -top-20 -left-20 w-72 h-72 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -right-16 w-80 h-80 rounded-full bg-[#171A82]/60 blur-3xl pointer-events-none"></div>

                <!-- Top Header: School Emblem & Name -->
                <div class="relative z-10 flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white p-1.5 shadow-md flex items-center justify-center shrink-0">
                        <img
                            src="/images/logo-smk-round.png"
                            alt="Logo SMK Sriwijaya 3 Lumbir"
                            class="w-full h-full object-contain"
                        />
                    </div>
                    <div>
                        <div class="text-white font-bold text-base leading-tight tracking-tight">
                            Panel Admin
                        </div>
                        <div class="text-[#FBBF24] font-bold text-[11px] sm:text-xs uppercase tracking-wider">
                            SMK SRIWIJAYA 3 LUMBIR
                        </div>
                    </div>
                </div>

                <!-- Middle Content: Pill Badge, Title, Description, Checkpoints -->
                <div class="relative z-10 my-6 lg:my-auto space-y-4 lg:space-y-5">
                    <!-- Administrator Pill -->
                    <div>
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-white/95 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-[#F59E0B] shadow-[0_0_8px_#F59E0B]"></span>
                            Portal Administrator
                        </span>
                    </div>

                    <!-- Heading -->
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-snug">
                        Pusat Kendali Profil &amp; Informasi Sekolah
                    </h1>

                    <!-- Description -->
                    <p class="text-white/80 text-xs sm:text-sm leading-relaxed max-w-md">
                        Kelola data akademik, kegiatan siswa, kemitraan DUDI, serta pembaruan berita dalam satu sistem terpadu.
                    </p>

                    <!-- Features Checklist -->
                    <div class="pt-1 sm:pt-2 space-y-2.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-white/15 border border-white/20 flex items-center justify-center shrink-0 text-white">
                                <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-white/90 text-xs font-medium">Autentikasi Aman &amp; Terenkripsi</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-white/15 border border-white/20 flex items-center justify-center shrink-0 text-white">
                                <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-white/90 text-xs font-medium">Statistik Pengunjung Real-Time</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-white/15 border border-white/20 flex items-center justify-center shrink-0 text-white">
                                <svg class="w-3 h-3 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="text-white/90 text-xs font-medium">Kelola PPDB, Galeri, dan Berkas Unduhan</span>
                        </div>
                    </div>
                </div>

                <!-- Bottom Footer Info -->
                <div class="relative z-10 flex items-center justify-between text-white/50 text-[11px] pt-4 sm:pt-6 border-t border-white/10 mt-auto">
                    <span>Versi CMS 2.0</span>
                    <span>&copy; 2026 SMK Sriwijaya 3</span>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- RIGHT / BOTTOM PANEL (White Form Container) -->
            <!-- ============================================== -->
            <div class="flex-1 w-full p-6 sm:p-10 lg:p-12 xl:p-14 flex flex-col justify-center bg-white">
                <div class="w-full max-w-[440px] mx-auto">

                    <!-- Subtitle & Main Greeting -->
                    <div class="mb-6">
                        <div class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-[#171A82] mb-1">
                            SELAMAT DATANG
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0F172A] tracking-tight">
                            Masuk ke Panel Admin
                        </h2>
                        <p class="text-xs sm:text-sm text-[#64748B] mt-1.5">
                            Silakan masukkan kredensial akun administrator Anda.
                        </p>
                    </div>

                    <!-- Flash Status Alert (e.g. Berhasil Logout) -->
                    <div
                        v-if="status"
                        class="mb-5 p-3.5 rounded-xl bg-emerald-50/90 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5"
                    >
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ status }}</span>
                    </div>

                    <!-- Login Form -->
                    <form @submit.prevent="submit" class="space-y-4">

                        <!-- Field Email / Username -->
                        <div>
                            <label for="username" class="block text-xs font-semibold text-[#1E293B] mb-1.5">
                                Alamat Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="4"></circle>
                                        <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-4 8"></path>
                                    </svg>
                                </div>
                                <input
                                    id="username"
                                    type="text"
                                    v-model="form.username"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="admin@smk.sch.id"
                                    class="w-full pl-10 pr-4 h-11 sm:h-12 bg-white border border-[#E2E8F0] rounded-xl text-sm text-[#0F172A] placeholder:text-[#94A3B8] focus:border-[#171A82] focus:ring-4 focus:ring-[#171A82]/10 focus:outline-none transition-all"
                                    :class="{ 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/10': form.errors.username }"
                                />
                            </div>
                            <InputError class="mt-1.5 text-xs text-rose-500" :message="form.errors.username" />
                        </div>

                        <!-- Field Password with Eye Toggle -->
                        <div>
                            <label for="password" class="block text-xs font-semibold text-[#1E293B] mb-1.5">
                                Kata Sandi
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </div>
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="w-full pl-10 pr-11 h-11 sm:h-12 bg-white border border-[#E2E8F0] rounded-xl text-sm text-[#0F172A] placeholder:text-[#94A3B8] focus:border-[#171A82] focus:ring-4 focus:ring-[#171A82]/10 focus:outline-none transition-all"
                                    :class="{ 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/10': form.errors.password }"
                                />
                                <button
                                    type="button"
                                    @click="toggleShowPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#94A3B8] hover:text-[#0F172A] focus:outline-none transition-colors"
                                    :title="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                    aria-label="Toggle password visibility"
                                >
                                    <!-- Eye icon (Show) -->
                                    <svg v-if="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <!-- Eye off icon (Hide) -->
                                    <svg v-else class="w-4 h-4 text-[#171A82]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            <InputError class="mt-1.5 text-xs text-rose-500" :message="form.errors.password" />
                        </div>

                        <!-- Row: Remember Me & Public Web Link -->
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input
                                    type="checkbox"
                                    v-model="form.remember"
                                    class="w-4 h-4 rounded text-[#171A82] border-slate-300 focus:ring-[#171A82] cursor-pointer"
                                />
                                <span class="text-xs text-[#64748B] font-medium">Ingat Saya</span>
                            </label>

                            <a
                                href="/"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-[#171A82] hover:text-[#0C1252] hover:underline transition-colors"
                            >
                                <span>Lihat Web Publik</span>
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-3">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full h-11 sm:h-12 rounded-xl bg-[#0B1569] hover:bg-[#171A82] active:scale-[0.99] text-white text-sm font-semibold shadow-[0_8px_20px_rgba(11,21,105,0.28)] flex items-center justify-center gap-2 transition-all disabled:opacity-70 disabled:pointer-events-none cursor-pointer"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>{{ form.processing ? 'Memproses...' : 'Masuk ke Panel Admin' }}</span>
                                <svg v-if="!form.processing" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <!-- Quick Testing Accounts Pill -->
                    <div class="mt-6 pt-5 border-t border-slate-100">
                        <p class="text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-center text-[#94A3B8] mb-2.5">
                            Akun Uji Coba Cepat
                        </p>
                        <div class="grid grid-cols-3 gap-1.5">
                            <button
                                type="button"
                                @click="setQuickAccount('admin')"
                                class="px-2 py-1.5 rounded-lg bg-[#F8F9FD] hover:bg-[#EEF2FF] hover:text-[#171A82] border border-slate-200 text-[11px] font-medium text-[#475569] text-center transition-colors cursor-pointer"
                            >
                                Admin
                            </button>
                            <button
                                type="button"
                                @click="setQuickAccount('guru')"
                                class="px-2 py-1.5 rounded-lg bg-[#F8F9FD] hover:bg-[#EEF2FF] hover:text-[#171A82] border border-slate-200 text-[11px] font-medium text-[#475569] text-center transition-colors cursor-pointer"
                            >
                                Guru
                            </button>
                            <button
                                type="button"
                                @click="setQuickAccount('siswa')"
                                class="px-2 py-1.5 rounded-lg bg-[#F8F9FD] hover:bg-[#EEF2FF] hover:text-[#171A82] border border-slate-200 text-[11px] font-medium text-[#475569] text-center transition-colors cursor-pointer"
                            >
                                Siswa
                            </button>
                        </div>
                    </div>

                    <!-- Footer IT Support note -->
                    <div class="mt-6 text-center text-xs text-[#64748B]">
                        Ada kendala akses? Hubungi <span class="font-semibold text-[#0F172A]">Superadmin</span> atau <a href="#" class="font-semibold text-[#171A82] hover:underline">Tim IT Sekolah</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
/* Clean & light transitions */
input:focus {
    box-shadow: 0 0 0 3px rgba(23, 26, 130, 0.12);
}
</style>
