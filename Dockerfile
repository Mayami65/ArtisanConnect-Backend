FROM serversideup/php:8.2-fpm-nginx

# Switch to root to install dependencies and configure permissions
USER root

# Install postgresql extension for Laravel (serversideup already has most, but adding pgsql just in case)
RUN apt-get update && apt-get install -y postgresql-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY --chown=www-data:www-data . /var/www/html

# Copy the start script to the entrypoint directory so it runs automatically
COPY --chown=root:root start.sh /etc/entrypoint.d/99-deploy.sh
RUN chmod +x /etc/entrypoint.d/99-deploy.sh

# Switch back to www-data
USER www-data

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
