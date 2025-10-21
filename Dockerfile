# Use PHP 8.3 with Apache
FROM serversideup/php:8.3-fpm-nginx

WORKDIR /var/www/html

# Copy files
COPY . .

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Optimize Laravel
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

EXPOSE 8080
CMD ["php-fpm"]
