import AppLayout from '@admin/layout/AppLayout.vue';
import type { RouteRecordRaw } from 'vue-router';

const adminRoutes: RouteRecordRaw[] = [
    {
        path: 'login',
        name: 'admin-login',
        component: () => import('@admin/views/pages/auth/Login.vue'),
        meta: { requiresAuth: false },
    },
    {
        path: '/',
        component: AppLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                redirect: '/admin/dashboard',
            },
        ],
    },
];

export default adminRoutes;
