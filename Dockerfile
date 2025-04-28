FROM php:8.2-apache
RUN a2enmod rewrite

RUN apt-get update && apt-get install -y --no-install-recommends libzip-dev && docker-php-ext-install -j$(nproc) zip

COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

COPY src/ /var/www/html/

RUN mkdir -p /var/www/html/tmp
RUN mkdir -p /var/www/html/logs
RUN mkdir -p /var/www/html/data

RUN chown -R www-data:www-data /var/www/html
RUN chmod -R -f 775 /var/www/html

EXPOSE 80
