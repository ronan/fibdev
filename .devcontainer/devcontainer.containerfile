FROM mcr.microsoft.com/devcontainers/php:0-8.2-bullseye

RUN apt-get update && apt-get install -y mariadb-client libpng-dev
RUN docker-php-ext-install pdo pdo_mysql gd

ENV DRUPAL_VERSION 10.1.3

WORKDIR /core
RUN set -eux; \
	composer create-project --no-interaction "drupal/recommended-project:$DRUPAL_VERSION" ./; \
    composer require drush/drush;

ENV PATH=${PATH}:/core/vendor/bin

COPY reset.sh /usr/local/bin/reset