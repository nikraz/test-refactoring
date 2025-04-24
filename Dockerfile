FROM php:8.4-rc-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    && docker-php-ext-install bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN chmod +x bin/console \
    && composer install --no-interaction --prefer-dist

CMD ["bin/console", "commission:calculate", "input.txt"]
