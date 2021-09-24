FROM php:latest

RUN apt update && apt install -y \
    pkg-config \
    libssl-dev \
    libmcrypt-dev \
    sqlite3 \
    libsqlite3-dev \
    openssl \
    libcurl4-openssl-dev \
    git \
    zip \
    zlib1g-dev \
    libzip-dev \
    unzip \
    mailutils \
    mc \
    openssh-client \
    gnupg \
    gnupg2 \
    gnupg1 \
    libicu-dev \
    gettext

# install mysql support
RUN \
    docker-php-ext-install \
        mysqli \
        pdo \
        pdo_mysql \
        zip

WORKDIR /app

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
