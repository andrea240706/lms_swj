<script setup>
defineProps({
    name: {
        type: String,
        required: true,
    },
    className: {
        type: String,
        default: 'w-5 h-5',
    },
});
</script>

<template>
    <!-- Home -->
    <svg
        v-if="name === 'home'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M3 10.5L12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9.5z" />
    </svg>

    <!-- Book / Materi -->
    <svg
        v-else-if="name === 'book'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
    </svg>

    <!-- Task / Nilai -->
    <svg
        v-else-if="name === 'task'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <rect x="4" y="3" width="16" height="18" rx="3" />
        <path d="M9 9h6" />
        <path d="M9 13h6" />
        <path d="M9 17h4" />
    </svg>

    <!-- Chart / Rekap -->
    <svg
        v-else-if="name === 'chart'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M3 3v18h18" />
        <path d="M7 15l4-4 3 3 5-6" />
    </svg>

    <!-- Clock / Presensi -->
    <svg
        v-else-if="name === 'clock'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <circle cx="12" cy="12" r="9" />
        <path d="M12 7v5l3 2" />
    </svg>

    <!-- User -->
    <svg
        v-else-if="name === 'user'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <circle cx="12" cy="8" r="4" />
        <path d="M5 20c1.5-3.5 4.2-5 7-5s5.5 1.5 7 5" />
    </svg>

    <!-- Download -->
    <svg
        v-else-if="name === 'download'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M12 3v12" />
        <path d="M7 10l5 5 5-5" />
        <path d="M5 21h14" />
    </svg>

    <!-- Menu Hamburger -->
    <svg
        v-else-if="name === 'menu'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
    >
        <path d="M4 7h16" />
        <path d="M4 12h10" />
        <path d="M4 17h14" />
    </svg>

    <!-- Chevron Left -->
    <svg
        v-else-if="name === 'chevronLeft'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M15 18l-6-6 6-6" />
    </svg>

    <!-- Chevron Right -->
    <svg
        v-else-if="name === 'chevronRight'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M9 18l6-6-6-6" />
    </svg>

    <!-- Sidebar Panel Toggle -->
    <svg
        v-else-if="name === 'panelLeft'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <rect x="3" y="3" width="18" height="18" rx="2" />
        <path d="M9 3v18" />
        <path d="M14 9l-3 3 3 3" />
    </svg>

    <!-- Back Arrow -->
    <svg
        v-else-if="name === 'back'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M19 12H5" />
        <path d="M11 18l-6-6 6-6" />
    </svg>

    <!-- Play -->
    <svg
        v-else-if="name === 'play'"
        :class="className"
        viewBox="0 0 24 24"
        fill="currentColor"
    >
        <path d="M8 5.5v13l11-6.5-11-6.5z" />
    </svg>

    <!-- Check -->
    <svg
        v-else-if="name === 'check'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2.4"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M20 6L9 17l-5-5" />
    </svg>

    <!-- Lock -->
    <svg
        v-else-if="name === 'lock'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <rect x="5" y="11" width="14" height="10" rx="2" />
        <path d="M8 11V7a4 4 0 0 1 8 0v4" />
    </svg>

    <!-- Unlock -->
    <svg
        v-else-if="name === 'unlock'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <rect x="5" y="11" width="14" height="10" rx="2" />
        <path d="M8 11V7a4 4 0 0 1 7.5-1.8" />
    </svg>

    <!-- Arrow Up Right -->
    <svg
        v-else-if="name === 'arrowUpRight'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M7 17L17 7" />
        <path d="M8 7h9v9" />
    </svg>

    <!-- Bell / Notification -->
    <svg
        v-else-if="name === 'bell'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
        <path d="M13.73 21a2 2 0 0 1-3.46 0" />
    </svg>

    <!-- Video Camera -->
    <svg
        v-else-if="name === 'video'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <polygon points="23 7 16 12 23 17 23 7" />
        <rect x="1" y="5" width="15" height="14" rx="2" ry="2" />
    </svg>

    <!-- Plus -->
    <svg
        v-else-if="name === 'plus'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M12 5v14" />
        <path d="M5 12h14" />
    </svg>

    <!-- Close -->
    <svg
        v-else-if="name === 'x'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M18 6L6 18" />
        <path d="M6 6l12 12" />
    </svg>

    <!-- Upload -->
    <svg
        v-else-if="name === 'upload'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M12 21V9" />
        <path d="M7 14l5-5 5 5" />
        <path d="M5 3h14" />
    </svg>

    <!-- File -->
    <svg
        v-else-if="name === 'file'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
        <path d="M14 2v6h6" />
    </svg>

    <!-- Trash -->
    <svg
        v-else-if="name === 'trash'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M3 6h18" />
        <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" />
        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
        <path d="M10 11v6" />
        <path d="M14 11v6" />
    </svg>

    <!-- Edit -->
    <svg
        v-else-if="name === 'edit'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M12 20h9" />
        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z" />
    </svg>

    <!-- Folder -->
    <svg
        v-else-if="name === 'folder'"
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
    </svg>

    <!-- Fallback Check/Circle -->
    <svg
        v-else
        :class="className"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <circle cx="12" cy="12" r="9" />
    </svg>
</template>
