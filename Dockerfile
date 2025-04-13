FROM php:8.2-cli

WORKDIR /var/www/html

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar extensiones de PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Copiar los archivos del proyecto
COPY . .

# Instalar dependencias
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Exponer el puerto que usa la aplicación
EXPOSE 8080

# Comando para ejecutar la aplicación
CMD ["php", "run.php"] 