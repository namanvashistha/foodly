FROM php:8.2-apache

# Install mysqli extension for database connection
RUN docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli

# Copy the application source code into the container
COPY . /var/www/html/

# Expose port 80 for Apache
EXPOSE 80
