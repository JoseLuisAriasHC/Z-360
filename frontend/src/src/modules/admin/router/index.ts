import type { RouteRecordRaw } from 'vue-router'

const adminRoutes: RouteRecordRaw[] = [
  {
    path: 'login',
    name: 'admin-login',
    component: () => import('@admin/views/Login.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '',
    component: () => import('@admin/layouts/AdminLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/admin/dashboard'
      },
      // {
      //   path: 'dashboard',
      //   name: 'admin-dashboard',
      //   component: () => import('@admin/views/Dashboard.vue')
      // },
      // Más rutas de admin...
    ]
  }
]

export default adminRoutes