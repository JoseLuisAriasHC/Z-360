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

    // Protección de rutas de admin que requieren autenticación
    if (requiresAuthAdmin === true) {
        if (!isAuthenticated) {
            if (to.name !== 'admin-login') {
                next({ name: 'admin-login' });
                return;
            }
            next();
            return;
        }
        
        const isAdminUser = authService.isAdmin();
        if (!isAdminUser) {
            if (to.name !== 'admin-access-denied') {
                next({ name: 'admin-access-denied' });
                return;
            }
            next();
            return;
        }
    }

    // Si el usuario está autenticado y va a login, redirigir al dashboard
    if (requiresAuthAdmin === false && isAuthenticated && to.name === 'admin-login') {
        next({ name: 'admin-dashboard' });
        return;
    }

    // Protección de rutas de usuario
    if (requiresAuthUser === true) {
        if (!isAuthenticated) {
            if (to.name !== 'login') {
                next({ name: 'login' });
                return;
            }
            next();
            return;
        }
    }
    next();
});
export default router;
