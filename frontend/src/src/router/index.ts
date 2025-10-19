import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router';
import adminRoutes from '@admin/router';
import authService from '@/services/authService';
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
    const requiresAuthAdmin = to.meta.requiresAuthAdmin as boolean | undefined;
    const requiresAuthUser = to.meta.requiresAuthUser as boolean | undefined;
    const isAuthenticated = !!localStorage.getItem('auth_token');

    if (requiresAuthAdmin) {
        if (!isAuthenticated) {
            next({ name: 'admin-login' });
            return;
        }
        const isAdminUser = authService.isAdmin();
        if (!isAdminUser) {
            next({ name: 'admin-access-denied' });
            return;
        }
    }

    if (requiresAuthAdmin === false && to.name === 'admin-login') {
        next({ name: 'admin-dashboard' });
        return;
    }

    if (requiresAuthUser) {
        if (!isAuthenticated) {
            next({ name: 'login' });
            return;
        }
        if (isAuthenticated && to.name === 'login') {
            next({ name: 'dashboard' });
            return;
        }
    }

    next();
});

export default router;
