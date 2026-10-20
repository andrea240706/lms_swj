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

defineEmits(['openMenu', 'openProfile']);
</script>

<template>
    <header class="flex items-center justify-between px-6 pt-1 pb-3">
        <!-- Menu / Role Switcher Button -->
        <button
            type="button"
            @click="$emit('openMenu')"
            class="w-11 h-11 rounded-full bg-white border border-[#ECEAE4] flex items-center justify-center text-charcoal soft-shadow active:scale-95 transition-transform cursor-pointer"
            title="Menu & Ganti Role"
        >
            <LmsIcon name="menu" className="w-5 h-5" />
        </button>

        <!-- Center: Title or Badge -->
        <div v-if="title" class="text-center">
            <h1 class="text-[17px] font-extrabold text-charcoal tracking-tight">
                {{ title }}
            </h1>
        </div>
        <div v-else class="text-center">
            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-white border border-[#ECEAE4] text-subtext">
                {{ role.label }} • {{ role.badge }}
            </span>
        </div>

        <!-- Right: Profile Avatar & Status -->
        <button
            type="button"
            @click="$emit('openProfile')"
            class="relative group cursor-pointer"
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
    </header>
</template>
