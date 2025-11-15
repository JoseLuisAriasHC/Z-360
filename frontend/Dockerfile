FROM node:22-alpine

RUN apk add --no-cache dumb-init

WORKDIR /app

# Copiar archivos de dependencias
COPY src/package*.json ./

# Instalar dependencias
RUN npm install

# Copiar código fuente
COPY src/. .

EXPOSE 5173

ENTRYPOINT ["dumb-init", "--"]

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]