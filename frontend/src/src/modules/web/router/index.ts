import type { RouteRecordRaw } from 'vue-router';
import AppLayout from '@web/layout/AppLayout.vue';

const webRoutes: RouteRecordRaw[] = [
    {
        path: '',
        component: AppLayout,
        children: [
            {
                path: 'home',
                name: 'home',
                component: () => import('@web/views/Home.vue'),
            },
            {
                path: ':catchAll(.*)*',
                name: 'not-found',
                component: () => import('@web/views/NotFound.vue'),
            },
        ],
    },
];

export default webRoutes;
