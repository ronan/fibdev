# https://github.com/devcontainers/images/blob/main/src/php/.devcontainer/Dockerfile
FROM mcr.microsoft.com/devcontainers/php:0-8.0-bullseye

# Install PHP extensions needed to develop the app
RUN apt-get update && apt-get install -y libzip-dev libonig-dev libpng-dev libjpeg-dev libpq-dev mariadb-client \
	&& docker-php-ext-configure gd --with-jpeg=/usr \
	&& docker-php-ext-install gd mbstring pdo pdo_mysql zip

# Custom php ini
RUN ln -s /workspace/.devcontainer/devcontainer.php.ini /usr/local/etc/php/conf.d/devcontainer.php.ini

# Download a copy of the backdrop tarball to be installed by reset.sh
RUN curl -fSL "https://github.com/backdrop/backdrop/archive/1.25.1.tar.gz" -o /backdrop.tar.gz \
	&& mkdir -p /backdrop \
	&& tar -xz --strip-components=1 -C /backdrop -f /backdrop.tar.gz

# Download bee
RUN curl -fSL "https://github.com/backdrop-contrib/bee/archive/refs/heads/1.x-1.x.tar.gz" -o /bee.tar.gz \
	&& mkdir -p /bee \
	&& tar -xz --strip-components=1 -C /bee -f /bee.tar.gz

RUN ln -s /workspace/.devcontainer/bee.sh 	/usr/local/bin/bee
RUN ln -s /workspace/.devcontainer/reset.sh /usr/local/bin/reset

