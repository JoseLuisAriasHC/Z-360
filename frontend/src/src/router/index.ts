import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'
import adminRoutes from '@admin/router';
const typedAdminRoutes: RouteRecordRaw[] = adminRoutes as unknown as RouteRecordRaw[];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/admin',
            children: typedAdminRoutes,
        },
        // {
        //     path: '/',
        //     children: webRoutes,
        // },
    ],
});

// Guard global para proteger rutas de admin
router.beforeEach((to, from, next) => {
    const isAdminRoute = to.path.startsWith('/admin');
    const token = localStorage.getItem('auth_token');

    if (isAdminRoute && to.path !== '/admin/login' && !token) {
        next('/admin/login');
    } else {
        next();
    }
});

export default router;
