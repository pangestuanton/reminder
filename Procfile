web: php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php -S 0.0.0.0:$PORT -t public
scheduler: php artisan schedule:work
worker: php artisan queue:work --queue=telegram --tries=3 --timeout=30
