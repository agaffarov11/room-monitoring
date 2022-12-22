FROM ghcr.io/roadrunner-server/roadrunner:2.12.0-rc.1 AS roadrunner
FROM php:8.1-alpine

ARG UID=1000
ARG GID=1000
ARG PLACEHOLDER_USER_NAME="docker_user"
ARG DEV_DEPENDENCIES="xdebug"
ARG INCLUDE_DEV_DEPS

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr

RUN chmod +x /usr/local/bin/install-php-extensions &&  \
    install-php-extensions zip pdo_pgsql amqp tokenizer sockets

RUN if test -n "$INCLUDE_DEV_DEPS"; \
    then \
      >&2 echo "Building development environment"; \
      >&2 echo "Installing dev dependencies: ($DEV_DEPENDENCIES)"; \
      install-php-extensions $DEV_DEPENDENCIES; \
    else \
      >&2 echo "Building production environment"; \
    fi

RUN apk -U upgrade && apk add --no-cache su-exec \
    && addgroup -g $GID -S $PLACEHOLDER_USER_NAME \
    && adduser -u $UID -S -G $PLACEHOLDER_USER_NAME $PLACEHOLDER_USER_NAME

WORKDIR /var/www
RUN rm -rf ./*
COPY --chown=$UID:$GID ./ ./docker/www ./

RUN rm -rf docker && chown -R $UID:$GID .

RUN chmod +x /usr/bin/composer && \
    alias composer='XDEBUG_MODE=off \composer' &&  \
    su-exec $UID composer install \
    --no-interaction \
    --no-scripts \
    --no-dev \
    --prefer-dist

USER $UID:$GID
ENTRYPOINT ["rr", "serve", "-c", ".rr.dev.yaml"]

