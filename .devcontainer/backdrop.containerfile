FROM php:8.1-fpm

# install the PHP extensions we need
RUN apt-get update && apt-get install -y libzip-dev libonig-dev libpng-dev libjpeg-dev libpq-dev \
	&& docker-php-ext-configure gd --with-jpeg=/usr \
	&& docker-php-ext-install gd mbstring pdo pdo_mysql pdo_pgsql zip


CMD ["php-fpm", "-c", "/workspace/.devcontainer/php.ini"]
