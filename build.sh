#!/bin/sh

set -e

BIN_DIR="./bin"
COMPOSER_PATH="$BIN_DIR/composer"
PUBLIC_BUILD_DIR="./public/build"

mkdir -p "$BIN_DIR"

EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

# Verify Composer installer
if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
    >&2 echo "ERROR: Invalid installer checksum"
    rm -f composer-setup.php
    exit 1
fi

php composer-setup.php --install-dir="$BIN_DIR" --filename=composer --quiet
rm -f composer-setup.php

# Install dependencies using Composer
"$COMPOSER_PATH" install --no-dev --optimize-autoloader

# Run Laravel commands
php artisan key:generate
php artisan migrate --force
php artisan filament:optimize
php artisan optimize

# Compress files in public build directory
if [ -d "$PUBLIC_BUILD_DIR" ]; then
    find "$PUBLIC_BUILD_DIR" -type f ! -name "*.gz" -exec gzip -f -k -9 {} \;
fi

echo "Setup completed successfully!"
