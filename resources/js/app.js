import './bootstrap';

import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import Aura from '@primeuix/themes/aura';
import PrimeVue from 'primevue/config';
import registerPrimeVueComponents from './plugins/primevue';
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';
//import { useColorMode } from '@vueuse/core';

import '../assets/styles.scss';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
        .use(plugin)
        .use(ZiggyVue, Ziggy)
        .use(PrimeVue, {
            theme: {
                preset: Aura,
                options: {
                    darkModeSelector: '.app-dark'
                }
            }
        })
        .use(ToastService)
        .use(ConfirmationService)
        .component('InertiaHead', Head)
        .component('InertiaLink', Link)

        registerPrimeVueComponents(app); 
        return app.mount(el);
    },
    progress: {
        color: 'var(--p-primary-500)',
        //color: '#4B5563',
    },
});
