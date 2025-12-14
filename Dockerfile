FROM node:18-alpine

WORKDIR /app

# Installer les dépendances système
RUN apk add --no-cache sqlite curl

# Copier les fichiers package
COPY package*.json ./

# Installer les dépendances
RUN npm install

# Copier le code source
COPY . .

# Build du frontend React
RUN npm run build

# Créer les dossiers nécessaires
RUN mkdir -p database database/backups server/uploads

# Exposer les ports
EXPOSE 3000 6000

# Démarrer l'application
CMD ["npm", "start"]
