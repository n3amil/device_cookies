FROM php:7.2

LABEL maintainer="Johannes Seipelt <johannes.seipelt@3m5.de>"

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# install debug tools
RUN pecl install xdebug \
 && docker-php-ext-enable xdebug

# install run script
COPY run.sh /
RUN chmod 700 /run.sh

WORKDIR /tmp

CMD /run.sh
