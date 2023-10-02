FROM php:8.2-fpm

# Install xdebug
RUN yes | pecl install xdebug \
    && rm -rf /tmp/pear

RUN apt-get update && apt-get install -y mariadb-client libpng-dev
RUN docker-php-ext-install pdo pdo_mysql gd

# Custom php ini settings
RUN ln -s /workspace/.devcontainer/drupal10.php.ini /usr/local/etc/php/conf.d/drupal10.php.ini 

CMD ["php-fpm"]