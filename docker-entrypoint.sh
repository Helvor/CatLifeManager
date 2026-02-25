#!/bin/bash
set -e

# Les volumes bind-mount écrasent les permissions définies dans le Dockerfile.
# On corrige ici, après le montage, pour que www-data puisse écrire.
chown -R www-data:www-data /var/www/html/database /var/www/html/uploads
chmod 777 /var/www/html/database /var/www/html/uploads

exec apache2-foreground
