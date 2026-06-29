FROM php:8.2-fpm
WORKDIR /usr/share/nginx/html
COPY images/nginx/http/index.php . 
EXPOSE 9000
