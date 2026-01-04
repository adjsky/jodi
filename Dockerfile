ARG PHP_VERSION=8.4
ARG NODE_VERSION=24

###################################################################
# Stage 1: Base                                                   #
###################################################################

FROM dunglas/frankenphp:php${PHP_VERSION}-alpine AS base

RUN install-php-extensions \
    opcache \
    pcntl \
    pdo_mysql \
    bcmath \
    intl \
    zip \
    gd

RUN apk add --no-cache multirun

###################################################################
# Stage 2: Install dependencies & build assets                    #
###################################################################

FROM base AS builder

WORKDIR /var/www/jodi

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./

RUN composer install --optimize-autoloader --no-dev --no-scripts

RUN apk add --no-cache nodejs npm

COPY package*.json ./
COPY patches/ ./patches

RUN npm ci

COPY . .

RUN npm run build

###################################################################
# Stage 3: Build runtime                                          #
###################################################################

FROM base AS runtime

ARG USER=jodi

RUN adduser -D ${USER}

WORKDIR /var/www/jodi

RUN chown ${USER}:${USER} /var/www/jodi

COPY --chown=${USER}:${USER} --from=builder /var/www/jodi/vendor ./vendor
COPY --chown=${USER}:${USER} --from=builder /var/www/jodi/public/build ./public/build
COPY --chown=${USER}:${USER} . .

USER ${USER}

ENV APP_ENV=production
ENV APP_DEBUG=false

RUN php artisan jodi:setup
RUN php artisan optimize

CMD ["multirun", "frankenphp run", "php artisan queue:work --tries=3 --timeout=60", "php artisan schedule:work"]
