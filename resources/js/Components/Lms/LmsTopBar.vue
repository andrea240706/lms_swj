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
});

defineEmits(['openMenu', 'openProfile', 'openNotification']);
</script>

<template>
    <header class="flex items-center justify-between px-4 sm:px-6 pt-4 pb-3">
        <!-- Left: Profile Avatar + Info / Title -->
        <div class="flex items-center gap-3">
            <!-- Profile Avatar & Status on the Left -->
            <button
                type="button"
                @click="$emit('openProfile')"
                class="relative group cursor-pointer shrink-0"
                title="Profil & Sesi Akun"
            >
                <img
                    :src="role.avatar"
                    :alt="role.userName"
                    class="w-11 h-11 rounded-full object-cover border-2 border-white shadow-sm"
                />
                <span
                    class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-[#4CAF50] border-2 border-white"
                ></span>
            </button>

            <!-- Text Content -->
            <div v-if="title" class="flex flex-col">
                <h1 class="text-[18px] font-extrabold text-charcoal tracking-tight leading-tight">
                    {{ title }}
                </h1>
                <span class="text-[11px] font-medium text-subtext">
                    {{ role.userName }} • {{ role.badge }}
                </span>
            </div>
            <div v-else class="flex flex-col">
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
        </div>

        <!-- Right Side: Notification Bell Button -->
        <div class="flex items-center gap-2 shrink-0">
            <button
                type="button"
                @click="$emit('openNotification')"
                class="relative w-11 h-11 rounded-full bg-white border border-[#ECEAE4] flex items-center justify-center text-charcoal soft-shadow hover:bg-black/5 active:scale-95 transition-all cursor-pointer"
                title="Notifikasi"
            >
                <LmsIcon name="bell" className="w-5 h-5 text-charcoal" />
                <!-- Unread Indicator Dot -->
                <span
                    class="absolute top-2.5 right-2.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"
                ></span>
            </button>
        </div>
    </header>
</template>
