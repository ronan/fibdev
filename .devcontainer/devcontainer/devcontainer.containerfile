# https://github.com/devcontainers/images/blob/main/src/php/.devcontainer/Dockerfile
FROM mcr.microsoft.com/devcontainers/php:0-8.1-bullseye

# Install PHP extensions needed to develop the app
RUN apt-get update && apt-get install -y libzip-dev libonig-dev libpng-dev libjpeg-dev libpq-dev mariadb-client \
	&& docker-php-ext-configure gd --with-jpeg=/usr \
	&& docker-php-ext-install gd mbstring pdo pdo_mysql zip

# Custom php ini
RUN ln -s /workspace/.devcontainer/devcontainer/devcontainer.php.ini /usr/local/etc/php/conf.d/devcontainer.php.ini

# Helper commands
RUN ln -s /workspace/.devcontainer/drupal9/drush9.sh 		/usr/local/bin/drush9;  	\
	ln -s /workspace/.devcontainer/drupal10/drush10.sh 		/usr/local/bin/drush10; 	\
	ln -s /workspace/.devcontainer/drupal9/composer9.sh 	/usr/local/bin/composer9; 	\
	ln -s /workspace/.devcontainer/drupal10/composer10.sh 	/usr/local/bin/composer10;	\
	ln -s /workspace/.devcontainer/reset.sh 				/usr/local/bin/reset;		\
	ln -s /workspace/.devcontainer/upgrade.sh 				/usr/local/bin/upgrade;


# RUN composer global require drush/drush:11.*