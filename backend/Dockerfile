# ── Backend Dockerfile ──────────────────────────────────────────────────────
FROM php:8.3-cli

# System deps
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    git \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Create SQLite DB file
RUN mkdir -p database && touch database/vouchers.db

# Set env
RUN cp .env.example .env \
    && sed -i 's|DB_DATABASE=/absolute/path/to/database/vouchers.db|DB_DATABASE=/app/database/vouchers.db|' .env \
    && php artisan key:generate

RUN php artisan migrate --force

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
