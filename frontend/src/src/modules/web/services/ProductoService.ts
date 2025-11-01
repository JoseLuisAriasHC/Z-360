import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';
import type { Color } from '@/modules/admin/services/ColorService';
import type { Marca } from '@/modules/admin/services/MarcaService';
import { type Genero, type CriterioEspecial, type CriterioBusqueda, CRITERIOS_ESPECIALES_VALORES } from '@/constants/productos';
import type { Etiqueta } from '@/modules/admin/services/EtiquetaService';

export interface VarianteProducto {
    id: number;
    color: Color;
    precio: number;
    precio_sin_iva: number;
    descuento: number;
    descuento_activo: boolean;
    precio_con_descuento: number;
    imagen_principal: string;
    tiene_stock: boolean;
}

export interface Producto {
    id: number;
    nombre: string;
    genero: string;
    tipo: string;
    marca: Marca;
    etiquetas: Etiqueta[];
    variants: VarianteProducto[];
}

export interface Pagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    has_more_pages: boolean;
}

export interface ProductsResponseWrapper {
    success: boolean;
    data: Producto[];
    pagination: Pagination;
}

const isSpecialCriterion = (criterio: CriterioBusqueda): criterio is CriterioEspecial => {
    // TIPOS_PRODUCTO_VALORES es un 'as const', por eso TypeScript sabe que es un array de literales
    return (CRITERIOS_ESPECIALES_VALORES as ReadonlyArray<string>).includes(criterio);
};

export const ProductService = {
    /**
     * Hace la llamada GET a la API para obtener el listado de productos,
     * adaptando la ruta y los parámetros según el criterio de búsqueda.
     *
     * @param genero El género del producto ('hombre', 'mujer', 'unisex').
     * @param criterioBusqueda El criterio de búsqueda ('urbanas', 'ofertas', 'top-ventas', etc.).
     * @returns Una promesa que resuelve con la lista de productos y la paginación.
     */
    async getProducts(
        genero: Genero,
        criterioBusqueda: CriterioBusqueda,
        page: number = 1 // Parámetro opcional para la paginación
    ): Promise<ProductsResponseWrapper> {
        let endpoint: string;
        let params: { [key: string]: string | number } = { page };

        if (isSpecialCriterion(criterioBusqueda)) {
            // Caso 1: Criterio especial (Rutas directas: /ofertas, /novedades, /top-ventas)
            // La ruta ya define el criterio. Añadimos el género como filtro.
            endpoint = `products/${criterioBusqueda}`;
            params.genero = genero;
        } else {
            // Caso 2: Búsqueda por listado (Ruta: /listado?genero=X&tipo_producto=Y)
            // El criterio es un TipoProducto (urbanas, deportivas, etc.)
            endpoint = 'products/listado';
            params.genero = genero;
            params.tipo_producto = criterioBusqueda; // TypeScript sabe que es TipoProducto aquí
        }

        try {
            const response: AxiosResponse<ProductsResponseWrapper> = await apiClient.get(endpoint, {
                params: params,
            });

            return response.data;
        } catch (error) {
            console.error(`Error al cargar productos para ${genero} - ${criterioBusqueda}:`, error);
            throw error;
        }
    },
};
