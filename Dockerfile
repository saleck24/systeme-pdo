FROM php:8.2-apache

# Installer l'extension mysqli indispensable pour se connecter à la base de données
RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli

# Copier le code source de l'application vers le dossier web d'Apache
COPY . /var/www/html/

# Donner les permissions appropriées au dossier web (pour l'upload d'images)
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/

# Exposer le port 80
EXPOSE 80
