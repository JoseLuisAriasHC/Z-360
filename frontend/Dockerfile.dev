FROM node:22-alpine

# Instalar dumb-init
RUN apk add --no-cache dumb-init

# Crear directorio de trabajo
WORKDIR /app

# Asignar permisos al usuario node antes de cambiarlo
RUN chown -R node:node /app

# Cambiar a usuario no-root
USER node

# Copiar archivos de dependencias y cambiar propietario al usuario node
COPY --chown=node:node src/package*.json ./

# Instalar dependencias
RUN npm install

# Copiar el resto del código
COPY --chown=node:node src/. .

# Exponer puerto
EXPOSE 5173

# Usar dumb-init para manejar señales correctamente
ENTRYPOINT ["dumb-init", "--"]

# Comando por defecto
CMD ["npm", "run", "dev", "--", "--host"]
