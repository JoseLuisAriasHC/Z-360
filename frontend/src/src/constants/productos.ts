// --- Constantes de Arrays (fuente de verdad) ---

export const GENEROS_VALORES = ['hombre', 'mujer', 'unisex'] as const;
export const TIPOS_VALORES = ['urbanas', 'deportivas', 'botas', 'sandalias'] as const;
export const CIERRE_VALORES = ['Cordones', 'Velcro', 'Cierre rapido', 'Zipper', 'Slip-on'] as const;
export const ALTURA_SUELA_VALORES = ['baja', 'alta', 'media'] as const;

// Criterios de busqueda
export const CRITERIOS_ESPECIALES_VALORES = ['ofertas', 'top-ventas', 'novedades'] as const;
export type CriterioEspecial = (typeof CRITERIOS_ESPECIALES_VALORES)[number];
export type CriterioBusqueda = Tipo | CriterioEspecial;


export type Genero = (typeof GENEROS_VALORES)[number];
export type Tipo = (typeof TIPOS_VALORES)[number];
export type Cierre = (typeof CIERRE_VALORES)[number];
export type AlturaSuela = (typeof ALTURA_SUELA_VALORES)[number];

// --- Mapeo de Severidad ---

type SeverableValue = Genero | Tipo | Cierre;

export const SEVERITY_MAP: Record<SeverableValue, string> = {
    // === primary ===
    hombre: 'primary', // Las comillas son opcionales si no hay espacios
    urbanas: 'primary',
    Cordones: 'primary',

    // === success ===
    mujer: 'success',
    deportivas: 'success',
    Velcro: 'success',

    // === info ===
    unisex: 'info',
    botas: 'info',
    'Cierre rapido': 'info', // Las comillas son necesarias por el espacio

    // === danger ===
    sandalias: 'danger',
    Zipper: 'danger',

    // === warn ===
    'Slip-on': 'warn',
};
