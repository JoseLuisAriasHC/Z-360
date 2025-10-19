
export const ROLES = {
    ADMIN: 'admin',
    CLIENTE: 'cliente',
} as const;

export type UserRole = typeof ROLES[keyof typeof ROLES];