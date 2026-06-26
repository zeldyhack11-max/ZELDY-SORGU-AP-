FROM php:8.3-apache

COPY . /var/www/html/

RUN echo "DirectoryIndex index.php" > /var/www/html/.htaccess

EXPOSE 80
