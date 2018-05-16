FROM phpdockerio/php72-fpm:latest
WORKDIR "/application"

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        php7.2-mysql \
        git \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* \
# Install composer
    && curl --insecure https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer \

# Install node and NPM
    && curl -sL https://deb.nodesource.com/setup_6.x | bash \
    && apt-get -q -y install nodejs \
    && node -v && npm -v