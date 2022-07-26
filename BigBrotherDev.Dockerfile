FROM base_image

LABEL name="BZFS Big Brother (DEV)"
LABEL version="X.X.X"

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
