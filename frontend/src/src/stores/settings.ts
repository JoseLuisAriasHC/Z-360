import { defineStore } from 'pinia'
import { ref } from 'vue'
import { WebSettingService } from '@/modules/admin/services/WebSettingService';

export const useSettingsStore = defineStore('settings', () => {
  const envioSettings = ref<{
    coste_envio: number
    free_coste_envio_from: number
  } | null>(null)

  const loading = ref(false)
  const error = ref<string | null>(null)

  const loadEnvioSettings = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await WebSettingService.getEnvioSettings()
      envioSettings.value = {
        coste_envio: parseFloat(response.coste_envio.valor),
        free_coste_envio_from: parseFloat(response.free_coste_envio_from.valor)
      }
    } catch (err: any) {
      error.value = 'No se pudieron cargar los ajustes de envío'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  return { envioSettings, loading, error, loadEnvioSettings }
})
