FROM php:8.1-bullseye

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
  libzip-dev \
  zip \
  unzip \
  git \
  curl \
  libpng-dev \
  libjpeg-dev \
  libfreetype6-dev

# Instalar extensiones necesarias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install gd zip exif mysqli pdo pdo_mysql opcache

# Librerías X11/gráficas requeridas por el binario wkhtmltopdf del vendor (h4cc/wkhtmltopdf-amd64)
RUN apt-get update && apt-get install -y --no-install-recommends \
  libxrender1 \
  libxext6 \
  libx11-6 \
  libfontconfig1 \
  libfreetype6 \
  xfonts-base \
  xfonts-75dpi \
  fontconfig

# Configurar OPcache para máximo rendimiento
RUN echo 'opcache.enable=1' >> /usr/local/etc/php/conf.d/opcache.ini \
  && echo 'opcache.memory_consumption=256' >> /usr/local/etc/php/conf.d/opcache.ini \
  && echo 'opcache.max_accelerated_files=20000' >> /usr/local/etc/php/conf.d/opcache.ini \
  && echo 'opcache.validate_timestamps=1' >> /usr/local/etc/php/conf.d/opcache.ini \
  && echo 'opcache.revalidate_freq=0' >> /usr/local/etc/php/conf.d/opcache.ini \
  && echo 'opcache.interned_strings_buffer=16' >> /usr/local/etc/php/conf.d/opcache.ini

# Limpiar
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar proyecto
COPY . /var/www/html

# Permisos Laravel
RUN chmod -R 777 storage bootstrap/cache

# Asegurar que el binario wkhtmltopdf incluido en vendor sea ejecutable
RUN if [ -f /var/www/html/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64 ]; then \
      chmod +x /var/www/html/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64; \
    fi

# Cachear config/rutas/vistas al construir la imagen
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Comando
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]