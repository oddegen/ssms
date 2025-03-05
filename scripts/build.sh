#!/bin/sh

# Install composer in the project
BIN_DIR=$1

EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]
then
    >&2 echo 'ERROR: Invalid installer checksum'
    rm composer-setup.php
    exit 1
fi

if [ -z "$BIN_DIR" ]
then
    if [ ! -d "../bin" ]; then
        mkdir ../bin
    fi
    BIN_DIR="../bin"
fi

php composer-setup.php --install-dir="$BIN_DIR" --filename=composer --quiet
rm composer-setup.php

# Install composer dependencies
$"BIN_DIR"/composer install --no-dev --optimize-autoloader

php artisan key:generate
php artisan migrate --force --seed
php artisan filament:optimize
php artisan optimize

find /app/public/build -type f ! -name "*.gz" -exec gzip   -f -r -k -9  {} \;




