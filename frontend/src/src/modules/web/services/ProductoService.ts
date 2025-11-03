import apiClient from '@/services/api';
import type { AxiosResponse } from 'axios';
import type { Color } from '@/modules/admin/services/ColorService';
import type { Marca } from '@/modules/admin/services/MarcaService';
import type { Etiqueta } from '@/modules/admin/services/EtiquetaService';

// Estructura principal del producto base (sin variantes)
export interface ProductoDetalleBase {
    id: number;
    nombre: string;
    marca: Marca;
    tipo: string;
    genero: string;
    descripcion: string;
    cierre: string;
    altura_suela: string;
    plantilla: string;
}

// Estructura de detalles técnicos
export interface DetallesTecnicos {
    parte_superior: string;
    parte_inferior: string;
    suela: string;
    cuidados: string;
}

// Estructura de la variante seleccionada (similar a la de listado, pero con más info de precios)
export interface VarianteDetalle {
    id: number;
    color: Color;
    precio: number;
    precio_sin_iva: number;
    iva: number;
    descuento: number;
    descuento_activo: boolean;
    precio_con_descuento: number;
    imagen_principal: string | null;
    imagenes_adicionales: string[];
}

// Estructura de las variantes disponibles para selección
export interface VarianteDisponible {
    id: number;
    color: Color;
    imagen_principal: string | null;
    seleccionada: boolean;
}

// Estructura de las tallas con stock
export interface TallaStock {
    id: number;
    talla: {
        id: number;
        numero: string;
    };
    stock: number;
    disponible: boolean;
    sku: string;
}

// Estructura de la información de envío
export interface InfoEnvio {
    costo_envio: number;
    envio_gratis_desde: number;
    mensaje: string;
}

// Estructura de la sección 'data' de la respuesta
export interface ProductoDetalleData {
    producto: ProductoDetalleBase;
    detalles: DetallesTecnicos;
    etiquetas: Etiqueta[];
    variante_seleccionada: VarianteDetalle;
    variantes: VarianteDisponible[];
    tallas: TallaStock[];
    envio: InfoEnvio;
}

// Estructura de la respuesta completa del detalle
export interface ProductoDetalleResponseWrapper {
    success: boolean;
    data: ProductoDetalleData;
}

export const ProductoService = {
    /**
     * Hace la llamada GET a la API para obtener el detalle completo de un producto
     * en base al ID de una de sus variantes.
     * * RUTA: api/products/{productVariantId}
     *
     * @param productVariantId El ID de la variante del producto a obtener.
     * @returns Una promesa que resuelve con los detalles completos del producto.
     */
    async getProductoDetalle(productVariantId: number): Promise<ProductoDetalleData> {
        try {
            // Especificamos el tipo de respuesta esperado (ProductoDetalleResponseWrapper)
            const response: AxiosResponse<ProductoDetalleResponseWrapper> = await apiClient.get(`products/${productVariantId}`);

            // Devolvemos solo el objeto 'data' que contiene toda la información del producto.
            if (response.data.success) {
                return response.data.data;
            } else {
                 // Manejo de caso donde la API devuelve success: false
                throw new Error('La API devolvió un estado de error para el detalle del producto.');
            }
        } catch (error) {
            console.error(`Error al cargar el detalle del producto variante ${productVariantId}:`, error);
            throw error;
        }
    }
};
