FROM php:8.1-fpm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Run queue worker in the background
CMD php artisan queue:work --daemon
