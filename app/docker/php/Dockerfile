FROM php:8.2-fpm-alpine AS php-base

WORKDIR /var/www/app

RUN apk add --no-cache \
    bash \
    git \
    icu-dev \
    libpq-dev \
    libzip-dev \
    oniguruma-dev \
    tzdata \
    curl \
    zip \
    unzip \
    autoconf 

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer:2.7.1 /usr/bin/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
        intl \
        opcache \
        pdo_pgsql \
        zip \
        redis \
        amqp 

COPY composer.json composer.lock ./ 
COPY docker/php/conf/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/conf/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY docker/php/conf/xdebug.ini /usr/local/etc/php/conf.d/zz-xdebug.ini




FROM php-base AS dev

ENV APP_ENV=dev \
    APP_DEBUG=1

RUN install-php-extensions xdebug

RUN composer install \
    --prefer-dist \
    --no-progress \
    --no-interaction \
    --no-scripts

RUN addgroup -S appgroup && \
    adduser -u 1000 -D -S -G appgroup appuser

COPY . ./

RUN chown -R appuser:appgroup ./storage \
    && chown -R appuser:appgroup ./bootstrap/cache

USER appuser

CMD ["php-fpm"]





FROM php-base AS prod

ENV APP_ENV=prod \
    APP_DEBUG=0

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-progress \
    --no-interaction \
    --no-scripts \
    --optimize-autoloader

COPY . ./



# RUN composer dump-autoload --optimize --no-dev && \
#     php artisan optimize && \
#     php artisan config:cache && \
#     php artisan route:cache && \
#     php artisan view:cache && \
#     php artisan event:cache


# RUN php artisan optimize \
#     && php artisan config:cache \
#     && php artisan route:cache \
#     && php artisan view:cache \
#     && php artisan event:cache


RUN chown -R www-data:www-data ./storage \
    && chown -R www-data:www-data ./bootstrap/cache

USER www-data

CMD ["php-fpm"]