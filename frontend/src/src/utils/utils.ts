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
