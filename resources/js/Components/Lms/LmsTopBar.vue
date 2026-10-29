<script setup>
import LmsIcon from '@/Components/Lms/LmsIcon.vue';

defineProps({
    role: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        default: null,
    },
    isSidebarCollapsed: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['openMenu', 'openProfile', 'openNotification', 'toggleSidebar', 'openSettings']);
</script>

<template>
    <header class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 pt-4 pb-3">
        <div class="w-full max-w-[1800px] mx-auto flex items-center justify-between">
            <!-- ==================================================== -->
            <!-- LEFT SECTION                                         -->
            <!-- Desktop: [ > ] (if collapsed) + Page Title           -->
            <!-- Mobile: Avatar + Profile / Title                     -->
            <!-- ==================================================== -->
            <div class="flex items-center gap-3">
                <!-- Sidebar Toggle Hamburger Button (Desktop Only: beside menu title) -->
                <button
                    type="button"
                    @click="$emit('toggleSidebar')"
                    class="hidden lg:flex w-10 h-10 rounded-2xl bg-white border border-[#ECEAE4] items-center justify-center text-charcoal soft-shadow hover:bg-canvas active:scale-95 transition-all cursor-pointer shrink-0"
                    title="Buka / Tutup Sidebar (⌘B)"
                >
                    <LmsIcon name="menu" className="w-5 h-5 text-charcoal" />
                </button>

                <!-- Mobile Only: Profile Avatar & Status -->
                <button
                    type="button"
                    @click="$emit('openProfile')"
                    class="lg:hidden relative group cursor-pointer shrink-0"
                    title="Profil & Sesi Akun"
                >
                    <img
                        :src="role.avatar"
                        :alt="role.userName"
                        class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm"
                    />
                    <span
                        class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-[#4CAF50] border-2 border-white ring-1 ring-black/5"
                    ></span>
                </button>

                <!-- Left Text Content -->
                <!-- 1. If Title is set (Both mobile & desktop) -->
                <div v-if="title" class="flex flex-col">
                    <h1 class="text-[17px] sm:text-[18px] lg:text-[20px] font-extrabold text-charcoal tracking-tight leading-tight">
                        {{ title }}
                    </h1>
                    <span class="text-[11px] font-medium text-subtext">
                        Portal Akademik & Pembelajaran
                    </span>
                </div>

                <!-- 2. If Title is NOT set (Home/Beranda) -->
                <div v-else class="flex flex-col">
                    <!-- Mobile view: User Name & Badge -->
                    <div class="lg:hidden flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="text-[15px] font-extrabold text-charcoal leading-tight">
                                {{ role.userName }}
                            </span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white border border-[#ECEAE4] text-subtext shadow-2xs">
                                {{ role.badge }}
                            </span>
                        </div>
                        <span class="text-[11px] font-medium text-subtext mt-0.5">
                            {{ role.label }}
                        </span>
                    </div>

                    <!-- Desktop view: Page Title "Beranda" -->
                    <div class="hidden lg:flex flex-col">
                        <h1 class="text-[20px] font-extrabold text-charcoal tracking-tight leading-tight">
                            Beranda
                        </h1>
                        <span class="text-[11px] font-medium text-subtext">
                            Portal Akademik SMK SWJ
                        </span>
                    </div>
                </div>
            </div>

            <!-- ==================================================== -->
            <!-- RIGHT SECTION                                        -->
            <!-- Desktop: Academic Term + Bell + Profile Avatar Widget-->
            <!-- Mobile: Bell                                         -->
            <!-- ==================================================== -->
            <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">
                <!-- Academic Term Tag for Desktop -->
                <div class="hidden md:flex items-center gap-2 bg-white px-3.5 py-2 rounded-2xl border border-[#ECEAE4] soft-shadow text-xs font-bold text-charcoal">
                    <span class="w-2 h-2 rounded-full bg-[#4CAF50] animate-pulse"></span>
                    <span>T.A 2026/2027 Ganjil</span>
                </div>

                <!-- Notification Bell Button -->
                <button
                    type="button"
                    @click="$emit('openNotification')"
                    class="relative w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-white border border-[#ECEAE4] flex items-center justify-center text-charcoal soft-shadow hover:bg-black/5 active:scale-95 transition-all cursor-pointer"
                    title="Notifikasi"
                >
                    <LmsIcon name="bell" className="w-5 h-5 text-charcoal" />
                    <!-- Unread Indicator Dot -->
                    <span
                        class="absolute top-2.5 right-2.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"
                    ></span>
                </button>

                <!-- Settings Button -->
                <button
                    type="button"
                    @click="$emit('openSettings')"
                    class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-white border border-[#ECEAE4] flex items-center justify-center text-charcoal soft-shadow hover:bg-black/5 active:scale-95 transition-all cursor-pointer"
                    title="Pengaturan LMS"
                >
                    <LmsIcon name="settings" className="w-5 h-5 text-charcoal" />
                </button>

                <!-- Desktop Separator -->
                <div class="hidden lg:block w-px h-8 bg-[#ECEAE4]/80 mx-1"></div>

                <!-- Desktop Only: Profile Avatar & User Details on the RIGHT -->
                <button
                    type="button"
                    @click="$emit('openProfile')"
                    class="hidden lg:flex items-center gap-3 p-1.5 pr-3.5 rounded-2xl bg-white border border-[#ECEAE4] soft-shadow hover:bg-canvas active:scale-98 transition-all cursor-pointer group"
                    title="Profil & Sesi Akun"
                >
                    <!-- User Avatar with Online Dot -->
                    <div class="relative shrink-0">
                        <img
                            :src="role.avatar"
                            :alt="role.userName"
                            class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-xs group-hover:scale-105 transition-transform"
                        />
                        <span
                            class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-[#4CAF50] border-2 border-white ring-1 ring-black/5"
                        ></span>
                    </div>

                    <!-- User Name & Badge & Role -->
                    <div class="flex flex-col text-left">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[14px] font-extrabold text-charcoal leading-tight">
                                {{ role.userName }}
                            </span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-canvas border border-[#ECEAE4] text-subtext shadow-2xs">
                                {{ role.badge }}
                            </span>
                        </div>
                        <span class="text-[11px] font-medium text-subtext mt-0.5">
                            {{ role.label }}
                        </span>
                    </div>
                </button>
            </div>
        </div>
    </header>
</template>
