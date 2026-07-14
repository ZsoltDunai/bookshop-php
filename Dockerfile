FROM php:8.3-cli-alpine AS backend

RUN apk add --no-cache sqlite-libs curl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && docker-php-ext-install pdo_sqlite

WORKDIR /app

COPY composer.json /app/
RUN composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader

COPY src/ /app/src/
COPY public/index.php /app/public/index.php
COPY router.php /app/router.php

FROM backend AS frontend-build

RUN apk add --no-cache nodejs npm

COPY frontend/package.json /app/frontend/
WORKDIR /app/frontend
RUN npm install --no-audit --no-fund

COPY frontend/ /app/frontend/
RUN npm run build

FROM php:8.3-cli-alpine

RUN apk add --no-cache sqlite-libs \
    && docker-php-ext-install pdo_sqlite

WORKDIR /app

COPY --from=backend /app/vendor /app/vendor
COPY --from=backend /app/src /app/src
COPY --from=backend /app/public/index.php /app/public/index.php
COPY --from=backend /app/composer.json /app/composer.json
COPY --from=frontend-build /app/public/browser /app/public/browser
COPY router.php /app/router.php

RUN mkdir -p /app/data && chown www-data:www-data /app/data

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public", "router.php"]
