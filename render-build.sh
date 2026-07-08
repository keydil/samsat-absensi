#!/usr/bin/env bash
# Berhenti mengeksekusi jika ada error
set -o errexit

echo "📦 Memulai Build Render untuk Absensi Samsat..."

echo "1. Install Node.js dependencies..."
npm install

echo "2. Build Vite (Frontend assets)..."
npm run build

echo "3. Install Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "4. Membersihkan semua cache lama..."
php artisan optimize:clear

echo "5. Membuat cache baru untuk performa produksi..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "6. Menjalankan Database Migrations ke TiDB..."
# PENTING: Gunakan --force agar tidak ada prompt konfirmasi (yes/no) saat production
php artisan migrate --force

echo "✅ Build Selesai!"
