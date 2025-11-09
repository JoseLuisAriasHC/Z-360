import { computed } from 'vue';
import { useRoute } from 'vue-router';
import noImageSvg from '@/assets/img/no-image.svg';

// Función genérica para devolver un valor seguro (no nulo/indefinido),
// usando un valor por defecto si el valor original es nulo o indefinido.
export const safeValue = <T>(value: T | null | undefined, defaultValue: T): T => value ?? defaultValue;

// Función específica para asegurar que un número sea devuelto (o 0 por defecto).
export const safeNumber = (value: number | null | undefined): number => safeValue(value, 0);

// Mostrar en las tablas la fecha con un formato mas limpio
export const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};

export const getParamId = () => {
    return computed<number | null>(() => {
        const route = useRoute();
        const idParam = route.params.id;
        return Array.isArray(idParam) ? null : idParam ? parseInt(idParam as string) : null;
    });
};
export const handleImageError = (event: Event) => {
    const imgElement = event.target as HTMLImageElement;
    imgElement.onerror = null;
    imgElement.src = noImageSvg;
};

/**
 * Formatea una fecha a una cadena ISO 8601 (YYYY-MM-DD HH:MM:SS).
 * Utiliza este formato para compatibilidad si el backend lo requiere,
 * aunque se recomienda enviar la cadena ISO completa.
 * @param fecha - Objeto Date a formatear.
 * @returns Cadena de fecha y hora formateada.
 */
export const formatDateForForm = (fecha: Date | null | undefined): string => {
    if (fecha instanceof Date && !isNaN(fecha.getTime())) {
        // Genera el formato no estándar YYYY-MM-DD HH:MM:SS
        return fecha.toISOString().slice(0, 19).replace('T', ' ');
    }
    return '';
};

/**
 * Genera una cadena ISO 8601 completa (con Z), el formato más seguro para APIs.
 * @param fecha - Objeto Date a formatear.
 * @returns Cadena ISO 8601 completa.
 */
export const toISOString = (fecha: Date | null | undefined): string => {
    if (fecha instanceof Date && !isNaN(fecha.getTime())) {
        return fecha.toISOString();
    }
    return '';
};

export const formatPrice = (price: number) => {
    return price.toFixed(2);
};
