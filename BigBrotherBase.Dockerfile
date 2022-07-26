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
