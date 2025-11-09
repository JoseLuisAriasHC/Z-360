import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export interface DatosEnvio {
  envio_nombre: string
  envio_email: string
  envio_telefono: string
  envio_direccion_calle: string
  envio_direccion_numero_calle: string
  envio_direccion_piso_info: string
  envio_direccion_ciudad: string
  envio_direccion_cp: string
  usar_misma_direccion_facturacion: boolean
}

export interface DatosFacturacion {
  facturacion_nombre: string
  facturacion_email: string
  facturacion_telefono: string
  facturacion_direccion_calle: string
  facturacion_direccion_numero_calle: string
  facturacion_direccion_piso_info: string
  facturacion_direccion_ciudad: string
  facturacion_direccion_cp: string
}

export const useCheckoutStore = defineStore('checkout', () => {
  const envio = ref<DatosEnvio>({
    envio_nombre: '',
    envio_email: '',
    envio_telefono: '',
    envio_direccion_calle: '',
    envio_direccion_numero_calle: '',
    envio_direccion_piso_info: '',
    envio_direccion_ciudad: '',
    envio_direccion_cp: '',
    usar_misma_direccion_facturacion: true,
  })

  const facturacion = ref<DatosFacturacion>({
    facturacion_nombre: '',
    facturacion_email: '',
    facturacion_telefono: '',
    facturacion_direccion_calle: '',
    facturacion_direccion_numero_calle: '',
    facturacion_direccion_piso_info: '',
    facturacion_direccion_ciudad: '',
    facturacion_direccion_cp: '',
  })

  // Persistencia local
  const stored = localStorage.getItem('checkout')
  if (stored) {
    try {
      const parsed = JSON.parse(stored)
      envio.value = parsed.envio ?? envio.value
      facturacion.value = parsed.facturacion ?? facturacion.value
    } catch (e) {
      console.warn('Error al leer checkout del localStorage', e)
    }
  }

  watch([envio, facturacion], ([newEnvio, newFact]) => {
    localStorage.setItem('checkout', JSON.stringify({ envio: newEnvio, facturacion: newFact }))
  }, { deep: true })

  const clearCheckout = () => {
    envio.value = {
      envio_nombre: '',
      envio_email: '',
      envio_telefono: '',
      envio_direccion_calle: '',
      envio_direccion_numero_calle: '',
      envio_direccion_piso_info: '',
      envio_direccion_ciudad: '',
      envio_direccion_cp: '',
      usar_misma_direccion_facturacion: true,
    }
    facturacion.value = {
      facturacion_nombre: '',
      facturacion_email: '',
      facturacion_telefono: '',
      facturacion_direccion_calle: '',
      facturacion_direccion_numero_calle: '',
      facturacion_direccion_piso_info: '',
      facturacion_direccion_ciudad: '',
      facturacion_direccion_cp: '',
    }
    localStorage.removeItem('checkout')
  }

  return { envio, facturacion, clearCheckout }
})
