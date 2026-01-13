FROM php:8.4-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pdo_sqlite zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy the entire application
COPY . .

# Install dependencies (including dev dependencies for tests)
RUN composer install --prefer-dist --no-progress --optimize-autoloader

# Set environment variable for Doctrine
ENV DOCTRINE_DIR=/app

# Default command - run tests
ENTRYPOINT ["./vendor/bin/phpunit"]
CMD ["--testsuite", "Smoke Tests"]

