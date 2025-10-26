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
                path: 'productos',
                name: 'admin-productos',
                component: () => import('@admin/views/Productos.vue'),
            },
            {
                path: 'productos/new',
                name: 'admin-productos-new',
                component: () => import('@admin/views/ProductoForm.vue'),
            },
            {
                path: 'productos/:id',
                name: 'admin-productos-edit',
                component: () => import('@admin/views/ProductoForm.vue'),
                props: true,
            },
            {
                path: 'variantes-producto/new',
                name: 'admin-productos-variantes-new',
                component: () => import('@admin/views/VariantesForm.vue'),
            },
            {
                path: 'variantes-producto/:id',
                name: 'admin-productos-variantes-edit',
                component: () => import('@admin/views/VariantesForm.vue'),
                props: true,
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
            },
            {
                path: 'marcas/:id',
                name: 'admin-marcas-edit',
                component: () => import('@admin/views/MarcaForm.vue'),
                props: true,
            },
            {
                path: 'etiquetas',
                name: 'admin-etiquetas',
                component: () => import('@admin/views/Etiquetas.vue'),
            },
            {
                path: 'etiquetas/new',
                name: 'admin-etiquetas-new',
                component: () => import('@admin/views/EtiquetaForm.vue'),
            },
            {
                path: 'etiquetas/:id',
                name: 'admin-etiquetas-edit',
                component: () => import('@admin/views/EtiquetaForm.vue'),
                props: true,
            },
            {
                path: 'tipos-uso',
                name: 'admin-tipos-uso',
                component: () => import('@admin/views/TiposUso.vue'),
            },
            {
                path: 'tipos-uso/new',
                name: 'admin-tipos-uso-new',
                component: () => import('@admin/views/TiposUsoForm.vue'),
            },
            {
                path: 'tipos-uso/:id',
                name: 'admin-tipos-uso-edit',
                component: () => import('@admin/views/TiposUsoForm.vue'),
                props: true,
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
            },
            {
                path: 'tallas/:id',
                name: 'admin-tallas-edit',
                component: () => import('@admin/views/TallaForm.vue'),
                props: true,
            },
            {
                path: 'colores',
                name: 'admin-colores',
                component: () => import('@admin/views/Colores.vue'),
            },
            {
                path: 'colores/new',
                name: 'admin-colores-new',
                component: () => import('@admin/views/ColorForm.vue'),
            },
            {
                path: 'colores/:id',
                name: 'admin-colores-edit',
                component: () => import('@admin/views/ColorForm.vue'),
                props: true,
            },
            {
                path: 'cupones',
                name: 'admin-cupones',
                component: () => import('@admin/views/Cupones.vue'),
            },
            {
                path: 'cupones/new',
                name: 'admin-cupones-new',
                component: () => import('@admin/views/CuponForm.vue'),
            },
            {
                path: 'cupones/:id',
                name: 'admin-cupones-edit',
                component: () => import('@admin/views/CuponForm.vue'),
                props: true,
            },
            {
                path: 'parametros-generales',
                name: 'admin-parametros-generales',
                component: () => import('@admin/views/ParametrosGenerales.vue'),
            },
        ],
    },
    {
        path: ':catchAll(.*)*',
        name: 'admin-not-found',
        component: () => import('@admin/views/NotFound.vue'),
    },
];

export default adminRoutes;
