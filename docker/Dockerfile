FROM php:7.2

LABEL maintainer="Johannes Seipelt <johannes.seipelt@3m5.de>"

# install system dependencies
RUN apt-get update \
 && apt-get install -y software-properties-common \
	zip \
 && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# install debug tools
RUN pecl install xdebug \
 && docker-php-ext-enable xdebug

# install run script
COPY run.sh /usr/src
RUN chmod 700 /usr/src/run.sh

WORKDIR /usr/src

CMD /usr/src/run.sh
