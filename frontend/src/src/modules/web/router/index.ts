import type { RouteRecordRaw } from 'vue-router'

const webRoutes: RouteRecordRaw[] = [
  {
    path: '',
    component: () => import('@web/layouts/WebLayout.vue'),
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('@web/views/Home.vue')
      },
      {
        path: 'about',
        name: 'about',
        component: () => import('@web/views/About.vue')
      },
      // Más rutas web...
    ]
  }
]

export default webRoutes