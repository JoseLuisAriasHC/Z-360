import type { App } from 'vue'
import PrimeVue from 'primevue/config'
import Aura from '@primevue/themes/aura'

// Importar componentes PrimeVue que usarás
import Button from 'primevue/button'
import InputText from 'primevue/inputtext'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import Toast from 'primevue/toast'
import ToastService from 'primevue/toastservice'
// ... más componentes según necesites

// Estilos de PrimeVue
import 'primeicons/primeicons.css'

export function setupAdminModule(app: App) {
  app.use(PrimeVue, {
    theme: {
      preset: Aura,
      options: {
        darkModeSelector: '.app-dark',
        cssLayer: {
          name: 'primevue',
          order: 'tailwind-base, primevue, tailwind-utilities'
        }
      }
    }
  })
  
  app.use(ToastService)
  
  // Registrar componentes globalmente para el admin
  app.component('Button', Button)
  app.component('InputText', InputText)
  app.component('DataTable', DataTable)
  app.component('Column', Column)
  app.component('Toast', Toast)
  // ... más componentes
}