# ─── QuickBite / Seges Foods — Dockerfile for Render ─────────
# PHP 8.2 + Apache + PostgreSQL driver
# ──────────────────────────────────────────────────────────────

FROM php:8.2-apache

# Install PostgreSQL client library + PHP pdo_pgsql extension
RUN apt-get update && apt-get install -y \
        libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite (for clean URLs if needed later)
RUN a2enmod rewrite

# Set the document root to the project directory
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Configure Apache to allow .htaccess overrides
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

# Copy all project files into the container
COPY . /var/www/html/

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Create writable uploads directory for food images
RUN mkdir -p /var/www/html/assets/images/foods \
    && chown -R www-data:www-data /var/www/html/assets/images/foods

# Expose port 80
EXPOSE 80

# Use the default apache2 foreground command
CMD ["apache2-foreground"]
