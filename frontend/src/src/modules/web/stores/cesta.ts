import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';

export interface CestaItem {
    id: number;
    nombre: string;
    marca: string;
    idTalla: number;
    talla: string;
    imagen: string;
    precio: number;
    cantidad: number;
    color: string;
    descuento: number;
    descuento_activo: boolean;
    precio_con_descuento: number;
}

export const useCestaStore = defineStore('cesta', () => {
    const items = ref<CestaItem[]>([]);

    const stored = localStorage.getItem('cesta');
    if (stored) {
        try {
            items.value = JSON.parse(stored);
        } catch (e) {
            console.warn('Error al leer la cesta del localStorage', e);
            localStorage.removeItem('cesta');
        }
    }

    watch(
        items,
        (newItems) => {
            localStorage.setItem('cesta', JSON.stringify(newItems));
        },
        { deep: true }
    );

    const addProducto = (producto: CestaItem) => {
        const existente = items.value.find((i) => i.id === producto.id && i.talla === producto.talla);
        if (existente) {
            existente.cantidad += producto.cantidad;
        } else {
            items.value.push(producto);
        }
    };

    const incrementarCantidad = (producto: CestaItem) => {
        if (producto) {
            producto.cantidad++;
        }
    };

    const decrementarCantidad = (producto: CestaItem) => {
        if (producto && producto.cantidad > 1) {
            producto.cantidad--;
        } else if (producto && producto.cantidad === 1) {
            removeProducto(producto.id, producto.talla);
        }
    };

    const removeProducto = (id: number, talla: string) => {
        items.value = items.value.filter((i) => !(i.id === id && i.talla === talla));
    };

    const clearCesta = () => {
        items.value = [];
    };

    const total = computed(() => items.value.reduce((acc, i) => acc + i.precio_con_descuento * i.cantidad, 0));
    const totalItems = computed(() => items.value.reduce((acc, i) => acc + i.cantidad, 0));

    return {
        items,
        addProducto,
        incrementarCantidad,
        decrementarCantidad,
        removeProducto,
        clearCesta,
        total,
        totalItems,
    };
});
