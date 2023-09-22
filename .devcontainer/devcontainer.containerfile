FROM mcr.microsoft.com/devcontainers/php:0-8.2-bullseye

RUN apt-get update && apt-get install -y mariadb-client libpng-dev
RUN docker-php-ext-install pdo pdo_mysql gd

# Download a copy of the backdrop tarball to be installed by reset.sh
RUN curl -fSL "https://github.com/backdrop/backdrop/archive/1.25.1.tar.gz" -o /backdrop.tar.gz

# Install bee
RUN curl -fSL "https://github.com/backdrop-contrib/bee/archive/1.x-1.0.0-beta.tar.gz" -o /bee.tar.gz \
	&& mkdir -p /bee \
	&& tar -xz --strip-components=1 -C /bee -f /bee.tar.gz

RUN ln -s /workspace/.devcontainer/bee.sh /usr/local/bin/bee

COPY reset.sh /usr/local/bin/reset