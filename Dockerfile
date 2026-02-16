ARG PHP_VERSION=8.4
ARG NODE_VERSION=24.12.0

###################################################################
# Stage 1: Base                                                   #
###################################################################

FROM dunglas/frankenphp:php${PHP_VERSION}-alpine AS base

RUN install-php-extensions \
    pcntl \
    bcmath \
    intl \
    zip \
    gd

RUN apk add --no-cache multirun

###################################################################
# Stage 2.1: Install PHP dependencies                             #
###################################################################

FROM node:${NODE_VERSION}-alpine AS node_builder
FROM base AS builder

WORKDIR /var/www/jodi

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./

RUN composer install --optimize-autoloader --no-dev --no-scripts

###################################################################
# Stage 2.2: Install Node.js dependencies & build assets          #
###################################################################

COPY --from=node_builder /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node_builder /usr/local/bin/node /usr/local/bin/node

COPY package*.json ./
COPY patches/ ./patches

RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm && \
    ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

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

RUN chown -R ${USER}:${USER} /config/caddy /data/caddy
RUN chown ${USER}:${USER} /var/www/jodi

COPY --chown=${USER}:${USER} --from=builder /var/www/jodi/vendor ./vendor
COPY --chown=${USER}:${USER} --from=builder /var/www/jodi/public/build ./public/build
COPY --chown=${USER}:${USER} . .

USER ${USER}

ENV APP_ENV=production
ENV APP_DEBUG=false

RUN php artisan event:cache && \
    php artisan route:cache && \
    php artisan view:cache

ENTRYPOINT ["scripts/entrypoint.sh"]
CMD [ \
    "multirun", \
    "frankenphp run", \
    "php artisan queue:work --tries=3 --timeout=60 --backoff=30", \
    "php artisan schedule:work --quiet" \
]
