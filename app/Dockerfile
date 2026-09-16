FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" curl gd mbstring pdo_mysql \
    && a2enmod rewrite headers expires deflate \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . /var/www/html/

# O bloco abaixo existe apenas na hospedagem cPanel original e não é válido
# na imagem oficial php:apache. A remoção ocorre somente dentro da imagem.
RUN if [ -f .htaccess ]; then \
      sed -i '/# php -- BEGIN cPanel-generated handler/,/# php -- END cPanel-generated handler/d' .htaccess; \
    fi \
    && chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
