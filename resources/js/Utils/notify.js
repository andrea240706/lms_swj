/**
 * Event-driven notification helper compatible with Laravel Notify models:
 * toast, connectify, drakify, smiley, emotify
 */

let notifyIdCounter = 0;

export const notify = {
    show(options) {
        if (typeof options === 'string') {
            options = {
                message: options,
                type: 'success',
                title: 'Berhasil',
                model: 'toast',
            };
        }

        const notification = {
            id: ++notifyIdCounter,
            type: options.type || 'info',
            model: options.model || 'toast',
            title: options.title ?? null,
            message: options.message ?? null,
            icon: options.icon ?? null,
            duration: typeof options.duration === 'number' ? options.duration : 5000,
            actions: Array.isArray(options.actions) ? options.actions : [],
            ...options,
        };

        if (typeof window !== 'undefined') {
            window.dispatchEvent(
                new CustomEvent('laravel-notify', {
                    detail: notification,
                })
            );
        }

        return notification;
    },

    success(message, title = 'Berhasil', options = {}) {
        return this.show({
            type: 'success',
            title,
            message,
            model: 'toast',
            ...options,
        });
    },

    error(message, title = 'Terjadi Kesalahan', options = {}) {
        return this.show({
            type: 'error',
            title,
            message,
            model: 'toast',
            ...options,
        });
    },

    warning(message, title = 'Peringatan', options = {}) {
        return this.show({
            type: 'warning',
            title,
            message,
            model: 'toast',
            ...options,
        });
    },

    info(message, title = 'Informasi', options = {}) {
        return this.show({
            type: 'info',
            title,
            message,
            model: 'toast',
            ...options,
        });
    },

    connectify(message, title = 'Koneksi Terhubung', type = 'success') {
        return this.show({
            type,
            title,
            message,
            model: 'connectify',
        });
    },

    drakify(type = 'success') {
        return this.show({
            type,
            model: 'drakify',
        });
    },

    smiley(message, title = 'Berhasil', type = 'success') {
        return this.show({
            type,
            title,
            message,
            model: 'smiley',
        });
    },

    emotify(message, title = 'Berhasil', type = 'success') {
        return this.show({
            type,
            title,
            message,
            model: 'emotify',
        });
    },
};

if (typeof window !== 'undefined') {
    window.notify = notify;
}
