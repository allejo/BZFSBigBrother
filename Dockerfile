ARG NODE_VERSION='16.x'
ARG PICKLE_VERSION='0.7.9'

#
# Scratch image to install all of our dependencies
#

FROM php:8.1-apache AS scratch_dependency_image
ARG NODE_VERSION
ARG PICKLE_VERSION

RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION} | bash -
RUN apt-get update && apt-get install -y \
      apt-utils \
      git \
      libpq-dev \
      nodejs \
      wget \
      zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer (https://getcomposer.org/)
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Install Pickle (https://github.com/FriendsOfPHP/pickle)
RUN wget https://github.com/FriendsOfPHP/pickle/releases/download/v${PICKLE_VERSION}/pickle.phar \
      -O /usr/local/bin/pickle \
    && chmod +x /usr/local/bin/pickle

# Enable MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql

# Copy our site source
COPY . /var/www/big-brother/
WORKDIR /var/www/big-brother/

# Setup our dependency folders
RUN useradd -m -u 1001 BigBrotherUser
RUN mkdir -p node_modules/ \
    && mkdir -p public/build/ \
    && mkdir -p var/cache/ \
    && mkdir -p var/log/ \
    && mkdir -p vendor/ \
    && chown -R BigBrotherUser assets/generated/ node_modules/ public/build/ var/ vendor/

USER BigBrotherUser

# Everything for Composer
ENV APP_ENV=prod
ENV APP_DEBUG=0
RUN composer install --no-dev --optimize-autoloader

# Everything for Node
ENV NODE_ENV=production
RUN npm ci
RUN npm run build

#
# Build the base of our container
#

FROM php:8.1-apache AS base_image
ARG NODE_VERSION
ARG PICKLE_VERSION

RUN apt-get update && apt-get install -y \
      gdal-bin \
      libpq-dev \
      wget \
    && rm -rf /var/lib/apt/lists/*

# Install Pickle (https://github.com/FriendsOfPHP/pickle)
RUN wget https://github.com/FriendsOfPHP/pickle/releases/download/v${PICKLE_VERSION}/pickle.phar \
      -O /usr/local/bin/pickle \
    && chmod +x /usr/local/bin/pickle

# Enable PostgreSQL extensions
RUN docker-php-ext-install pdo pdo_mysql

# Configure Apache
COPY container-src/apache2.conf /etc/apache2/apache2.conf
COPY container-src/000-default.conf /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite

# Copy Symfony source
COPY --from=scratch_dependency_image /var/www/big-brother/ /var/www/big-brother/
WORKDIR /var/www/big-brother/

#
# The development version of our Docker image
#

FROM base_image as development

LABEL name="BZFS Big Brother (DEV)"

# Install Node.js/npm & dependencies for being able to run Composer
RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION} | bash -
RUN apt-get update && apt-get install -y \
      apt-utils \
      git \
      libpq-dev \
      nodejs \
      vim \
      wget \
      zip

# Install Composer (https://getcomposer.org/)
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Install Xdebug and configure it to automatically attempt to attach itself to
# port 9000 of the Docker host machine
RUN pickle install xdebug --defaults \
    && docker-php-ext-enable xdebug \
    && echo '\
        xdebug.mode=debug\n\
        xdebug.client_host=host.docker.internal\n\
        xdebug.client_port=9000\n\
        xdebug.discover_client_host=true\n\
        xdebug.start_with_request=trigger\n\
    ' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN echo '\
    # Helper functions for enabling/disabling Xdebug in the CLI\n\
    enableDebug() {\n\
      export XDEBUG_MODE=debug\n\
      export XDEBUG_SESSION=1\n\
      export COMPOSER_ALLOW_XDEBUG=1\n\
    }\n\
    disableDebug() {\n\
      unset XDEBUG_MODE\n\
      unset XDEBUG_SESSION\n\
      unset COMPOSER_ALLOW_XDEBUG\n\
    }\n\
    ' >> /root/.bashrc

ENTRYPOINT ["apache2-foreground"]

#
# The production version of our Docker image
#

FROM base_image as production

LABEL name="BZFS Big Brother"
LABEL description="A web application to store and track information about players and their connections"

RUN useradd -m -u 1001 BigBrotherUser
USER BigBrotherUser

ENTRYPOINT ["apache2-foreground"]
