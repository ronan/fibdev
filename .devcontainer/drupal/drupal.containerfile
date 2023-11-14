FROM php:7.4-fpm

# Install xdebug
# RUN yes | pecl install xdebug \
#     && rm -rf /tmp/pear

# Install PHP extensions needed to run the app
RUN apt-get update && apt-get install -y libzip-dev libonig-dev libpng-dev libjpeg-dev libpq-dev \
	&& docker-php-ext-configure gd --with-jpeg=/usr \
	&& docker-php-ext-install gd mbstring pdo pdo_mysql zip

# Custom php ini settings
RUN ln -s /workspace/.devcontainer/drupal/drupal.php.ini /usr/local/etc/php/conf.d/drupal.php.ini

CMD ["php-fpm"]