<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const notifications = ref([]);
const activeTimers = new Map();

const removeNotification = (id) => {
    if (activeTimers.has(id)) {
        clearTimeout(activeTimers.get(id));
        activeTimers.delete(id);
    }
    const idx = notifications.value.findIndex((n) => n.id === id);
    if (idx !== -1) {
        notifications.value.splice(idx, 1);
    }
};

const addNotification = (item) => {
    if (!item) return;

    // Deduplicate identical pending messages within a short window
    const now = Date.now();
    const existing = notifications.value.find(
        (n) => n.message === item.message && n.type === item.type && now - n.createdAt < 1000
    );
    if (existing) return;

    const id = item.id || `notif_${now}_${Math.random().toString(36).substr(2, 6)}`;
    const duration = typeof item.duration === 'number' ? item.duration : 5000;

    const notif = {
        id,
        createdAt: now,
        type: item.type || 'info',
        model: item.model || 'toast',
        title: item.title ?? getDefaultTitle(item.type),
        message: item.message ?? '',
        icon: item.icon ?? null,
        duration,
        actions: Array.isArray(item.actions) ? item.actions : [],
    };

    notifications.value.push(notif);

    if (duration > 0 && duration < 600000) {
        const timer = setTimeout(() => {
            removeNotification(id);
        }, duration);
        activeTimers.set(id, timer);
    }
};

const getDefaultTitle = (type) => {
    switch (type) {
        case 'success':
            return 'Berhasil';
        case 'error':
            return 'Terjadi Kesalahan';
        case 'warning':
            return 'Peringatan';
        case 'info':
        default:
            return 'Informasi';
    }
};

