FROM php:8.2-fpm

# Install xdebug
RUN yes | pecl install xdebug \
    && rm -rf /tmp/pear

# Install PHP extensions needed to run the app
RUN apt-get update && apt-get install -y libzip-dev libonig-dev libpng-dev libjpeg-dev libpq-dev \
	&& docker-php-ext-configure gd --with-jpeg=/usr \
	&& docker-php-ext-install gd mbstring pdo pdo_mysql zip

# Custom php ini settings
RUN ln -s /workspace/.devcontainer/drupal10/drupal10.php.ini /usr/local/etc/php/conf.d/drupal10.php.ini

CMD ["php-fpm"]