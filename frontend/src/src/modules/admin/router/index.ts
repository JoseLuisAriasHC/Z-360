import AppLayout from '@admin/layout/AppLayout.vue';
import type { RouteRecordRaw } from 'vue-router';

const adminRoutes: RouteRecordRaw[] = [
    {
        path: 'login',
        name: 'admin-login',
        component: () => import('@admin/views/auth/Login.vue'),
        meta: { requiresAuthAdmin: false },
    },
    {
        path: 'access',
        name: 'admin-access-denied',
        component: () => import('@admin/views/auth/Access.vue'),
        meta: { requiresAuth: false },
    },
    {
        path: '',
        component: AppLayout,
        meta: { requiresAuthAdmin: true },
        children: [
            {
                path: '',
                redirect: { name: 'admin-dashboard' },
            },
            {
                path: 'dashboard',
                name: 'admin-dashboard',
                component: () => import('@admin/views/Dashboard.vue'),
            },
        ],
    },
];

export default adminRoutes;
