 FROM php:${PHP_VERSION:-"8.1"}-fpm

# Install xdebug
RUN yes | pecl install xdebug \
    && rm -rf /tmp/pear

# Install PHP extensions needed to run the app
RUN apt-get update && \
 		apt-get install -y \
      git \
      libzip-dev \
      libonig-dev \
      libpng-dev \
      libjpeg-dev \
      libwebp-dev \	
      mariadb-client \
      libfreetype6-dev \
      unzip && \
    docker-php-ext-configure \ 
      gd \
        --with-jpeg=/usr \
        --with-freetype  \
        --with-webp &&   \
    docker-php-ext-install \
      gd \
      opcache \
      mbstring \
      pdo \
      pdo_mysql \
      zip

# Custom php ini settings
RUN ln -s /workspace/.devcontainer/app/app.php.ini /usr/local/etc/php/conf.d/app.php.ini

# Sendmail capture
RUN curl https://raw.githubusercontent.com/Sanchiz/fake_sendmail.sh/master/fake_sendmail.sh -o /usr/bin/sendmail && \
  chmod +x /usr/bin/sendmail

# Bee (drush for backrop)
RUN cd ~ && \
  git clone https://github.com/backdrop-contrib/bee.git && \
  chmod +x bee/bee.php

ENV PATH="${PATH}:/workspace/.devcontainer/bin"

CMD ["php-fpm"]