FROM node:22-alpine

WORKDIR /app

# Copiar package.json y package-lock.json
COPY src/package*.json ./

# Instalar dependencias
RUN npm install

# Copiar el resto del código
COPY src/. .

# Exponer el puerto de Vite
EXPOSE 5173

# Comando para desarrollo
CMD ["npm", "run", "dev", "--", "--host"]