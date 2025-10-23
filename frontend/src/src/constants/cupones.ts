export const TIPOS_VALORES = ['porcentaje', 'fijo'];
export type Tipo = (typeof TIPOS_VALORES)[number];

export const SEVERITY_MAP: Record<Tipo, string> = {
    fijo: 'success',
    porcentaje: 'info',
};
