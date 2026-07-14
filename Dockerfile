FROM php:8.3-cli-alpine

RUN apk add --no-cache sqlite-libs \
    && docker-php-ext-install pdo_sqlite

WORKDIR /app

COPY src/ /app/src/
COPY views/ /app/views/
COPY public/ /app/public/
COPY router.php /app/router.php

RUN mkdir -p /app/data && chown www-data:www-data /app/data

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public", "router.php"]
