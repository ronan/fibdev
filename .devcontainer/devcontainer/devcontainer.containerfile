# https://github.com/devcontainers/images/blob/main/src/php/.devcontainer/Dockerfile
FROM mcr.microsoft.com/devcontainers/php:0-8.1-bullseye

# Install PHP extensions needed to develop the app
RUN apt-get update && apt-get install -y \
		libzip-dev \
		libonig-dev \
		libpng-dev \
		libjpeg-dev \
		libwebp-dev \
		mariadb-client \
		libfreetype6-dev \
	&& docker-php-ext-configure \ 
		gd --with-jpeg=/usr --with-freetype --with-webp \
	&& docker-php-ext-install \
		gd \
		opcache \
		mbstring \
		pdo \
		pdo_mysql \
		zip

# Custom php ini
RUN ln -s /workspace/.devcontainer/devcontainer/devcontainer.php.ini \
		  /usr/local/etc/php/conf.d/devcontainer.php.ini

# Install mustache processor
RUN curl -sSL https://raw.githubusercontent.com/tests-always-included/mo/master/mo -o /usr/bin/mo \
	&& chmod a+x /usr/bin/mo;

# Helper commands
RUN ln -s /workspace/.devcontainer/drupal/drush.sh 			/usr/local/bin/drush;  		\
	ln -s /workspace/.devcontainer/bin/initialize.php 		/usr/local/bin/initialize;	\
	ln -s /workspace/.devcontainer/bin/reset.sh 		    /usr/local/bin/reset;		\
	ln -s /workspace/.devcontainer/bin/ingest.php 			/usr/local/bin/ingest;		\
	ln -s /workspace/.devcontainer/bin/upgrade.php 			/usr/local/bin/upgrade;		\
	ln -s /workspace/.devcontainer/bin/validate.sh 			/usr/local/bin/validate;	\
	ln -s /workspace/.devcontainer/bin/run.php 				/usr/local/bin/run;

#RUN yarn global add rjsonx

# Disable git's folder ownership setting
RUN git config --global --add safe.directory "*"