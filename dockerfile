FROM php:8.2-apache

# Installer les extensions PHP nécessaires (SQLite + GD pour la génération des icônes PWA)
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Configuration PHP (limites upload)
COPY php.ini /usr/local/etc/php/conf.d/catlife.ini

# Copier les fichiers de l'application
COPY . /var/www/html/

# Créer les dossiers nécessaires et donner les permissions
RUN mkdir -p /var/www/html/uploads /var/www/html/database /var/www/html/icons \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/uploads \
    && chmod -R 777 /var/www/html/database

# Générer les icônes PWA (manifest + iOS apple-touch-icon)
RUN php /var/www/html/scripts/generate_icons.php

# Script d'entrypoint : corrige les permissions des volumes bind-mount au démarrage
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Exposer le port 80
EXPOSE 80

# Entrypoint : fixe les permissions puis démarre Apache
ENTRYPOINT ["docker-entrypoint.sh"]
