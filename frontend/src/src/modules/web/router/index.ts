import type { RouteRecordRaw } from 'vue-router';
import AppLayout from '@web/layout/AppLayout.vue';

const webRoutes: RouteRecordRaw[] = [
    {
        path: '',
        component: AppLayout,
        children: [
            {
                path: '',
                name: 'home',
                component: () => import('@web/views/Home.vue'),
            },
            {
                path: 'carrito',
                name: 'carrito',
                component: () => import('@web/views/Carrito.vue'),
            },
            {
                path: 'productos/:genero/:criterioBusqueda',
                name: 'productos',
                component: () => import('@web/views/Productos.vue'),
            },
            {
                path: 'productos/:id',
                name: 'producto-detalles',
                component: () => import('@web/views/Producto.vue'),
                props: true,
            },
            {
                path: 'precheckout',
                name: 'precheckout',
                component: () => import('@web/views/Precheckout.vue'),
            },
            {
                path: 'checkout',
                name: 'checkout',
                component: () => import('@web/views/Checkout.vue'),
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
