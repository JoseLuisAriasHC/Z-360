<template>
  <div class="tw-min-h-screen tw-flex tw-flex-col">
    <header class="tw-bg-white tw-shadow">
      <nav class="tw-container tw-mx-auto tw-px-4 tw-py-4">
        <div class="tw-flex tw-justify-between tw-items-center">
          <router-link to="/" class="tw-text-2xl tw-font-bold">
            Logo
          </router-link>
          <div class="tw-space-x-4">
            <router-link to="/" class="tw-text-gray-700 hover:tw-text-blue-600">
              Inicio
            </router-link>
            <router-link to="/about" class="tw-text-gray-700 hover:tw-text-blue-600">
              Nosotros
            </router-link>
          </div>
        </div>
      </nav>
    </header>
    
    <main class="tw-flex-grow">
      <router-view></router-view>
    </main>
    
    <footer class="tw-bg-gray-800 tw-text-white tw-py-8">
      <div class="tw-container tw-mx-auto tw-px-4 tw-text-center">
        <p>&copy; 2025 Tu Empresa. Todos los derechos reservados.</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
/* Estilos personalizados sin Tailwind si es necesario */
</style>