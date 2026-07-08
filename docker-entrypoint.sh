#!/bin/bash
set -e

echo "Membersihkan dan merestart cache..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Menjalankan Database Migrations ke TiDB..."
php artisan migrate --force

echo "Memulai Server Apache..."
exec apache2-foreground
