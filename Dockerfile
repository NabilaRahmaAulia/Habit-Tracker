# Gunakan image PHP dengan Apache
FROM php:8.2-apache

# Salin semua file project ke dalam folder web server
COPY . /var/www/html/

# Ubah permission agar Apache bisa membaca file
RUN chmod -R 755 /var/www/html

# Pastikan index.php jadi halaman utama
RUN echo "<IfModule dir_module>\nDirectoryIndex index.php index.html\n</IfModule>" > /etc/apache2/conf-enabled/dir.conf

# Install ekstensi MySQL
RUN docker-php-ext-install mysqli

# Port default Apache
EXPOSE 80
