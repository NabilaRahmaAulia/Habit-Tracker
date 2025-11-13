# Gunakan image PHP dengan Apache
FROM php:8.2-apache

# Copy semua file project ke dalam container
COPY . /var/www/html/

# Install ekstensi yang dibutuhkan (misalnya MySQL)
RUN docker-php-ext-install mysqli

# Port default Apache
EXPOSE 80
