export const GENEROS_VALORES = ['hombre', 'mujer', 'unisex'];
export type Genero = (typeof GENEROS_VALORES)[number];

export const TIPOS_VALORES = ['urbanas', 'deportivas', 'botas', 'sandalias'];
export type Tipo = (typeof TIPOS_VALORES)[number];

export const CIERRE_VALORES = ['Cordones', 'Velcro', 'Cierre rapido', 'Zipper', 'Slip-on'];
export type Cierre = (typeof CIERRE_VALORES)[number];


type SeverableValue = Genero | Tipo | Cierre;

export const SEVERITY_MAP: Record<SeverableValue, string> = {
    // === primary ===
    hombre: 'primary',
    urbanas: 'primary',
    Cordones: 'primary',

    // === success ===
    mujer: 'success',
    deportivas: 'success',
    Velcro: 'success',

    // === info ===
    unisex: 'info',
    botas: 'info',
    'Cierre rapido': 'info',

    // === danger ===
    sandalias: 'danger',
    Zipper: 'danger',

    // === warn ===
    'Slip-on': 'warn',
};
