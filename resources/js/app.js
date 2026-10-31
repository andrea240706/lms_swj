import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import LaravelNotify from '@/Components/Notification/LaravelNotify.vue';
import { notify } from '@/Utils/notify';

window.notify = notify;

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({
            render: () =>
                h('div', { class: 'contents' }, [
                    h(App, props),
                    h(LaravelNotify),
                ]),
        });

        app.use(plugin);
        app.use(ZiggyVue);
        app.config.globalProperties.$notify = notify;

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
