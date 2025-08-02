FROM webdevops/php-nginx:8.3-alpine

# Metatadata ARGS label-schema.org

ARG VERSION
ARG VCS_URL
ARG VCS_REF
ARG BUILD_DATE

LABEL org.label-schema.build-date=$BUILD_DATE \
    org.label-schema.version=$VERSION \
    org.label-schema.url=$VCS_URL \
    org.label-schema.vcs-url=$VCS_URL \
    org.label-schema.vcs-ref=$VCS_REF

WORKDIR /app

COPY conf/bin/ /opt/docker/bin/

COPY ./ ./

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install

ARG APP_ENV=dev
ENV APP_ENV=${APP_ENV}

RUN if [ "$APP_ENV" = "prod" ]; then \
        echo "lancement des tests en prod..."; \
        ./vendor/bin/phpunit --testsuite=Production; \
    elif [ "$APP_ENV" = "dev" ]; then \
        echo "lancement des tests en dev..."; \
        ./vendor/bin/phpunit --testsuite=Development; \
    else \
        echo "environnement non renseigné ou inconnu, pas d'exécution de test"; \
    fi

