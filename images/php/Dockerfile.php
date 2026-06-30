FROM php:8.2-fpm-alpine
RUN apk add --no-cache postgresql-dev
RUN docker-php-ext-install pdo pdo_pgsql
WORKDIR /usr/share/nginx/html
COPY images/nginx/http/index.php . 
EXPOSE 9000
