FROM php:8.2-cli

# Инсталиране на pdo_mysql разширението
RUN docker-php-ext-install pdo_mysql
