FROM node:18-alpine

WORKDIR /app

# Installer les dépendances système
RUN apk add --no-cache sqlite curl

# Copier les fichiers package.json séparément pour optimiser le cache
COPY package*.json ./

# Installer les dépendances
RUN npm install

# Copier tout le code source
COPY . .

# Build du frontend React (dans le dossier client)
WORKDIR /app/client
RUN npm run build

# Créer les dossiers nécessaires pour le backend
RUN mkdir -p /app/database /app/database/backups /app/server/uploads

# Exposer les ports
EXPOSE 3000 6000

# Revenir au WORKDIR racine pour démarrer le serveur
WORKDIR /app
CMD ["npm", "start"]

