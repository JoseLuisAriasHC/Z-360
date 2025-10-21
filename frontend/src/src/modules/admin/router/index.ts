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
            {
                path: 'profile',
                name: 'admin-profile',
                component: () => import('@admin/views/Profile.vue'),
            },
            {
                path: 'marcas',
                name: 'admin-marcas',
                component: () => import('@admin/views/Marcas.vue'),
            },
            {
                path: 'marcas/new',
                name: 'admin-marcas-new',
                component: () => import('@admin/views/MarcaForm.vue'),
                meta: { requiresAuthAdmin: true, title: 'Crear Marca' },
            },
            {
                path: 'marcas/:id',
                name: 'admin-marcas-edit',
                component: () => import('@admin/views/MarcaForm.vue'),
                props: true,
                meta: { requiresAuthAdmin: true, title: 'Editar Marca' },
            },
            {
                path: 'tallas',
                name: 'admin-tallas',
                component: () => import('@admin/views/Tallas.vue'),
            },
            {
                path: 'tallas/new',
                name: 'admin-tallas-new',
                component: () => import('@admin/views/TallaForm.vue'),
                meta: { requiresAuthAdmin: true, title: 'Crear Talla' },
            },
            {
                path: 'tallas/:id',
                name: 'admin-tallas-edit',
                component: () => import('@admin/views/TallaForm.vue'),
                props: true,
                meta: { requiresAuthAdmin: true, title: 'Editar Talla' },
            },
        ],
    },
    {
        path: ':catchAll(.*)',
        name: 'admin-not-found',
        component: () => import('@admin/views/NotFound.vue'),
    },
];

export default adminRoutes;
