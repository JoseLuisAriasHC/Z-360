import '@/assets/styles.scss';

import { createApp } from 'vue';
import { createPinia } from 'pinia';

import App from './App.vue';
import router from './router';
// import { setupAdminModule } from '@admin/main'

import Aura from '@primeuix/themes/aura';
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';

const app = createApp(App);


app.use(createPinia());
app.use(router);

app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            darkModeSelector: '.app-dark',
        },
    },
});
app.use(ToastService);

app.mount('#app');
