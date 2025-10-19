// Función genérica para devolver un valor seguro (no nulo/indefinido),
// usando un valor por defecto si el valor original es nulo o indefinido.
export const safeValue = <T>(value: T | null | undefined, defaultValue: T): T => value ?? defaultValue;

// Función específica para asegurar que un número sea devuelto (o 0 por defecto).
export const safeNumber = (value: number | null | undefined): number => safeValue(value, 0);
