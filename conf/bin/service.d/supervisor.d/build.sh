#!/usr/bin/env bash

php /app/bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
# php /app/bin/console assets:install
 
# php bin/console sass:build
# php bin/console asset-map:compile
 
chmod -R 777 /app/var/cache
chmod -R 777 /app/var/log
chmod -R 777 /app/upload/
