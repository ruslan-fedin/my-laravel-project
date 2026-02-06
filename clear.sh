#!/bin/bash

echo "🚀 Начинаю полную очистку кэша Laravel Sail..."

./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan clear-compiled

echo "✨ Очистка завершена! Проверьте страницу архива."
