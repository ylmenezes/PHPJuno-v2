FROM php:7.3-apache
RUN a2enmod rewrite 
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apt-get update && apt-get install -y zlib1g-dev libicu-dev g++
RUN apt-get install -y libzip-dev zip && docker-php-ext-install zip
RUN docker-php-ext-configure intl && docker-php-ext-install intl
RUN apt-get update && apt-get install nano