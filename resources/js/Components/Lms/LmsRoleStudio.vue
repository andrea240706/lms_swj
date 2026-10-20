<script setup>
defineProps({
    roles: {
        type: Object,
        required: true,
    },
    activeRole: {
        type: String,
        required: true,
    },
});

defineEmits(['selectRole']);
</script>

<template>
    <aside
        class="w-full max-w-[410px] lg:max-w-[340px] bg-white/90 backdrop-blur-xl rounded-[28px] p-5 soft-shadow border border-white/60 self-center lg:self-start lg:mt-4"
    >
        <div class="flex items-center justify-between mb-3">
            <span
                class="text-[11px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-charcoal text-white"
            >
                Studio Inspector
            </span>
            <span class="text-xs text-subtext font-medium">Mobile-First LMS</span>
        </div>
        <h1 class="text-lg font-extrabold text-charcoal leading-snug">
            LMS SMK Sriwijaya
        </h1>
        <p class="text-xs text-subtext mt-1 mb-4">
            Pilih <strong>Role Pengguna</strong> di bawah untuk menguji struktur menu, alur kerja, &amp; desain antarmuka setiap akun.
        </p>

        <!-- 6 Role Selector -->
        <div class="mb-4">
            <label
                class="text-[11px] font-bold uppercase tracking-wider text-subtext block mb-2"
            >
                Pilih Role Pengguna (6 Roles)
            </label>
            <div class="grid grid-cols-2 gap-2">
                <button
                    v-for="r in Object.values(roles)"
                    :key="r.id"
                    type="button"
                    @click="$emit('selectRole', r.id)"
                    class="flex items-center justify-between px-3 py-2.5 rounded-2xl border text-left transition-all cursor-pointer"
                    :class="
                        activeRole === r.id
                            ? 'bg-charcoal text-white border-charcoal shadow-xs'
                            : 'bg-canvas text-charcoal border-[#E8E6E0] hover:border-charcoal/40'
                    "
                >
                    <div>
                        <div class="text-xs font-bold leading-tight">{{ r.label }}</div>
                        <div
                            class="text-[10px] mt-0.5"
                            :class="activeRole === r.id ? 'text-white/75' : 'text-subtext'"
                        >
                            {{ r.badge }}
                        </div>
                    </div>
                    <span
                        class="w-2 h-2 rounded-full"
                        :class="activeRole === r.id ? 'bg-[#B9E2A7]' : 'bg-transparent'"
                    ></span>
                </button>
            </div>
        </div>

        <!-- Active Role Quick Summary -->
        <div
            v-if="roles[activeRole]"
            class="p-3.5 rounded-2xl bg-canvas border border-[#ECEAE4] text-xs space-y-1.5"
        >
            <div class="flex items-center gap-2.5">
                <img
                    :src="roles[activeRole].avatar"
                    :alt="roles[activeRole].userName"
                    class="w-9 h-9 rounded-full object-cover border border-white shadow-xs"
                />
                <div>
                    <div class="font-bold text-charcoal leading-tight">
                        {{ roles[activeRole].userName }}
                    </div>
                    <div class="text-[11px] text-subtext">
                        {{ roles[activeRole].label }} • {{ roles[activeRole].badge }}
                    </div>
                </div>
            </div>
            <p class="text-[11px] text-subtext pt-1 border-t border-[#ECEAE4] leading-relaxed">
                {{ roles[activeRole].focusNote }}
            </p>
        </div>
    </aside>
</template>
