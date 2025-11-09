import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import type { Cupon } from '@/modules/web/services/CuponService'

export const useCuponStore = defineStore('cupon', () => {
  const cupon = ref<Cupon | null>(null)

  const stored = localStorage.getItem('cupon')
  if (stored) {
    try {
      cupon.value = JSON.parse(stored)
    } catch (e) {
      console.warn('Error al leer el cupón del localStorage', e)
      localStorage.removeItem('cupon')
    }
  }

  watch(
    cupon,
    (newCupon) => {
      if (newCupon) {
        localStorage.setItem('cupon', JSON.stringify(newCupon))
      } else {
        localStorage.removeItem('cupon')
      }
    },
    { deep: true }
  )

  const descuento = computed(() => {
    if (!cupon.value) return 0
    return cupon.value.descuento
  })

  const tipo = computed(() => cupon.value?.tipo ?? 'fijo')

  const clearCupon = () => {
    cupon.value = null
  }

  return {
    cupon,
    descuento,
    tipo,
    clearCupon,
  }
})
