import type { App } from 'vue';
import Aura from '@primeuix/themes/aura';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';

import '@admin/assets/styles.scss';

export function setupAdminModule(app: App) {
    app.use(PrimeVue, {
        theme: {
            preset: Aura,
            options: {
                darkModeSelector: '.app-dark',
            },
        },
    });
    app.use(ConfirmationService);
    app.use(ToastService);
}

