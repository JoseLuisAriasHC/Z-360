# Etapa 1: Build
FROM node:22-alpine AS builder

WORKDIR /app

# Copiar archivos de dependencias
COPY src/package*.json ./

# Instalar dependencias 
RUN npm ci

# Copiar código fuente
COPY src/. .

# Pasar variables a vite
ARG VITE_API_URL
ARG VITE_ENV
ARG VITE_STORAGE_URL

ENV VITE_API_URL=$VITE_API_URL
ENV VITE_ENV=$VITE_ENV
ENV VITE_STORAGE_URL=$VITE_STORAGE_URL

# Build de producción
RUN npm run build

# Etapa 2: Servidor Nginx
FROM nginx:alpine

# Copiar archivos compilados desde la etapa de build
COPY --from=builder /app/dist /usr/share/nginx/html

# Copiar configuración personalizada de Nginx
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Exponer puerto 80
EXPOSE 80

# Comando por defecto (Nginx en foreground)
CMD ["nginx", "-g", "daemon off;"]