import { createRouter, createWebHistory } from 'vue-router'
import adminRoutes from '@admin/router'
import webRoutes from '@web/router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/admin',
      children: adminRoutes
    },
    {
      path: '/',
      children: webRoutes
    }
  ]
})

// Guard global para proteger rutas de admin
router.beforeEach((to, from, next) => {
  const isAdminRoute = to.path.startsWith('/admin')
  const token = localStorage.getItem('auth_token')
  
  if (isAdminRoute && to.path !== '/admin/login' && !token) {
    next('/admin/login')
  } else {
    next()
  }
})

export default router