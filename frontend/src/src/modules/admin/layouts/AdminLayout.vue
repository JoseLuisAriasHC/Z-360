<template>
  <div class="layout-wrapper" :class="containerClass">
    <app-topbar @menu-toggle="onMenuToggle"></app-topbar>
    <div class="layout-sidebar" @click="onSidebarClick">
      <!-- <app-menu :model="menu" @menuitem-click="onMenuItemClick"></app-menu> -->
    </div>
    <div class="layout-main-container">
      <div class="layout-main">
        <router-view></router-view>
      </div>
    </div>
    <Toast />
    <div class="layout-mask" @click="onMaskClick"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import AppTopbar from '@admin/components/sakai/landing/TopbarWidget.vue'
// import AppMenu from '@admin/components/sakai/AppMenu.vue'

// Importar estilos de Sakai
import '@admin/assets/styles/layout.scss'

const layoutMode = ref('static')
const staticMenuInactive = ref(false)
const overlayMenuActive = ref(false)
const mobileMenuActive = ref(false)

const containerClass = computed(() => {
  return {
    'layout-theme-light': true,
    'layout-overlay': layoutMode.value === 'overlay',
    'layout-static': layoutMode.value === 'static',
    'layout-static-inactive': staticMenuInactive.value && layoutMode.value === 'static',
    'layout-overlay-active': overlayMenuActive.value,
    'layout-mobile-active': mobileMenuActive.value
  }
})

const menu = ref([
  {
    label: 'Home',
    items: [
      { label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/admin/dashboard' }
    ]
  },
  // ... más items del menú
])

const onMenuToggle = () => {
  if (window.innerWidth > 991) {
    staticMenuInactive.value = !staticMenuInactive.value
  } else {
    mobileMenuActive.value = !mobileMenuActive.value
  }
}

const onSidebarClick = () => {
  // Lógica del sidebar
}

const onMenuItemClick = () => {
  if (window.innerWidth <= 991) {
    mobileMenuActive.value = false
  }
}

const onMaskClick = () => {
  mobileMenuActive.value = false
}
</script>