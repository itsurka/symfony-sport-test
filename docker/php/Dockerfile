FROM php:7.2-fpm

RUN apt-get update
RUN apt-get -y install gcc make build-essential autoconf libssl-dev \
  git bash nano wget libc-dev pkg-config librabbitmq-dev libmcrypt-dev zlib1g-dev libicu-dev g++ libpng-dev zip \
            curl \
            libfreetype6-dev \
            libcurl3-dev \
            libjpeg-dev \
            libjpeg62-turbo-dev \
            libpq-dev \
            libxml2-dev \
            libzip-dev \
            openssh-client \
            unzip
RUN pecl install mcrypt-1.0.2

# bcmath bz2 calendar ctype curl dba dom enchant exif fileinfo filter ftp gd gettext gmp hash iconv imap interbase intl
# json ldap mbstring mcrypt mssql mysql mysqli oci8 odbc opcache pcntl pdo pdo_dblib pdo_firebird pdo_mysql pdo_oci
# pdo_odbc pdo_pgsql pdo_sqlite pgsql phar posix pspell readline recode reflection session shmop simplexml snmp soap
# sockets spl standard sybase_ct sysvmsg sysvsem sysvshm tidy tokenizer wddx xml xmlreader xmlrpc xmlwriter xsl zip


RUN docker-php-ext-configure gd \
        --with-png-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-configure bcmath && \
    docker-php-ext-install \
        soap \
        zip \
        curl \
        bcmath \
        exif \
        gd \
        iconv \
        intl \
        mbstring \
        opcache \
        pdo_mysql \
        pdo_pgsql \
        pcntl \
        sockets

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- \
        --filename=composer \
        --install-dir=/usr/local/bin && \
        echo "alias composer='composer'" >> /root/.bashrc && \
        composer

# Add GITHUB_API_TOKEN support for composer
RUN chmod 700 \
        /usr/local/bin/docker-php-entrypoint \
        /usr/local/bin/composer

# Install composer plugins
RUN composer global require --optimize-autoloader \
        "hirak/prestissimo:^0.3.7" && \
    composer global dumpautoload --optimize && \
    composer clear-cache

# Install xdebug
RUN yes | pecl install xdebug-2.7.2 \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN echo "php_admin_value[error_reporting] = E_ALL & ~E_STRICT & ~E_DEPRECATED" >> /usr/local/etc/php-fpm.d/www.conf \
 && echo "php_admin_value[display_startup_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf \
 && echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf \
 && echo "php_flag[display_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf \
 && echo "catch_workers_output = yes" >> /usr/local/etc/php-fpm.d/www.conf \
 && mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

VOLUME ["/var/www"]
WORKDIR /var/www
