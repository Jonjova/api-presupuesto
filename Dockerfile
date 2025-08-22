FROM php:8.1-fpm

# Instalar solo PHP y Nginx (quitamos MySQL)
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev \
    nginx \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Nginx
RUN echo 'server {\n\
    listen 80;\n\
    index index.php index.html;\n\
    server_name localhost;\n\
    root /var/www/html/public;\n\
    location / {\n\
        try_files \$uri \$uri/ /index.php?\$query_string;\n\
    }\n\
    location ~ \.php$ {\n\
        try_files \$uri =404;\n\
        fastcgi_split_path_info ^(.+\.php)(/.+)$;\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_index index.php;\n\
        include fastcgi_params;\n\
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;\n\
        fastcgi_param PATH_INFO \$fastcgi_path_info;\n\
    }\n\
}' > /etc/nginx/sites-available/default

WORKDIR /var/www/html

EXPOSE 80

CMD sh -c "php-fpm && nginx -g 'daemon off;'"