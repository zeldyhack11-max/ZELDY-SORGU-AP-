FROM php:8.3-apache

RUN a2enmod rewrite

COPY . /var/www/html/

RUN echo "DirectoryIndex tc.php" > /var/www/html/.htaccess

EXPOSE 80
