# https://github.com/devcontainers/images/blob/main/src/php/.devcontainer/Dockerfile
FROM mcr.microsoft.com/devcontainers/php:0-8.1-bullseye

# Install PHP extensions needed to develop the app
RUN apt-get update && \
		apt-get install -y \
			libzip-dev \
			libonig-dev \
			libpng-dev \
			libjpeg-dev \
			libwebp-dev \
			logrotate \
			mariadb-client \
			libfreetype6-dev && \
	  docker-php-ext-configure \ 
  		gd \
			--with-jpeg=/usr \
			--with-freetype \
			--with-webp &&\
		docker-php-ext-install \
			gd \
			opcache \
			mbstring \
			pdo \
			pdo_mysql \
			zip

# Terminus
RUN mkdir -p ~/terminus && cd ~/terminus && \
	curl -L https://github.com/pantheon-systems/terminus/releases/download/3.3.0/terminus.phar --output terminus && \
	chmod +x terminus && \
	./terminus self:update && \
	ln -s ~/terminus/terminus /usr/local/bin/terminus

# Bee (drush for backrop)
RUN cd ~ && \
		git clone https://github.com/backdrop-contrib/bee.git && \
 	 	chmod +x bee/bee.php

# Custom php ini
RUN ln -s /workspace/.devcontainer/devcontainer/devcontainer.php.ini \
          /usr/local/etc/php/conf.d/devcontainer.php.ini

# Task https://taskfile.dev
RUN sh -c "$(curl --location https://taskfile.dev/install.sh)" -- -d -b /usr/local/bin

# Disable git's folder ownership setting
# RUN git config --global --add safe.directory "*"

ENV PATH="${PATH}:/workspace/.devcontainer/bin"