const handleActionClick = (action, notificationId) => {
    if (action.action) {
        const method = (action.method || 'POST').toUpperCase();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch(action.action, {
            method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
            .then(() => {
                removeNotification(notificationId);
            })
            .catch((err) => {
                console.error('Notify Action Error:', err);
            });
    } else {
        removeNotification(notificationId);
    }
};

// Watch Inertia flash props
watch(
    () => page.props.flash,
    (flash) => {
        if (!flash) return;

        if (flash.notify) {
            addNotification(flash.notify);
        } else if (flash.success) {
            addNotification({
                type: 'success',
                model: 'toast',
                title: 'Berhasil',
                message: flash.success,
            });
        } else if (flash.error) {
            addNotification({
                type: 'error',
                model: 'toast',
                title: 'Terjadi Kesalahan',
                message: flash.error,
            });
        }
    },
    { deep: true, immediate: true }
);

// Event listener for client-side notifications
const onCustomNotify = (e) => {
    if (e.detail) {
        addNotification(e.detail);
    }
};

onMounted(() => {
    window.addEventListener('laravel-notify', onCustomNotify);
});

onUnmounted(() => {
    window.removeEventListener('laravel-notify', onCustomNotify);
    activeTimers.forEach((timer) => clearTimeout(timer));
    activeTimers.clear();
});
</script>

<template>
    <!-- Laravel Notify Container: Fixed at Top Right -->
    <aside
        aria-live="polite"
        class="pointer-events-none fixed top-4 right-4 z-[9999] flex flex-col gap-3 max-w-sm w-full px-4 sm:px-0"
    >
        <TransitionGroup
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-for="item in notifications"
                :key="item.id"
                class="pointer-events-auto w-full transition-all duration-200"
            >
                <!-- 1. TOAST MODEL (Default Laravel Notify Toast) -->
                <div
                    v-if="item.model === 'toast'"
                    class="overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/5 dark:bg-zinc-800 border-l-4"
                    :class="{
                        'border-emerald-500': item.type === 'success',
                        'border-rose-500': item.type === 'error',
                        'border-amber-500': item.type === 'warning',
                        'border-sky-500': item.type === 'info',
                    }"
                >
                    <div class="p-4">
                        <div class="flex items-start gap-3">
                            <!-- Icon -->
                            <div class="shrink-0 mt-0.5">
                                <span v-if="item.icon && item.icon.length <= 4" class="text-xl">
                                    {{ item.icon }}
                                </span>
                                <svg
                                    v-else-if="item.type === 'success'"
                                    class="size-5 text-emerald-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg
                                    v-else-if="item.type === 'error'"
                                    class="size-5 text-rose-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg
                                    v-else-if="item.type === 'warning'"
                                    class="size-5 text-amber-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                                <svg
                                    v-else
                                    class="size-5 text-sky-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <p v-if="item.title" class="text-sm font-semibold text-zinc-900 dark:text-white leading-tight">
                                    {{ item.title }}
                                </p>
                                <p v-if="item.message" class="text-xs text-zinc-600 dark:text-zinc-300 mt-0.5 leading-relaxed break-words">
                                    {{ item.message }}
                                </p>

                                <!-- Actions -->
                                <div v-if="item.actions.length > 0" class="mt-2.5 flex items-center gap-3">
                                    <template v-for="(act, aIdx) in item.actions" :key="aIdx">
                                        <a
                                            v-if="act.url"
                                            :href="act.url"
                                            :target="act.openUrlInNewTab ? '_blank' : '_self'"
                                            rel="noopener noreferrer"
                                            class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 cursor-pointer"
                                        >
                                            {{ act.label }}
                                        </a>
                                        <button
                                            v-else
                                            type="button"
                                            @click="handleActionClick(act, item.id)"
                                            class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 cursor-pointer"
                                        >
                                            {{ act.label }}
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Close Button -->
                            <button
                                type="button"
                                @click="removeNotification(item.id)"
                                class="shrink-0 p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 rounded-md transition-colors cursor-pointer"
                            >
                                <span class="sr-only">Tutup notifikasi</span>
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 2. CONNECTIFY MODEL -->
                <div
                    v-else-if="item.model === 'connect' || item.model === 'connectify'"
                    class="overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/5 dark:bg-zinc-800 border-t-4"
                    :class="item.type === 'error' ? 'border-rose-600' : 'border-emerald-600'"
                >
                    <div class="p-4 flex items-start gap-3">
                        <div
                            class="p-2 rounded-full text-white shrink-0"
                            :class="item.type === 'error' ? 'bg-gradient-to-r from-rose-600 to-rose-800' : 'bg-gradient-to-r from-emerald-600 to-emerald-800'"
                        >
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white leading-tight">
                                {{ item.title }}
                            </p>
                            <p v-if="item.message" class="text-xs text-zinc-600 dark:text-zinc-300 mt-0.5 break-words">
                                {{ item.message }}
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="removeNotification(item.id)"
                            class="p-1 text-zinc-400 hover:text-zinc-600 rounded-md cursor-pointer"
                        >
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- 3. DRAKIFY MODEL -->
                <div
                    v-else-if="item.model === 'drake' || item.model === 'drakify'"
                    class="h-32 relative rounded-xl overflow-hidden shadow-2xl ring-1 ring-black/10"
                >
                    <img
                        :src="item.type === 'error' ? '/vendor/mckenziearts/laravel-notify/images/drake-error.jpg' : '/vendor/mckenziearts/laravel-notify/images/drake-success.jpg'"
                        alt="Drake"
                        class="absolute inset-0 w-full h-full object-cover"
                    />
                    <div
                        class="absolute inset-0 opacity-70"
                        :class="item.type === 'error' ? 'bg-rose-600' : 'bg-emerald-600'"
                    ></div>
                    <div class="relative z-10 p-4 h-full flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="size-10 rounded-full bg-white flex items-center justify-center shadow-md">
                                <svg
                                    v-if="item.type === 'error'"
                                    class="size-6 text-rose-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2.5"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <svg
                                    v-else
                                    class="size-6 text-emerald-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2.5"
                                    stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </span>
                            <button
                                type="button"
                                @click="removeNotification(item.id)"
                                class="p-1 text-white hover:text-zinc-200 cursor-pointer"
                            >
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 4. SMILEY MODEL -->
                <div
                    v-else-if="item.model === 'smiley'"
                    class="overflow-hidden rounded-xl bg-white shadow-xl ring-1 ring-black/5 dark:bg-zinc-800 p-4"
                >
                    <div class="flex items-start gap-3">
                        <span class="text-2xl shrink-0">
                            {{ item.icon || (item.type === 'error' ? '🙅🏽‍♂' : '👍') }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white leading-tight">
                                {{ item.title }}
                            </p>
                            <p v-if="item.message" class="text-xs text-zinc-600 dark:text-zinc-300 mt-0.5 break-words">
                                {{ item.message }}
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="removeNotification(item.id)"
                            class="p-1 text-zinc-400 hover:text-zinc-600 rounded-md cursor-pointer"
                        >
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- 5. EMOTIFY MODEL -->
                <div
                    v-else-if="item.model === 'emotify'"
                    class="overflow-hidden rounded-xl shadow-xl ring-1 ring-black/10 p-4 text-white"
                    :class="item.type === 'error' ? 'bg-gradient-to-r from-orange-500 via-rose-500 to-rose-800' : 'bg-gradient-to-r from-teal-500 via-emerald-600 to-emerald-800'"
                >
                    <div class="flex items-start gap-3">
                        <div class="shrink-0">
                            <svg v-if="item.type === 'error'" class="size-10" fill="none" viewBox="0 0 40 40" stroke="currentColor">
                                <path d="M30.96 9.04c6.053 6.053 6.053 15.867 0 21.92-6.053 6.053-15.867 6.053-21.92 0-6.053-6.053-6.053-15.867 0-21.92 6.053-6.053 15.867-6.053 21.92 0z" fill="#F7F7F7" stroke="#E52828"/>
                                <path d="M11 14l4.05 2.01L11.032 18M29 18l-3.959-2.008L28.968 14" stroke="#E52828" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14 26s1.269-4 6.028-4C24.787 22 26 25.968 26 25.968" stroke="#E52828" stroke-miterlimit="10" stroke-linecap="round"/>
                            </svg>
                            <svg v-else class="size-10" fill="none" viewBox="0 0 40 40" stroke="currentColor">
                                <path d="M19.995 4.5A15.498 15.498 0 0 1 32.89 28.606 15.5 15.5 0 1 1 19.995 4.5z" fill="#F7F7F7" stroke="#137230" />
                                <path d="M14.232 22.382c-.253 0-.43.355-.332.673.5 1.667 2.171 5.858 6.318 5.858s5.859-4.227 6.37-5.908c.1-.323-.078-.678-.334-.678l-12.022.055z" fill="#034915" />
                                <path d="M20.153 32.27c2.492 0 4.512-1.496 4.512-3.34 0-1.846-2.02-3.342-4.512-3.342-2.493 0-4.513 1.496-4.513 3.341 0 1.846 2.02 3.341 4.513 3.341z" fill="#137230" />
                                <path d="M24.98 18.78s-.029-2.485 2.178-2.485c2.206 0 2.16 2.46 2.16 2.46m-18.634.026s-.029-2.486 2.18-2.486c2.208 0 2.16 2.466 2.16 2.466" stroke="#137230" stroke-miterlimit="10" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold leading-tight">
                                {{ item.title }}
                            </p>
                            <p v-if="item.message" class="text-xs text-white/90 mt-0.5 break-words">
                                {{ item.message }}
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="removeNotification(item.id)"
                            class="p-1 text-white hover:text-white/80 rounded-md cursor-pointer"
                        >
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </TransitionGroup>
    </aside>
</template>
