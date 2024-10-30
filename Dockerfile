FROM composer:2 AS composer

FROM php:8.3-cli

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

#Installing and enabling features and PHP extension needed
RUN apt-get update -y \
  && apt-get install -y libxml2-dev git unzip openjdk-17-jre \
  && apt-get clean -y \
  && docker-php-ext-install soap
